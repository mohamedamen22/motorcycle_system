<?php
$servername = "localhost"; // Haddii aad isticmaaleyso localhost
$username = "root"; // Username kaaga
$password = ""; // Haddii aanad lahayn password
$dbname = "attendancemsystem"; // Magaca database-kaaga

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
