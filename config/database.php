<?php
/**
 * Construction Site Materials & Equipment Tracking System
 * Database connection (PDO + MySQL) - XAMPP defaults
 * Proposal 3.3.4: PHP back end, MySQL database, VS Code
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'construction_tracking');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default: no password. Change in production.

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
