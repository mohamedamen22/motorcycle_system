<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize status message
$statusMsg = '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Attendance Management System">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>View Attendance - Dashboard</title>
  <!-- CSS Links -->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .present { color: #28a745; font-weight: bold; }
    .absent { color: #dc3545; font-weight: bold; }
    .table-responsive { overflow-x: auto; }
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
            <h1 class="h3 mb-0 text-gray-800">View Class Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Class Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Search Attendance Records</h6>
                </div>
                <div class="card-body">
                  <form method="post" class="needs-validation" novalidate>
                    <div class="form-row">
                      <div class="col-md-6 mb-3">
                        <label for="dateTaken">Select Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dateTaken" name="dateTaken" required>
                        <div class="invalid-feedback">Please select a date</div>
                      </div>
                      <div class="col-md-6 mb-3 d-flex align-items-end">
                        <button type="submit" name="view" class="btn btn-primary">
                          <i class="fas fa-search"></i> View Attendance
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
                  <?php if(!empty($statusMsg)): ?>
                    <div class="alert alert-info"><?php echo $statusMsg; ?></div>
                  <?php endif; ?>
                </div>
                <div class="card-body">
                  <?php
                  if(isset($_POST['view'])){
                    $dateTaken = $_POST['dateTaken'];

                    if(empty($dateTaken)) {
                      $statusMsg = "Please select a date";
                    } else {
                      $query = "SELECT s.firstName, s.lastName, s.otherName, s.admissionNumber,
                                       c.className, ca.classArmName, 
                                       st.sessionName, t.termName,
                                       a.dateTimeTaken, a.status
                                FROM tblattendance a
                                INNER JOIN tblstudents s ON a.admissionNo = s.admissionNumber
                                INNER JOIN tblclass c ON a.classId = c.Id
                                INNER JOIN tblclassarms ca ON a.classArmId = ca.Id
                                INNER JOIN tblsessionterm st ON a.sessionTermId = st.Id
                                INNER JOIN tblterm t ON st.termId = t.Id
                                WHERE a.dateTimeTaken LIKE CONCAT(?, '%')
                                ORDER BY a.dateTimeTaken DESC";
                      
                      $stmt = $conn->prepare($query);
                      $stmt->bind_param("s", $dateTaken);
                      $stmt->execute();
                      $result = $stmt->get_result();
                      
                      if($result->num_rows > 0) {
                        echo '<div class="table-responsive">
                                <table class="table table-bordered table-hover" id="attendanceTable">
                                  <thead class="thead-light">
                                    <tr>
                                      <th>#</th>
                                      <th>Student Name</th>
                                      <th>Admission No</th>
                                      <th>Class</th>
                                      <th>Class Arm</th>
                                      <th>Session</th>
                                      <th>Term</th>
                                      <th>Status</th>
                                      <th>Date</th>
                                    </tr>
                                  </thead>
                                  <tbody>';
                        
                        $counter = 1;
                        while($row = $result->fetch_assoc()) {
                          $fullName = htmlspecialchars($row['firstName']).' '.htmlspecialchars($row['lastName']);
                          if(!empty($row['otherName'])) {
                            $fullName .= ' '.htmlspecialchars($row['otherName']);
                          }
                          
                          $status = ($row['status'] == 1 || $row['status'] == '1') ? 
                            '<span class="present">Present</span>' : 
                            '<span class="absent">Absent</span>';
                          
                          echo '<tr>
                                  <td>'.$counter.'</td>
                                  <td>'.$fullName.'</td>
                                  <td>'.htmlspecialchars($row['admissionNumber']).'</td>
                                  <td>'.htmlspecialchars($row['className']).'</td>
                                  <td>'.htmlspecialchars($row['classArmName']).'</td>
                                  <td>'.htmlspecialchars($row['sessionName']).'</td>
                                  <td>'.htmlspecialchars($row['termName']).'</td>
                                  <td>'.$status.'</td>
                                  <td>'.htmlspecialchars($row['dateTimeTaken']).'</td>
                                </tr>';
                          $counter++;
                        }
                        
                        echo '</tbody>
                            </table>
                          </div>';
                      } else {
                        $statusMsg = "No attendance records found for ".date('F j, Y', strtotime($dateTaken));
                        echo '<div class="alert alert-warning">'.$statusMsg.'</div>';
                      }
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#attendanceTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]],
        "responsive": true,
        "dom": '<"top"f>rt<"bottom"lip><"clear">'
      });

      (function() {
        'use strict';
        window.addEventListener('load', function() {
          var forms = document.getElementsByClassName('needs-validation');
          var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();

      document.getElementById('dateTaken').valueAsDate = new Date();
    });
  </script>

</body>
</html>
