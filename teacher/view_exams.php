<?php
include '../Includes/dbcon.php';
include 'includes/header.php';

if(!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$teacherId = $_SESSION['userId'];
$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

$examsQuery = "SELECT e.*, s.subjectName FROM tblexams e
              JOIN tblsubjects s ON e.subjectId = s.Id
              WHERE e.teacherId = $teacherId AND e.classId = $classId AND e.classArmId = $classArmId
              ORDER BY e.examDate DESC";
$examsResult = $conn->query($examsQuery);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Your Exams</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Total Marks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($exam = $examsResult->fetch_assoc()): 
                                    $today = date('Y-m-d');
                                    $status = ($exam['examDate'] > $today) ? 'Upcoming' : 'Completed';
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($exam['examName']); ?></td>
                                    <td><?= htmlspecialchars($exam['subjectName']); ?></td>
                                    <td><?= htmlspecialchars($exam['examType']); ?></td>
                                    <td><?= date('M j, Y', strtotime($exam['examDate'])); ?></td>
                                    <td><?= $exam['totalMarks']; ?></td>
                                    <td>
                                        <span class="badge bg-<?= $status == 'Upcoming' ? 'info' : 'success'; ?>">
                                            <?= $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="enter_results.php?examId=<?= $exam['Id']; ?>" 
                                           class="btn btn-sm btn-primary">Enter Results</a>
                                        <a href="view_results.php?examId=<?= $exam['Id']; ?>" 
                                           class="btn btn-sm btn-secondary">View Results</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>