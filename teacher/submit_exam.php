<?php
include '../Includes/dbcon.php';
include 'includes/header.php';

if(!isset($_SESSION['userId']) {
    header("Location: login.php");
    exit();
}

$teacherId = $_SESSION['userId'];
$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];
$subjectId = isset($_GET['subjectId']) ? intval($_GET['subjectId']) : 0;

// Get active session term
$sessionTermQuery = "SELECT * FROM tblsessionterm WHERE isActive = 1 LIMIT 1";
$sessionTerm = $conn->query($sessionTermQuery)->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $examName = $conn->real_escape_string($_POST['examName']);
    $examType = $conn->real_escape_string($_POST['examType']);
    $totalMarks = floatval($_POST['totalMarks']);
    $passMark = floatval($_POST['passMark']);
    $examDate = $conn->real_escape_string($_POST['examDate']);
    $dueDate = $conn->real_escape_string($_POST['dueDate']);
    $instructions = $conn->real_escape_string($_POST['instructions']);
    
    $insertQuery = "INSERT INTO tblexams (examName, examType, subjectId, classId, classArmId, 
                   teacherId, sessionTermId, totalMarks, passMark, examDate, dueDate, instructions)
                   VALUES ('$examName', '$examType', $subjectId, $classId, $classArmId, 
                   $teacherId, {$sessionTerm['Id']}, $totalMarks, $passMark, '$examDate', '$dueDate', '$instructions')";
    
    if($conn->query($insertQuery)) {
        $_SESSION['success'] = "Exam created successfully!";
        header("Location: view_exams.php");
        exit();
    } else {
        $_SESSION['error'] = "Error creating exam: " . $conn->error;
    }
}

// Get subject details
$subjectQuery = "SELECT * FROM tblsubjects WHERE Id = $subjectId";
$subject = $conn->query($subjectQuery)->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Create New Exam</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="view_exams.php" class="btn btn-sm btn-outline-secondary">
                        View All Exams
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($subject['subjectName']); ?>" readonly>
                                <input type="hidden" name="subjectId" value="<?= $subjectId; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Exam Name</label>
                                <input type="text" class="form-control" name="examName" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Exam Type</label>
                                <select class="form-select" name="examType" required>
                                    <option value="">Select Type</option>
                                    <option value="Quiz">Quiz</option>
                                    <option value="Midterm">Midterm</option>
                                    <option value="Final">Final</option>
                                    <option value="Assignment">Assignment</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Marks</label>
                                <input type="number" step="0.01" class="form-control" name="totalMarks" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pass Mark</label>
                                <input type="number" step="0.01" class="form-control" name="passMark" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Exam Date</label>
                                <input type="date" class="form-control" name="examDate" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="dueDate" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instructions</label>
                            <textarea class="form-control" name="instructions" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Exam</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>