<?php
// Database configuration for XAMPP
$db_host = "localhost";
$db_user = "root";
$db_pass = "";  // Default XAMPP password is empty
$db_name = "motorcycle_parts_db";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8mb4");
?>