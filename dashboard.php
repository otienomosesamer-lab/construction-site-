<?php
$title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
$db = getDB();
$stats = [
    'materials' => $db->query("SELECT COUNT(*) c FROM materials")->fetch()['c'],
    'low_stock' => $db->query("SELECT COUNT(*) c FROM materials WHERE current_stock <= reorder_level")->fetch()['c'],
    'equipment' => $db->query("SELECT COUNT(*) c FROM equipment")->fetch()['c'],
    'available' => $db->query("SELECT COUNT(*) c FROM equipment WHERE status='available'")->fetch()['c'],
    'maintenance' => $db->query("SELECT COUNT(*) c FROM equipment WHERE status='under_maintenance'")->fetch()['c'],
];
$low = $db->query("SELECT * FROM materials WHERE current_stock <= reorder_level ORDER BY current_stock ASC")->fetchAll();
$recent = $db->query("SELECT t.*, m.name AS material, u.full_name AS by_user FROM material_transactions t JOIN materials m ON m.id=t.material_id LEFT JOIN users u ON u.id=t.recorded_by ORDER BY t.created_at DESC LIMIT 8")->fetchAll();
$equip = $db->query("SELECT * FROM equipment ORDER BY updated_at DESC LIMIT 8")->fetchAll();
?>
<h1>Dashboard</h1><p>Welcome back, <?= e($user['full_name']) ?>. Here is the current position at the site.</p>
<div class="cards">
<div class="card"><b><?= (int)$stats['materials'] ?></b><span>Materials tracked</span></div>
<div class="card <?= $stats['low_stock']?'warn':'' ?>"><b><?= (int)$stats['low_stock'] ?></b><span>Materials at / below reorder level</span></div>
<div class="card"><b><?= (int)$stats['equipment'] ?></b><span>Equipment items</span></div>
<div class="card"><b><?= (int)$stats['available'] ?></b><span>Equipment available</span></div>
<div class="card"><b><?= (int)$stats['maintenance'] ?></b><span>Under maintenance</span></div>
</div>
<h2>⚠️ Low stock — reorder soon</h2>
<?php if (!$low): ?><p>All materials are above their reorder level.</p><?php else: ?>
<table><tr><th>Material</th><th>Stock</th><th>Reorder level</th><th>Unit</th></tr>
<?php foreach ($low as $r): ?><tr><td><?= e($r['name']) ?></td><td><b><?= (int)$r['current_stock'] ?></b></td><td><?= (int)$r['reorder_level'] ?></td><td><?= e($r['unit']) ?></td></tr><?php endforeach; ?></table><?php endif; ?>
<h2>Recent material movements</h2>
<table><tr><th>Date</th><th>Material</th><th>Type</th><th>Qty</th><th>Recorded by</th></tr>
<?php foreach ($recent as $r): ?><tr><td><?= e($r['transaction_date']) ?></td><td><?= e($r['material']) ?></td><td><?= e(ucfirst($r['type'])) ?></td><td><?= (int)$r['quantity'] ?></td><td><?= e($r['by_user'] ?? '-') ?></td></tr><?php endforeach; ?></table>
<h2>Equipment — latest updates</h2>
<table><tr><th>Equipment</th><th>Serial</th><th>Status</th><th>Location</th></tr>
<?php foreach ($equip as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['serial_number']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['location']) ?></td></tr><?php endforeach; ?></table>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
