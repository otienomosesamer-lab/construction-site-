<?php
/**
 * ONE-TIME SETUP — run once in the browser: http://localhost/construction-tracking-system/setup.php
 * 1) Creates the database if missing, 2) runs database.sql, 3) creates demo users
 *    with password_hash() (so no plain-text passwords are stored).
 * Delete this file after setup.
 */
require_once __DIR__ . '/config/database.php';
$pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4");
$pdo->exec("USE `" . DB_NAME . "`");
$pdo->exec(file_get_contents(__DIR__ . '/database.sql'));
$users = [
    ['Moses Ochieng — Project Manager', 'pm@site.local', 'Pass@123', 'project_manager'],
    ['Amina Storekeeper', 'store@site.local', 'Pass@123', 'storekeeper'],
    ['Brian Equipment Manager', 'equip@site.local', 'Pass@123', 'equipment_manager'],
];
$stmt = $pdo->prepare("INSERT IGNORE INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)");
foreach ($users as [$name, $email, $pass, $role]) {
    $stmt->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
}
echo "<h2>Setup complete ✅</h2><p>Database <b>" . DB_NAME . "</b> created, tables + sample data loaded, and 3 demo users created.</p><p>Log in with <b>pm@site.local / Pass@123</b> (or store@ / equip@).</p><p><b>Now delete setup.php</b>, then go to <a href='login.php'>login.php</a>.</p>";
