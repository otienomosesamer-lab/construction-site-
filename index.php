<?php
// Entry point: send logged-in users to the dashboard, everyone else to login.
session_start();
header('Location: ' . (empty($_SESSION['user_id']) ? 'login.php' : 'dashboard.php'));
exit;
