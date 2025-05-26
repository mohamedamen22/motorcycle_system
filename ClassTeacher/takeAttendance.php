<?php 
// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Secure session start and include files
include '../Includes/dbcon.php';
include '../Includes/session.php';


// Initialize status message
$statusMsg = '';

// Get teacher's class information
$teacherId = $_SESSION['userId'];
$query = $conn->prepare("SELECT tblclass.className, tblclassarms.classArmName 
                        FROM tblclassteacher
                        INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
                        INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
                        WHERE tblclassteacher.Id = ?");
$query->bind_param("i", $teacherId);
$query->execute();
$result = $query->get_result();
$classInfo = $result->fetch_assoc();

if (!$classInfo) {
    die("Error: No class assigned to this teacher.");
}

// Get current active session term
$activeTermQuery = $conn->query("SELECT * FROM tblsessionterm WHERE isActive = '1' LIMIT 1");
$activeTerm = $activeTermQuery->fetch_assoc();
$sessionTermId = $activeTerm['Id'] ?? 0;

$dateTaken = date("Y-m-d");

// Check if attendance already exists for today
$attendanceCheck = $conn->prepare("SELECT * FROM tblattendance 
                                  WHERE classId = ? AND classArmId = ? AND dateTimeTaken = ?");
$attendanceCheck->bind_param("iis", $_SESSION['classId'], $_SESSION['classArmId'], $dateTaken);
$attendanceCheck->execute();
$attendanceResult = $attendanceCheck->get_result();

if ($attendanceResult->num_rows == 0) {
    // Initialize attendance for all students
    $studentQuery = $conn->prepare("SELECT admissionNumber FROM tblstudents 
                                   WHERE classId = ? AND classArmId = ?");
    $studentQuery->bind_param("ii", $_SESSION['classId'], $_SESSION['classArmId']);
    $studentQuery->execute();
    $students = $studentQuery->get_result();
    
    $initAttendance = $conn->prepare("INSERT INTO tblattendance 
                                     (admissionNo, classId, classArmId, sessionTermId, status, dateTimeTaken) 
                                     VALUES (?, ?, ?, ?, '0', ?)");
    
    while ($student = $students->fetch_assoc()) {
        $initAttendance->bind_param("siiss", 
            $student['admissionNumber'], 
            $_SESSION['classId'], 
            $_SESSION['classArmId'], 
            $sessionTermId, 
            $dateTaken
        );
        $initAttendance->execute();
    }
}

// Process attendance submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    if (!isset($_POST['check']) || !is_array($_POST['check'])) {
        $statusMsg = "<div class='alert alert-warning'>Please select at least one student.</div>";
    } else {
        // Check if attendance was already taken today
        $takenCheck = $conn->prepare("SELECT * FROM tblattendance 
                                    WHERE classId = ? AND classArmId = ? AND dateTimeTaken = ? AND status = '1'");
        $takenCheck->bind_param("iis", $_SESSION['classId'], $_SESSION['classArmId'], $dateTaken);
        $takenCheck->execute();
        
        if ($takenCheck->get_result()->num_rows > 0) {
            $statusMsg = "<div class='alert alert-danger'>Attendance has already been taken for today!</div>";
        } else {
            // Prepare update statement
            $updateStmt = $conn->prepare("UPDATE tblattendance 
                                         SET status = '1' 
                                         WHERE admissionNo = ? AND dateTimeTaken = ?");
            
            // Process each selected student
            $successCount = 0;
            foreach ($_POST['check'] as $admissionNo) {
                $updateStmt->bind_param("ss", $admissionNo, $dateTaken);
                if ($updateStmt->execute()) {
                    $successCount++;
                }
            }
            
            if ($successCount > 0) {
                $statusMsg = "<div class='alert alert-success'>Attendance recorded for $successCount students!</div>";
            } else {
                $statusMsg = "<div class='alert alert-danger'>Failed to record attendance.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Class Attendance System</title>
  
  <!-- CSS Libraries -->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  
  <style>
    .attendance-card {
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .attendance-header {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      border-radius: 10px 10px 0 0 !important;
    }
    .attendance-buttons { 
      margin-top: 20px; 
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .attendance-buttons button { 
      padding: 8px 20px; 
      border: none; 
      border-radius: 6px; 
      font-size: 14px; 
      font-weight: 500; 
      cursor: pointer; 
      transition: all 0.2s ease-in-out; 
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08); 
      min-width: 150px; 
      text-align: center; 
    }
    .btn-attend {
      background-color: #28a745;
      color: white;
    }
    .btn-select {
      background-color: #17a2b8;
      color: white;
    }
    .btn-deselect {
      background-color: #ffc107;
      color: #212529;
    }
    .search-box {
      position: relative;
      margin-bottom: 20px;
    }
    .search-box i {
      position: absolute;
      top: 12px;
      left: 12px;
      color: #6c757d;
    }
    .search-input {
      padding-left: 40px;
      border-radius: 30px;
    }
    .table-responsive {
      overflow-x: auto;
    }
    .table-hover tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.05);
    }
    .status-message {
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 1000;
      max-width: 400px;
      animation: fadeIn 0.5s, fadeOut 0.5s 4.5s;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
      from { opacity: 1; transform: translateY(0); }
      to { opacity: 0; transform: translateY(-20px); }
    }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Take Attendance <small class="text-muted">(<?php echo date("F j, Y"); ?>)</small></h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active">Class Attendance</li>
            </ol>
          </div>

          <?php if ($statusMsg): ?>
            <div class="status-message"><?php echo $statusMsg; ?></div>
          <?php endif; ?>

          <div class="row">
            <div class="col-lg-12">
              <form method="post" id="attendanceForm">
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" id="searchInput" class="form-control search-input" 
                         placeholder="Search students by name or admission number...">
                </div>
                
                <div class="card attendance-card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between attendance-header">
                    <h6 class="m-0 font-weight-bold">
                      <i class="fas fa-users mr-2"></i>
                      <?php echo htmlspecialchars($classInfo['className'] . ' - ' . $classInfo['classArmName']); ?>
                    </h6>
                    <span class="badge badge-light">
                      <i class="fas fa-calendar-day mr-1"></i>
                      <?php echo date("l, F j, Y"); ?>
                    </span>
                  </div>
                  
                  <div class="table-responsive p-3">
                    <table class="table table-hover align-middle">
                      <thead class="thead-light">
                        <tr>
                          <th width="5%">#</th>
                          <th width="20%">Student Name</th>
                          <th width="15%">Admission No</th>
                          <th width="15%">Class</th>
                          <th width="15%">Class Arm</th>
                          <th width="10%">Present</th>
                        </tr>
                      </thead>
                      <tbody id="studentsTable">
                        <?php
                        $studentQuery = $conn->prepare("SELECT s.Id, s.admissionNumber, s.firstName, s.lastName, 
                                                      c.className, ca.classArmName
                                                      FROM tblstudents s
                                                      INNER JOIN tblclass c ON c.Id = s.classId
                                                      INNER JOIN tblclassarms ca ON ca.Id = s.classArmId
                                                      WHERE s.classId = ? AND s.classArmId = ?
                                                      ORDER BY s.lastName, s.firstName");
                        $studentQuery->bind_param("ii", $_SESSION['classId'], $_SESSION['classArmId']);
                        $studentQuery->execute();
                        $students = $studentQuery->get_result();
                        
                        if ($students->num_rows > 0) {
                            $counter = 0;
                            while ($student = $students->fetch_assoc()) {
                                $counter++;
                                echo "<tr>
                                    <td>{$counter}</td>
                                    <td>{$student['firstName']} {$student['lastName']}</td>
                                    <td>{$student['admissionNumber']}</td>
                                    <td>{$student['className']}</td>
                                    <td>{$student['classArmName']}</td>
                                    <td>
                                        <div class='form-check'>
                                            <input class='form-check-input attendance-check' 
                                                   type='checkbox' name='check[]' 
                                                   value='{$student['admissionNumber']}' id='check{$counter}'>
                                            <label class='form-check-label' for='check{$counter}'></label>
                                        </div>
                                    </td>
                                </tr>";
                                echo "<input type='hidden' name='admissionNo[]' value='{$student['admissionNumber']}'>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4'>
                                <div class='alert alert-info'>No students found in this class.</div>
                                </td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                    
                    <div class="attendance-buttons">
                      <button type="submit" name="save" class="btn btn-attend">
                        <i class="fas fa-check-circle mr-2"></i>Submit Attendance
                      </button>
                      <button type="button" class="btn btn-select" onclick="toggleCheckboxes(true)">
                        <i class="fas fa-check-square mr-2"></i>Select All
                      </button>
                      <button type="button" class="btn btn-deselect" onclick="toggleCheckboxes(false)">
                        <i class="fas fa-times-circle mr-2"></i>Deselect All
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>
  
  <!-- Scroll to top button -->
  <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
  
  <!-- JavaScript Libraries -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Auto-hide status messages after 5 seconds
      setTimeout(function() {
        $('.status-message').fadeOut('slow');
      }, 5000);
      
      // Search functionality
      $('#searchInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('#studentsTable tr').filter(function() {
          const rowText = $(this).text().toLowerCase();
          $(this).toggle(rowText.indexOf(searchText) > -1);
        });
      });
    });
    
    // Toggle all checkboxes
    function toggleCheckboxes(selectAll) {
      $('.attendance-check').prop('checked', selectAll);
    }
    
    // Confirm before submitting attendance
    $('#attendanceForm').on('submit', function() {
      const checkedCount = $('.attendance-check:checked').length;
      if (checkedCount === 0) {
        alert('Please select at least one student as present.');
        return false;
      }
      return confirm(`Are you sure you want to mark ${checkedCount} students as present?`);
    });
  </script>
</body>
</html>