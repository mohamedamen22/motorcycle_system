<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

// Check if case ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No case ID provided";
    header("Location: index.php");
    exit();
}

$case_id = (int)$_GET['id'];

// Fetch case details
$stmt = $pdo->prepare("SELECT * FROM student_cases WHERE case_id = ?");
$stmt->execute([$case_id]);
$case = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$case) {
    $_SESSION['error'] = "Case not found";
    header("Location: index.php");
    exit();
}

// Fetch student details if you have a students table
// $student_stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
// $student_stmt->execute([$case['student_id']]);
// $student = $student_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Case - <?= htmlspecialchars($case['case_type']) ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Case Details</h1>
        </div>
    </div>

    <div class="navbar">
        <div class="container">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="add_case.php"><i class="fas fa-plus"></i> Add New Case</a>
            <a href="edit_case.php?id=<?= $case_id ?>"><i class="fas fa-edit"></i> Edit Case</a>
            
        </div>
    </div>

    <div class="container">
        <?php displayMessage(); ?>

        <div class="card">
            <div class="card-header">
                <h2><?= htmlspecialchars($case['case_type']) ?> 
                    <span class="severity-<?= $case['severity'] ?>">
                        (<?= ucfirst($case['severity']) ?>)
                    </span>
                </h2>
                <span class="badge badge-<?= $case['status'] ?>">
                    <?= ucfirst($case['status']) ?>
                </span>
            </div>

            <div class="case-details">
                <div class="detail-row">
                    <div class="detail-group">
                        <h3><i class="fas fa-user"></i> Student Information</h3>
                        <p><strong>ID:</strong> <?= $case['student_id'] ?></p>
                        <p><strong>Name:</strong> <?= htmlspecialchars($case['student_FullName']) ?></p>
                        <p><strong>Parent Contact:</strong> <?= htmlspecialchars($case['parent_contact']) ?></p>
                    </div>

                    <div class="detail-group">
                        <h3><i class="fas fa-calendar-alt"></i> Case Timing</h3>
                        <p><strong>Date:</strong> <?= date('F j, Y', strtotime($case['case_date'])) ?></p>
                        <p><strong>Day:</strong> <?= $case['case_day'] ?></p>
                        <p><strong>Time:</strong> <?= $case['case_time'] ? date('h:i A', strtotime($case['case_time'])) : 'N/A' ?></p>
                    </div>
                </div>

                <div class="detail-group">
                    <h3><i class="fas fa-file-alt"></i> Description</h3>
                    <p><?= nl2br(htmlspecialchars($case['description'])) ?></p>
                </div>

                <div class="detail-group">
                    <h3><i class="fas fa-tasks"></i> Action Taken</h3>
                    <p><?= $case['action_taken'] ? nl2br(htmlspecialchars($case['action_taken'])) : 'No action recorded' ?></p>
                </div>

                <?php if ($case['status'] === 'resolved' || $case['status'] === 'closed'): ?>
                <div class="detail-group">
                    <h3><i class="fas fa-check-circle"></i> Resolution</h3>
                    <p><strong>Resolved Date:</strong> <?= $case['resolved_date'] ? date('F j, Y', strtotime($case['resolved_date'])) : 'N/A' ?></p>
                    <p><strong>Resolved By:</strong> <?= $case['resolved_by'] ? getUserName($pdo, $case['resolved_by']) : 'N/A' ?></p>
                    
                </div>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
    <a href="edit_case.php?id=<?= $case_id ?>" class="btn"><i class="fas fa-edit"></i> Edit Case</a>
    <a href="index.php" class="btn"><i class="fas fa-arrow-left"></i> Back to List</a>
    
    <?php if ($case['status'] === 'open' || $case['status'] === 'investigating'): ?>
        <a href="mark_resolved.php?id=<?= $case_id ?>" 
           class="btn btn-success"
           onclick="return confirm('Are you sure you want to mark this case as resolved?')">
           <p><strong>Resolved By:</strong> <?= $case['resolved_by'] ? 'User ID: '.$case['resolved_by'] : 'N/A' ?></p>
        </a>
    <?php elseif ($case['status'] === 'resolved'): ?>
        <a href="mark_closed.php?id=<?= $case_id ?>" 
           class="btn btn-success"
           onclick="return confirm('Close this case permanently?')">
            <i class="fas fa-lock"></i> Close Case
            <a href="print_case.php?id=<?= $case_id ?>" class="btn" target="_blank">
    <i class="fas fa-print"></i> Print Case Report
</a>

    <?php endif; ?>
</div>

    <script src="script.js"></script>
</body>
</html>