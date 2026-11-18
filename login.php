<?php
require_once __DIR__ . '/includes/auth.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = getDB()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user'] = ['id'=>$u['id'],'full_name'=>$u['full_name'],'email'=>$u['email'],'role'=>$u['role']];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid email or password. Try a demo login below.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | SiteTrack</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="login-wrap"><h1>🏗️ SiteTrack</h1><p>Construction Site Materials &amp; Equipment Tracking</p>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label>Email</label><input type="email" name="email" required placeholder="pm@site.local">
<label>Password</label><input type="password" name="password" required placeholder="Pass@123">
<br><br><button class="btn" style="width:100%">Login</button></form>
<p style="font-size:13px;background:#fef3c7;padding:12px;border-radius:8px">
<strong>Demo accounts (created by setup.php):</strong><br>
Project Manager — pm@site.local<br>Storekeeper — store@site.local<br>Equipment Manager — equip@site.local<br>Password for all: <code>Pass@123</code></p>
<p style="font-size:12px;color:#6b7280">First run? Open <code>setup.php</code> once in your browser to create the database tables and demo users, then delete it.</p>
</div></body></html>
