<?php
// Module 2 — Material Management (Objective 2): record received / issued, monitor stock.
// Storekeeper + Project Manager can record; Equipment Manager can view.
$title = 'Materials & Stock';
require_once __DIR__ . '/includes/header.php';
$db = getDB(); $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(current_role(), ['project_manager','storekeeper'], true)) {
    if (isset($_POST['add_material'])) {
        $s = $db->prepare("INSERT INTO materials (name, category, unit, unit_price, reorder_level, current_stock) VALUES (?,?,?,?,?,?)");
        $s->execute([trim($_POST['name']), trim($_POST['category']), trim($_POST['unit']), (float)$_POST['unit_price'], (int)$_POST['reorder_level'], (int)$_POST['current_stock']]);
        flash('Material added.');
    }
    if (isset($_POST['record_tx'])) {
        $mid = (int)$_POST['material_id']; $qty = (int)$_POST['quantity']; $type = $_POST['type'];
        if ($qty <= 0) { $msg = 'Quantity must be more than 0.'; }
        else {
            $db->beginTransaction();
            $db->prepare("INSERT INTO material_transactions (material_id,type,quantity,issued_to,supplier,reference_no,transaction_date,recorded_by,notes) VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$mid, $type, $qty, $_POST['issued_to'] ?: null, $_POST['supplier'] ?: null, $_POST['reference_no'] ?: null, $_POST['transaction_date'], $_SESSION['user_id'], $_POST['notes'] ?? '']);
            $delta = $type === 'received' ? $qty : -$qty;
            $db->prepare("UPDATE materials SET current_stock = GREATEST(0, current_stock + ?) WHERE id=?")->execute([$delta, $mid]);
            $db->commit(); flash('Transaction recorded and stock updated.');
            header('Location: materials.php'); exit;
        }
    }
}
$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM materials " . ($search ? "WHERE name LIKE ? OR category LIKE ? " : "") . "ORDER BY name";
$stmt = $db->prepare($sql); $stmt->execute($search ? ["%$search%","%$search%"] : []);
$materials = $stmt->fetchAll();
$tx = $db->query("SELECT t.*, m.name AS material FROM material_transactions t JOIN materials m ON m.id=t.material_id ORDER BY t.created_at DESC LIMIT 15")->fetchAll();
$can_edit = in_array(current_role(), ['project_manager','storekeeper'], true);
?>
<h1>Materials &amp; Stock</h1>
<p>Record materials received and issued. Stock updates automatically — issuing never drops stock below zero.</p>
<?php if ($msg): ?><div class="error"><?= e($msg) ?></div><?php endif; ?>
<form method="get" style="display:flex;gap:8px"><input name="q" value="<?= e($search) ?>" placeholder="Search materials…" oninput="this.form.submit()" style="max-width:300px"></form>
<table id="mt"><tr><th>Material</th><th>Category</th><th>Stock</th><th>Unit</th><th>Unit price (KES)</th><th>Reorder</th><th>Status</th></tr>
<?php foreach ($materials as $m): $low = $m['current_stock'] <= $m['reorder_level']; ?>
<tr><td><?= e($m['name']) ?></td><td><?= e($m['category']) ?></td><td><b><?= (int)$m['current_stock'] ?></b></td><td><?= e($m['unit']) ?></td><td><?= number_format($m['unit_price'],2) ?></td><td><?= (int)$m['reorder_level'] ?></td><td><?= $low ? '<span class="badge red">LOW STOCK</span>' : '<span class="badge green">OK</span>' ?></td></tr>
<?php endforeach; ?></table>

<?php if ($can_edit): ?>
<div class="grid2">
<form class="box" method="post"><h2>Add new material</h2>
<input type="hidden" name="add_material" value="1">
<label>Name</label><input name="name" required placeholder="e.g. Cement (50kg bag)">
<label>Category</label><input name="category" required placeholder="Cement / Steel / Timber">
<label>Unit</label><input name="unit" required placeholder="bags / tonnes / pieces">
<label>Unit price (KES)</label><input type="number" step="0.01" min="0" name="unit_price" required>
<label>Reorder level</label><input type="number" min="0" name="reorder_level" required>
<label>Opening stock</label><input type="number" min="0" name="current_stock" required>
<br><br><button class="btn">Add material</button></form>

<form class="box" method="post"><h2>Record received / issued</h2>
<input type="hidden" name="record_tx" value="1">
<label>Material</label><select name="material_id" required><?php foreach ($materials as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?> — stock <?= (int)$m['current_stock'] ?></option><?php endforeach; ?></select>
<label>Type</label><select name="type"><option value="received">Received (stock in)</option><option value="issued">Issued (stock out)</option></select>
<label>Quantity</label><input type="number" min="1" name="quantity" required>
<label>Date</label><input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required>
<label>Issued to / Supplier</label><input name="issued_to" placeholder="Issued to: Site Team A"><input name="supplier" placeholder="Supplier (for receipts)" style="margin-top:6px">
<label>Reference no.</label><input name="reference_no" placeholder="Delivery note / requisition no.">
<label>Notes</label><textarea name="notes" rows="2"></textarea>
<br><button class="btn">Save transaction</button></form>
</div>
<?php else: ?><p><em>Your role can view materials. Only the Storekeeper and Project Manager can record transactions.</em></p><?php endif; ?>

<h2>Recent transactions</h2>
<table><tr><th>Date</th><th>Material</th><th>Type</th><th>Qty</th><th>Issued to / Supplier</th><th>Ref</th></tr>
<?php foreach ($tx as $t): ?><tr><td><?= e($t['transaction_date']) ?></td><td><?= e($t['material']) ?></td><td><?= e(ucfirst($t['type'])) ?></td><td><?= (int)$t['quantity'] ?></td><td><?= e($t['issued_to'] ?: $t['supplier'] ?: '-') ?></td><td><?= e($t['reference_no'] ?: '-') ?></td></tr><?php endforeach; ?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
