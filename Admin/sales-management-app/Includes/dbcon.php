<?php
// Database connection settings
$host = 'localhost'; // Database host
$username = 'root'; // Database username
$password = ''; // Database password
$database = 'sales_management'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>