<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_attendance');

// Site configuration
define('SITE_NAME', 'Nidaamka Soo Habsanada Iskuulka');
define('SITE_LANG', 'so');
date_default_timezone_set('Africa/Mogadishu');

// Create database connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Xiriirka database-ka khalad ayaa dhacay: " . $e->getMessage());
}

// Session start
session_start();
?>