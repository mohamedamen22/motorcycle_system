<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No case ID provided";
    header("Location: index.php");
    exit();
}

$case_id = (int)$_GET['id'];

$stmt = $pdo->prepare("UPDATE student_cases SET status = 'closed' WHERE case_id = ?");

if ($stmt->execute([$case_id])) {
    $_SESSION['message'] = "Case closed successfully!";
} else {
    $_SESSION['error'] = "Failed to close case";
}

header("Location: view_case.php?id=" . $case_id);
exit();
?>