<?php
// Module 5 — Reports & Dashboard (Objective 4): stock, received/issued, equipment, maintenance.
$title = 'Reports';
require_once __DIR__ . '/includes/header.php';
$db = getDB();
$from = $_GET['from'] ?? date('Y-m-01'); $to = $_GET['to'] ?? date('Y-m-d');
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="stock-report.csv"');
    $out = fopen('php://output','w'); fputcsv($out, ['Material','Category','Stock','Unit','Unit Price (KES)','Stock Value (KES)','Reorder Level','Status']);
    foreach ($db->query("SELECT * FROM materials ORDER BY name") as $m) {
        fputcsv($out, [$m['name'],$m['category'],$m['current_stock'],$m['unit'],$m['unit_price'],$m['current_stock']*$m['unit_price'],$m['reorder_level'], $m['current_stock']<=$m['reorder_level']?'LOW':'OK']);
    }
    exit;
}
$stock = $db->query("SELECT *, (current_stock*unit_price) AS value FROM materials ORDER BY name")->fetchAll();
$tx = $db->prepare("SELECT t.*, m.name AS material FROM material_transactions t JOIN materials m ON m.id=t.material_id WHERE transaction_date BETWEEN ? AND ? ORDER BY transaction_date DESC");
$tx->execute([$from,$to]); $tx = $tx->fetchAll();
$equip = $db->query("SELECT * FROM equipment ORDER BY name")->fetchAll();
$maint = $db->query("SELECT mr.*, e.name AS equip FROM maintenance_records mr JOIN equipment e ON e.id=mr.equipment_id ORDER BY mr.maintenance_date DESC")->fetchAll();
$total_value = array_sum(array_column($stock, 'value'));
?>
<h1>Reports</h1>
<p>Reports for the Project Manager and site staff. Use the date filter for movements, or export the stock report as CSV.</p>
<form method="get" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
<div><label>From</label><input type="date" name="from" value="<?= e($from) ?>"></div>
<div><label>To</label><input type="date" name="to" value="<?= e($to) ?>"></div>
<div><button class="btn">Filter</button> <a class="btn secondary" href="reports.php?export=csv" style="padding:10px 16px">⬇ Export stock CSV</a> <button type="button" class="btn secondary" onclick="window.print()">🖨 Print</button></div></form>

<h2>Available material stock — total value KES <?= number_format($total_value,2) ?></h2>
<table><tr><th>Material</th><th>Stock</th><th>Unit price</th><th>Stock value</th><th>Status</th></tr>
<?php foreach ($stock as $m): ?><tr><td><?= e($m['name']) ?></td><td><?= (int)$m['current_stock'] ?> <?= e($m['unit']) ?></td><td><?= number_format($m['unit_price'],2) ?></td><td><?= number_format($m['value'],2) ?></td><td><?= $m['current_stock']<=$m['reorder_level']?'<span class="badge red">LOW</span>':'<span class="badge green">OK</span>' ?></td></tr><?php endforeach; ?></table>

<h2>Material received / issued (<?= e($from) ?> to <?= e($to) ?>)</h2>
<table><tr><th>Date</th><th>Material</th><th>Type</th><th>Qty</th><th>Issued to / Supplier</th></tr>
<?php foreach ($tx as $t): ?><tr><td><?= e($t['transaction_date']) ?></td><td><?= e($t['material']) ?></td><td><?= e(ucfirst($t['type'])) ?></td><td><?= (int)$t['quantity'] ?></td><td><?= e($t['issued_to'] ?: $t['supplier'] ?: '-') ?></td></tr><?php endforeach; ?></table>

<h2>Equipment location &amp; assignment</h2>
<table><tr><th>Equipment</th><th>Serial</th><th>Status</th><th>Location</th><th>Assigned to</th></tr>
<?php foreach ($equip as $e): ?><tr><td><?= e($e['name']) ?></td><td><?= e($e['serial_number']) ?></td><td><?= e($e['status']) ?></td><td><?= e($e['location']) ?></td><td><?= e($e['assigned_to'] ?: '-') ?></td></tr><?php endforeach; ?></table>

<h2>Equipment maintenance history</h2>
<table><tr><th>Date</th><th>Equipment</th><th>Type</th><th>Description</th><th>Cost (KES)</th><th>Next due</th></tr>
<?php foreach ($maint as $r): ?><tr><td><?= e($r['maintenance_date']) ?></td><td><?= e($r['equip']) ?></td><td><?= e($r['maintenance_type']) ?></td><td><?= e($r['description']) ?></td><td><?= number_format($r['cost'],2) ?></td><td><?= e($r['next_due_date'] ?: '-') ?></td></tr><?php endforeach; ?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
