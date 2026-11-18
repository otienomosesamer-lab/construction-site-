<?php require_once __DIR__ . '/auth.php'; require_login(); $user = current_user(); $role = current_role(); $page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Construction Tracking') ?> | SiteTrack</title>
<link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<header class="topbar"><div class="brand">🏗️ SiteTrack <span>Construction Materials &amp; Equipment Tracking</span></div>
<div class="userbox"><?= e($user['full_name']) ?> · <?= e(role_name($role)) ?> &nbsp;|&nbsp; <a href="logout.php">Logout</a></div></header>
<div class="layout">
<nav class="sidebar">
<a href="dashboard.php" class="<?= $page=='dashboard.php'?'active':'' ?>">📊 Dashboard</a>
<a href="materials.php" class="<?= $page=='materials.php'?'active':'' ?>">🧱 Materials &amp; Stock</a>
<a href="equipment.php" class="<?= $page=='equipment.php'?'active':'' ?>">🚜 Equipment</a>
<a href="maintenance.php" class="<?= $page=='maintenance.php'?'active':'' ?>">🔧 Maintenance</a>
<a href="reports.php" class="<?= $page=='reports.php'?'active':'' ?>">📄 Reports</a>
<?php if ($role==='project_manager'): ?><a href="users.php" class="<?= $page=='users.php'?'active':'' ?>">👥 Users</a><?php endif; ?>
<div class="side-note"><strong>Demo logins</strong><br>PM: pm@site.local<br>Store: store@site.local<br>Equip: equip@site.local<br>Password for all: <code>Pass@123</code><br><small>Change after setup.php</small></div>
</nav>
<main class="content">
<?php if ($m = flash()): ?><div class="flash"><?= e($m) ?></div><?php endif; ?>
