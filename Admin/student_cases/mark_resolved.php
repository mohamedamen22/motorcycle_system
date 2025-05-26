<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

// Check if user is logged in and has permission
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No case ID provided";
    header("Location: index.php");
    exit();
}

$case_id = (int)$_GET['id'];

// Update the case status
$stmt = $pdo->prepare("UPDATE student_cases 
                      SET status = 'resolved', 
                          resolved_date = CURDATE(), 
                          resolved_by = ? 
                      WHERE case_id = ?");

// Use $_SESSION['user_id'] if you have user authentication
$resolved_by = 1; // Replace with $_SESSION['user_id'] in real implementation

if ($stmt->execute([$resolved_by, $case_id])) {
    $_SESSION['message'] = "Case marked as resolved successfully!";
} else {
    $_SESSION['error'] = "Failed to update case status";
}

header("Location: view_case.php?id=" . $case_id);
exit();
?>