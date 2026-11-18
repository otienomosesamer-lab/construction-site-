<?php
// Module 1 — Login & User Management. Project Manager only.
$title = 'Users';
require_once __DIR__ . '/includes/header.php';
require_role(['project_manager']);
$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $db->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)")
       ->execute([trim($_POST['full_name']), trim($_POST['email']), password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['role']]);
    flash('User created.');
}
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM users WHERE id=? AND id<>?")->execute([(int)$_GET['delete'], $_SESSION['user_id']]);
    flash('User deleted.'); header('Location: users.php'); exit;
}
$users = $db->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY full_name")->fetchAll();
?>
<h1>Users</h1><p>Only the Project Manager can add or remove users. Passwords are stored hashed with <code>password_hash()</code>.</p>
<table><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th></th></tr>
<?php foreach ($users as $u): ?><tr><td><?= e($u['full_name']) ?></td><td><?= e($u['email']) ?></td><td><?= e(role_name($u['role'])) ?></td><td><?= e($u['created_at']) ?></td>
<td><?php if ($u['id'] != $_SESSION['user_id']): ?><a href="users.php?delete=<?= (int)$u['id'] ?>" data-confirm="Delete this user?" style="color:#dc2626">Delete</a><?php endif; ?></td></tr><?php endforeach; ?></table>
<form class="box" method="post"><h2>Add user</h2><input type="hidden" name="add_user" value="1">
<label>Full name</label><input name="full_name" required>
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required minlength="6">
<label>Role</label><select name="role"><option value="project_manager">Project Manager</option><option value="storekeeper">Storekeeper</option><option value="equipment_manager">Equipment Manager</option></select>
<br><br><button class="btn">Create user</button></form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
