<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $student_id = (int)$_POST['student_id'];
    $student_FullName = trim($_POST['student_FullName']);
    $parent_contact = trim($_POST['parent_contact']);
    $case_date = $_POST['case_date'];
    $case_day = date('l', strtotime($case_date));
    $case_time = $_POST['case_time'];
    $case_type = trim($_POST['case_type']);
    $severity = $_POST['severity'];
    $description = trim($_POST['description']);
    $action_taken = trim($_POST['action_taken']);
    $status = $_POST['status'];

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO student_cases 
                          (student_id, student_FullName, parent_contact, case_date, case_day, case_time, 
                          case_type, severity, description, action_taken, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([
        $student_id, $student_FullName, $parent_contact, $case_date, $case_day, $case_time,
        $case_type, $severity, $description, $action_taken, $status
    ])) {
        $_SESSION['message'] = "Case added successfully!";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding case. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Case</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Add New Student Case</h1>
        </div>
    </div>

    <div class="navbar">
        <div class="container">
            <a href="index.php">Home</a>
            <a href="add_case.php">Add New Case</a>
        </div>
    </div>

    <div class="container">
        <?php displayMessage(); ?>

        <div class="card">
            <form method="post" action="add_case.php">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div class="form-group">
                            <label for="student_id">Student ID *</label>
                            <input type="number" id="student_id" name="student_id" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="student_FullName">Student Full Name *</label>
                            <input type="text" id="student_FullName" name="student_FullName" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="parent_contact">Parent Contact</label>
                            <input type="text" id="parent_contact" name="parent_contact" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="case_date">Case Date *</label>
                            <input type="date" id="case_date" name="case_date" class="form-control datepicker" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="case_time">Case Time</label>
                            <input type="time" id="case_time" name="case_time" class="form-control timepicker">
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label for="case_type">Case Type *</label>
                            <input type="text" id="case_type" name="case_type" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="severity">Severity *</label>
                            <select id="severity" name="severity" class="form-control" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="open" selected>Open</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" class="form-control" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="action_taken">Action Taken</label>
                            <textarea id="action_taken" name="action_taken" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-success">Save Case</button>
                    <a href="index.php" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.js