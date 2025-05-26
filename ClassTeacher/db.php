<?php
$servername = "localhost"; // MySQL server address
$username = "root"; // Your MySQL username
$password = ""; // MySQL password (usually empty for XAMPP)
$dbname = "attendancemsystem"; // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>
