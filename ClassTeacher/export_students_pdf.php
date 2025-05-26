<?php 
error_reporting(E_ALL); // Enable error reporting during development
include '../Includes/dbcon.php';  // Ensure dbcon.php is correctly included
include '../Includes/session.php';  // Ensure session.php is correctly included
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

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
                  <h6 class="m-0 font-weight-bold text-primary">View Class Attendance</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="dateTaken" required>
                      </div>
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Class Attendance</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Other Name</th>
                            <th>Admission No</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (isset($_POST['view'])) {
                            $dateTaken = $_POST['dateTaken'];
                            
                            // Prepare SQL query with placeholders to avoid SQL injection
                            $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
                                      tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
                                      tblstudents.firstName, tblstudents.lastName, tblstudents.otherName, tblstudents.admissionNumber
                                      FROM tblattendance
                                      INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                                      INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                                      INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
                                      INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
                                      INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                                      WHERE tblattendance.dateTimeTaken = ? AND tblattendance.classId = ? AND tblattendance.classArmId = ?";
                            
                            if ($stmt = $conn->prepare($query)) {
                              $stmt->bind_param("sii", $dateTaken, $_SESSION['classId'], $_SESSION['classArmId']);
                              $stmt->execute();
                              $result = $stmt->get_result();
                              
                              $num = $result->num_rows;
                              $sn = 0;
                              $presentCount = 0;
                              $absentCount = 0;

                              if ($num > 0) {
                                while ($rows = $result->fetch_assoc()) {
                                  $status = $rows['status'] == '1' ? "Present" : "Absent";
                                  $colour = $rows['status'] == '1' ? "#00FF00" : "#FF0000";
                                  $status == "Present" ? $presentCount++ : $absentCount++;
                                  $sn++;
                                  echo "
                                  <tr>
                                    <td>$sn</td>
                                    <td>{$rows['firstName']}</td>
                                    <td>{$rows['lastName']}</td>
                                    <td>{$rows['otherName']}</td>
                                    <td>{$rows['admissionNumber']}</td>
                                    <td>{$rows['className']}</td>
                                    <td>{$rows['classArmName']}</td>
                                    <td>{$rows['sessionName']}</td>
                                    <td>{$rows['termName']}</td>
                                    <td style='background-color: $colour;'>$status</td>
                                    <td>{$rows['dateTimeTaken']}</td>
                                  </tr>";
                                }

                                // Show total summary
                                echo "
                                <tr>
                                  <td colspan='11'>
                                    <div class='alert alert-info text-center' role='alert'>
                                      <strong>Total Present:</strong> $presentCount &nbsp;&nbsp; | &nbsp;&nbsp; 
                                      <strong>Total Absent:</strong> $absentCount
                                    </div>
                                  </td>
                                </tr>";
                              } else {
                                echo "
                                <tr>
                                  <td colspan='11'>
                                    <div class='alert alert-danger' role='alert'>
                                      No Record Found!
                                    </div>
                                  </td>
                                </tr>";
                              }

                              $stmt->close();
                            } else {
                              echo "Error preparing the query.";
                            }
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <?php include "Includes/footer.php";?>
    </div>
  </div>

  <!-- Scroll to top -->
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
      $('#dataTableHover').DataTable();
    });
  </script>
</body>
</html>
