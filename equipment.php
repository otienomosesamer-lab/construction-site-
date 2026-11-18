<?php
// Module 3 — Equipment Management (Objective 3): details, assignment, location, availability.
$title = 'Equipment';
require_once __DIR__ . '/includes/header.php';
$db = getDB();
$can_edit = in_array(current_role(), ['project_manager','equipment_manager'], true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_edit) {
    if (isset($_POST['add_equipment'])) {
        $s = $db->prepare("INSERT INTO equipment (name, category, serial_number, status, location) VALUES (?,?,?,?,?)");
        $s->execute([trim($_POST['name']), trim($_POST['category']), trim($_POST['serial_number']), $_POST['status'], trim($_POST['location'])]);
        flash('Equipment added.');
    }
    if (isset($_POST['assign'])) {
        $id = (int)$_POST['equipment_id'];
        $db->beginTransaction();
        $db->prepare("INSERT INTO equipment_assignments (equipment_id, assigned_to, location, assigned_date) VALUES (?,?,?,?)")
           ->execute([$id, trim($_POST['assigned_to']), trim($_POST['location']), $_POST['assigned_date']]);
        $db->prepare("UPDATE equipment SET status='assigned', assigned_to=?, location=?, assigned_date=? WHERE id=?")
           ->execute([trim($_POST['assigned_to']), trim($_POST['location']), $_POST['assigned_date'], $id]);
        $db->commit(); flash('Equipment assigned.');
    }
    if (isset($_POST['return'])) {
        $id = (int)$_POST['equipment_id'];
        $db->beginTransaction();
        $db->prepare("UPDATE equipment_assignments SET status='returned', returned_date=CURDATE() WHERE equipment_id=? AND status='active'")->execute([$id]);
        $db->prepare("UPDATE equipment SET status='available', assigned_to=NULL, assigned_date=NULL, location=? WHERE id=?")->execute([trim($_POST['location']), $id]);
        $db->commit(); flash('Equipment returned to store.');
    }
    header('Location: equipment.php'); exit;
}
$filter = $_GET['status'] ?? '';
$stmt = $db->prepare("SELECT * FROM equipment " . ($filter ? "WHERE status=? " : "") . "ORDER BY name");
$stmt->execute($filter ? [$filter] : []);
$items = $stmt->fetchAll();
?>
<h1>Equipment</h1><p>Track equipment details, assignment, location and availability.</p>
<form method="get"><select name="status" onchange="this.form.submit()"><option value="">All statuses</option>
<?php foreach (['available','assigned','under_maintenance','out_of_service'] as $s): ?><option value="<?= $s ?>" <?= $filter===$s?'selected':'' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option><?php endforeach; ?></select></form>
<table><tr><th>Equipment</th><th>Serial</th><th>Status</th><th>Location</th><th>Assigned to</th><th>Action</th></tr>
<?php foreach ($items as $it): ?><tr><td><?= e($it['name']) ?><br><small><?= e($it['category']) ?></small></td><td><?= e($it['serial_number']) ?></td>
<td><span class="badge <?= $it['status']==='available'?'green':($it['status']==='under_maintenance'?'red':'amber') ?>"><?= e(str_replace('_',' ',$it['status'])) ?></span></td>
<td><?= e($it['location']) ?></td><td><?= e($it['assigned_to'] ?: '-') ?></td>
<td><?php if ($can_edit && $it['status']==='available'): ?><small>Use Assign form below</small><?php elseif ($can_edit && $it['status']==='assigned'): ?><small>Use Return form below</small><?php else: ?>—<?php endif; ?></td></tr><?php endforeach; ?></table>
<?php if ($can_edit): ?>
<div class="grid2">
<form class="box" method="post"><h2>Add equipment</h2><input type="hidden" name="add_equipment" value="1">
<label>Name</label><input name="name" required placeholder="e.g. Concrete Mixer 350L">
<label>Category</label><input name="category" required placeholder="Mixing / Power / Lifting">
<label>Serial number</label><input name="serial_number" required placeholder="e.g. MX-001">
<label>Status</label><select name="status"><option>available</option><option>under_maintenance</option><option>out_of_service</option></select>
<label>Location</label><input name="location" required value="Main Store"><br><br><button class="btn">Add equipment</button></form>
<form class="box" method="post"><h2>Assign / return equipment</h2>
<label>Equipment</label><select name="equipment_id" required><?php foreach ($items as $it): ?><option value="<?= (int)$it['id'] ?>"><?= e($it['name']) ?> (<?= e($it['status']) ?>)</option><?php endforeach; ?></select>
<label>Assigned to</label><input name="assigned_to" placeholder="Site Team A / Crane Crew">
<label>Location</label><input name="location" placeholder="Block A — Slab" required>
<label>Date</label><input type="date" name="assigned_date" value="<?= date('Y-m-d') ?>">
<br><br><button class="btn" name="assign" value="1">Assign</button>
<button class="btn secondary" name="return" value="1">Return to store</button></form>
</div>
<?php else: ?><p><em>Your role can view equipment. Only the Equipment Manager and Project Manager can assign or add equipment.</em></p><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
