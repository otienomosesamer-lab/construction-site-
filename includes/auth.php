<?php
// Session + role-based access control. Included at the top of every protected page.
session_start();
require_once __DIR__ . '/../config/database.php';

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function current_user() { return $_SESSION['user'] ?? null; }
function current_role() { return $_SESSION['user']['role'] ?? ''; }
function role_name($role) {
    return [
        'project_manager'   => 'Project Manager',
        'storekeeper'       => 'Storekeeper',
        'equipment_manager' => 'Equipment Manager',
    ][$role] ?? $role;
}
// $roles = e.g. ['project_manager','storekeeper']
function require_role(array $roles) {
    require_login();
    if (!in_array(current_role(), $roles, true)) {
        http_response_code(403);
        die('<h2>Access denied</h2><p>Your role (' . htmlspecialchars(role_name(current_role())) . ') cannot access this page.</p><p><a href="dashboard.php">Back to Dashboard</a></p>');
    }
}
function flash($msg = null) {
    if ($msg !== null) { $_SESSION['flash'] = $msg; return; }
    $m = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $m;
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
