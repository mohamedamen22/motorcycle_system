<?php
// Include the database connection file
include('db.php');

// Ensure the necessary GET parameters are present
if (isset($_GET['date'], $_GET['classId'], $_GET['classArmId'])) {
    $date = $_GET['date'];
    $classId = $_GET['classId'];
    $classArmId = $_GET['classArmId'];

    // Prepare SQL query to fetch attendance data for the given date, class, and class arm
    $sql = "SELECT * FROM attendance WHERE date = ? AND class_id = ? AND class_arm_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $date, $classId, $classArmId); // 's' for string, 'i' for integer

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any data is returned
    if ($result->num_rows > 0) {
        $attendanceData = [];
        while ($row = $result->fetch_assoc()) {
            $attendanceData[] = $row;
        }
    } else {
        $attendanceData = [];
    }
} else {
    die("Missing required parameters.");
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="your-stylesheet.css">
</head>
<body>
    <div class="container">
        <h1>Attendance for Date: <?php echo $date; ?> (Class: <?php echo $classId; ?>, Class Arm: <?php echo $classArmId; ?>)</h1>

        <?php if (!empty($attendanceData)): ?>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Attendance Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceData as $attendance): ?>
                        <tr>
                            <td><?php echo $attendance['student_name']; ?></td>
                            <td><?php echo $attendance['status'] == 1 ? 'Present' : 'Absent'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance data available for this date and class.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>
