<?php
// Module 4 — Maintenance Management (Objective 3): history, next due date, cost.
$title = 'Maintenance';
require_once __DIR__ . '/includes/header.php';
$db = getDB();
$can_edit = in_array(current_role(), ['project_manager','equipment_manager'], true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_edit && isset($_POST['add_maintenance'])) {
    $db->beginTransaction();
    $db->prepare("INSERT INTO maintenance_records (equipment_id, maintenance_type, description, maintenance_date, cost, performed_by, next_due_date, status) VALUES (?,?,?,?,?,?,?,?)")
       ->execute([(int)$_POST['equipment_id'], $_POST['maintenance_type'], trim($_POST['description']), $_POST['maintenance_date'], (float)$_POST['cost'], trim($_POST['performed_by']), $_POST['next_due_date'] ?: null, $_POST['status']]);
    if ($_POST['status'] === 'in_progress') {
        $db->prepare("UPDATE equipment SET status='under_maintenance' WHERE id=?")->execute([(int)$_POST['equipment_id']]);
    } elseif ($_POST['status'] === 'completed') {
        $db->prepare("UPDATE equipment SET status='available' WHERE id=? AND status='under_maintenance'")->execute([(int)$_POST['equipment_id']]);
    }
    $db->commit(); flash('Maintenance record saved.'); header('Location: maintenance.php'); exit;
}
$equip = $db->query("SELECT id, name, serial_number FROM equipment ORDER BY name")->fetchAll();
$rows = $db->query("SELECT mr.*, e.name AS equip, e.serial_number FROM maintenance_records mr JOIN equipment e ON e.id=mr.equipment_id ORDER BY mr.maintenance_date DESC")->fetchAll();
?>
<h1>Maintenance</h1><p>Record routine service, repairs and inspections — with cost and next due date.</p>
<table><tr><th>Date</th><th>Equipment</th><th>Type</th><th>Description</th><th>Cost (KES)</th><th>Performed by</th><th>Next due</th><th>Status</th></tr>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['maintenance_date']) ?></td><td><?= e($r['equip']) ?><br><small><?= e($r['serial_number']) ?></small></td><td><?= e($r['maintenance_type']) ?></td><td><?= e($r['description']) ?></td><td><?= number_format($r['cost'],2) ?></td><td><?= e($r['performed_by'] ?: '-') ?></td><td><?= e($r['next_due_date'] ?: '-') ?></td><td><?= e($r['status']) ?></td></tr><?php endforeach; ?></table>
<?php if ($can_edit): ?>
<form class="box" method="post"><h2>Add maintenance record</h2><input type="hidden" name="add_maintenance" value="1">
<div class="grid2"><div><label>Equipment</label><select name="equipment_id" required><?php foreach ($equip as $e): ?><option value="<?= (int)$e['id'] ?>"><?= e($e['name']) ?> (<?= e($e['serial_number']) ?>)</option><?php endforeach; ?></select>
<label>Type</label><select name="maintenance_type"><option>routine</option><option>repair</option><option>inspection</option></select>
<label>Date</label><input type="date" name="maintenance_date" value="<?= date('Y-m-d') ?>" required>
<label>Status</label><select name="status"><option>completed</option><option>scheduled</option><option>in_progress</option></select></div>
<div><label>Description</label><textarea name="description" rows="3" required placeholder="e.g. Oil change, filters, hydraulic hose replacement"></textarea>
<label>Cost (KES)</label><input type="number" step="0.01" min="0" name="cost" value="0">
<label>Performed by</label><input name="performed_by" placeholder="Technician / manager name">
<label>Next due date</label><input type="date" name="next_due_date"></div></div>
<br><button class="btn">Save maintenance record</button></form>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
