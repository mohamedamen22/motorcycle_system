<?php
include '../Includes/dbcon.php';
include 'includes/header.php';

// Check teacher session
if(!isset($_SESSION['userId']) || $_SESSION['userType'] != 'ClassTeacher') {
    header("Location: login.php");
    exit();
}

$teacherId = $_SESSION['userId'];
$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

// Get assigned subjects
$subjectsQuery = "SELECT s.* FROM tblsubjects s 
                 JOIN tblteacher_subjects ts ON s.Id = ts.subjectId
                 WHERE ts.teacherId = $teacherId AND ts.classId = $classId AND ts.classArmId = $classArmId";
$subjectsResult = $conn->query($subjectsQuery);

// Get upcoming exams
$examsQuery = "SELECT e.*, s.subjectName FROM tblexams e
              JOIN tblsubjects s ON e.subjectId = s.Id
              WHERE e.teacherId = $teacherId AND e.classId = $classId AND e.classArmId = $classArmId
              AND e.examDate >= CURDATE()
              ORDER BY e.examDate ASC LIMIT 5";
$examsResult = $conn->query($examsQuery);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Teacher Dashboard</h1>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Assigned Subjects</h5>
                            <p class="card-text display-4"><?= $subjectsResult->num_rows; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Upcoming Exams</h5>
                            <p class="card-text display-4"><?= $examsResult->num_rows; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Students</h5>
                            <?php
                            $studentsQuery = "SELECT COUNT(*) as total FROM tblstudents 
                                            WHERE classId = $classId AND classArmId = $classArmId";
                            $studentsCount = $conn->query($studentsQuery)->fetch_assoc()['total'];
                            ?>
                            <p class="card-text display-4"><?= $studentsCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Exams -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Upcoming Exams</h5>
                </div>
                <div class="card-body">
                    <?php if($examsResult->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Total Marks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($exam = $examsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($exam['examName']); ?></td>
                                        <td><?= htmlspecialchars($exam['subjectName']); ?></td>
                                        <td><?= htmlspecialchars($exam['examType']); ?></td>
                                        <td><?= date('M j, Y', strtotime($exam['examDate'])); ?></td>
                                        <td><?= $exam['totalMarks']; ?></td>
                                        <td>
                                            <a href="enter_results.php?examId=<?= $exam['Id']; ?>" class="btn btn-sm btn-primary">Enter Results</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No upcoming exams found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assigned Subjects -->
            <div class="card">
                <div class="card-header">
                    <h5>Your Assigned Subjects</h5>
                </div>
                <div class="card-body">
                    <?php if($subjectsResult->num_rows > 0): ?>
                        <div class="row">
                            <?php while($subject = $subjectsResult->fetch_assoc()): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($subject['subjectName']); ?></h5>
                                        <p class="card-text">Code: <?= htmlspecialchars($subject['subjectCode']); ?></p>
                                        <a href="submit_exam.php?subjectId=<?= $subject['Id']; ?>" class="btn btn-sm btn-success">Create Exam</a>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No subjects assigned to you.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>