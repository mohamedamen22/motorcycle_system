<?php
require_once 'db_connect.php';
require_once 'includes/functions.php';

// Query to get students with most cases
$query = "SELECT 
            student_id, 
            student_FullName, 
            COUNT(*) as case_count,
            MAX(case_date) as last_case_date
          FROM student_cases 
          GROUP BY student_id, student_FullName
          ORDER BY case_count DESC, last_case_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students with Most Cases</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Students with Most Cases</h1>
        </div>
    </div>

    <div class="navbar">
        <div class="container">
            <a href="index.php">All Cases</a>
            <a href="add_case.php">Add New Case</a>
            <a href="../index.php">Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <?php displayMessage(); ?>

        <div class="card">
            <div class="card-header">
                <h2>Students by Case Count</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Total Cases</th>
                        <th>Last Case Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No student cases found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $student['student_id'] ?></td>
                                <td><?= htmlspecialchars($student['student_FullName']) ?></td>
                                <td><?= $student['case_count'] ?></td>
                                <td><?= date('M j, Y', strtotime($student['last_case_date'])) ?></td>
                                <td>
                                    <a href="index.php?student_id=<?= $student['student_id'] ?>" class="btn">
                                        View All Cases
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>