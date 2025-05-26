<?php
include '../Includes/dbcon.php';
include 'includes/header.php';

if(!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

$studentsQuery = "SELECT * FROM tblstudents 
                 WHERE classId = $classId AND classArmId = $classArmId
                 ORDER BY lastName, firstName";
$studentsResult = $conn->query($studentsQuery);
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Class Students</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Admission No.</th>
                                    <th>Full Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = 1;
                                while($student = $studentsResult->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?= $count++; ?></td>
                                    <td><?= htmlspecialchars($student['admissionNumber']); ?></td>
                                    <td><?= htmlspecialchars($student['firstName'].' '.$student['lastName']); ?></td>
                                    <td>
                                        <a href="view_student_results.php?studentId=<?= $student['Id']; ?>" 
                                           class="btn btn-sm btn-info">View Results</a>
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