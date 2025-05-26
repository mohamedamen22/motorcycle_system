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
  <meta name="description" content="School Attendance System">
  <meta name="author" content="Your Name">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>View Student Attendance</title>
  
  <!-- CSS Links -->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <!-- JavaScript for AJAX -->
  <script>
    function typeDropDown(str) {
      if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
      } else { 
        if (window.XMLHttpRequest) {
          // Modern browsers
          xmlhttp = new XMLHttpRequest();
        } else {
          // Old IE versions
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("txtHint").innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET","ajaxCallTypes.php?tid="+str,true);
        xmlhttp.send();
      }
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- End Sidebar -->
    
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- End Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">View Student Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">View Student Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Student Attendance</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Student<span class="text-danger ml-2">*</span></label>
                        <?php
                        $qry = "SELECT * FROM tblstudents WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]' ORDER BY firstName ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;
                        if ($num > 0) {
                          echo '<select required name="admissionNumber" class="form-control mb-3">';
                          echo '<option value="">--Select Student--</option>';
                          while ($rows = $result->fetch_assoc()) {
                            echo '<option value="'.$rows['admissionNumber'].'">'.$rows['firstName'].' '.$rows['lastName'].'</option>';
                          }
                          echo '</select>';
                        }
                        ?>  
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Type<span class="text-danger ml-2">*</span></label>
                        <select required name="type" onchange="typeDropDown(this.value)" class="form-control mb-3">
                          <option value="">--Select--</option>
                          <option value="1">All</option>
                          <option value="2">By Single Date</option>
                          <option value="3">By Date Range</option>
                        </select>
                      </div>
                    </div>
                    <?php echo "<div id='txtHint'></div>"; ?>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                    <button type="submit" name="export_students_pdf" class="btn btn-danger">Export as PDF</button>
                  </form>
                </div>
              </div>

              <!-- Attendance Table -->
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
                            <th>Admission No</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Status</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $totalPresent = 0;
                            $totalAbsent = 0;

                            if(isset($_POST['view'])) {
                              $admissionNumber = $_POST['admissionNumber'];
                              $type = $_POST['type'];

                              // Build query based on selected type
                              $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                                        tblclass.className, tblclassarms.classArmName, 
                                        tblstudents.firstName, tblstudents.lastName, tblstudents.admissionNumber
                                        FROM tblattendance
                                        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
                                        INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
                                        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                                        WHERE tblattendance.admissionNo = '$admissionNumber' 
                                        AND tblattendance.classId = '$_SESSION[classId]' 
                                        AND tblattendance.classArmId = '$_SESSION[classArmId]'";

                              if($type == "2") { // Single Date
                                $singleDate = $_POST['singleDate'];
                                $query .= " AND tblattendance.dateTimeTaken = '$singleDate'";
                              } 
                              elseif($type == "3") { // Date Range
                                $fromDate = $_POST['fromDate'];
                                $toDate = $_POST['toDate'];
                                $query .= " AND tblattendance.dateTimeTaken BETWEEN '$fromDate' AND '$toDate'";
                              }

                              $rs = $conn->query($query);
                              $num = $rs->num_rows;
                              $sn = 0;
                              
                              if($num > 0) {
                                while ($rows = $rs->fetch_assoc()) {
                                  $status = ($rows['status'] == '1') ? 
                                    "<span class='badge badge-success'>Present</span>" : 
                                    "<span class='badge badge-danger'>Absent</span>";
                                  
                                  if($rows['status'] == '1') $totalPresent++;
                                  else $totalAbsent++;

                                  echo "<tr>
                                    <td>".++$sn."</td>
                                    <td>".$rows['firstName']."</td>
                                    <td>".$rows['lastName']."</td>
                                    <td>".$rows['admissionNumber']."</td>
                                    <td>".$rows['className']."</td>
                                    <td>".$rows['classArmName']."</td>
                                    <td>".$status."</td>
                                    <td>".$rows['dateTimeTaken']."</td>
                                  </tr>";
                                }

                                // Display totals
                                echo "<tr class='font-weight-bold'>
                                  <td colspan='6'>Total Records: $num</td>
                                  <td>Present: $totalPresent</td>
                                  <td>Absent: $totalAbsent</td>
                                </tr>";
                              } else {
                                echo "<tr><td colspan='8' class='text-center'>No attendance records found</td></tr>";
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
    </div>
  </div>

  <!-- JavaScript Libraries -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>
</html>