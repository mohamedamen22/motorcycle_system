<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get class and classArm info
$query = "SELECT tblclass.className, tblclassarms.classArmName 
    FROM tblclassteacher
    INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
    WHERE tblclassteacher.Id = '$_SESSION[userId]'";

$rs = $conn->query($query);
$rrw = $rs->fetch_assoc();

// Get classId and classArmId from session or fetch from tblclassteacher
$_SESSION['classId'] = $_SESSION['classId'] ?? '';
$_SESSION['classArmId'] = $_SESSION['classArmId'] ?? '';

$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

// ========== Attendance Statistics ==========
// Today
$today = date('Y-m-d');
$queryToday = mysqli_query($conn, "SELECT COUNT(*) as totalToday,
  SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as presentToday,
  SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as absentToday
  FROM tblattendance
  WHERE classId = '$classId' AND classArmId = '$classArmId' AND DATE(dateTimeTaken) = '$today'");
$dataToday = mysqli_fetch_assoc($queryToday);

// This Week
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$queryWeek = mysqli_query($conn, "SELECT COUNT(*) as totalWeek,
  SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as presentWeek,
  SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as absentWeek
  FROM tblattendance
  WHERE classId = '$classId' AND classArmId = '$classArmId'
  AND DATE(dateTimeTaken) BETWEEN '$monday' AND '$sunday'");
$dataWeek = mysqli_fetch_assoc($queryWeek);

// This Month
$firstDay = date('Y-m-01');
$lastDay = date('Y-m-t');
$queryMonth = mysqli_query($conn, "SELECT COUNT(*) as totalMonth,
  SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as presentMonth,
  SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as absentMonth
  FROM tblattendance
  WHERE classId = '$classId' AND classArmId = '$classArmId'
  AND DATE(dateTimeTaken) BETWEEN '$firstDay' AND '$lastDay'");
$dataMonth = mysqli_fetch_assoc($queryMonth);

// This Year
$yearStart = date('Y-01-01');
$yearEnd = date('Y-12-31');
$queryYear = mysqli_query($conn, "SELECT COUNT(*) as totalYear,
  SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as presentYear,
  SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as absentYear
  FROM tblattendance
  WHERE classId = '$classId' AND classArmId = '$classArmId'
  AND DATE(dateTimeTaken) BETWEEN '$yearStart' AND '$yearEnd'");
$dataYear = mysqli_fetch_assoc($queryYear);
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
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Class Teacher Dashboard (<?php echo $rrw['className'].' - '.$rrw['classArmName'];?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
          <!-- New User Card Example -->
          <?php 
$query1=mysqli_query($conn,"SELECT * from tblstudents where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]'");                       
$students = mysqli_num_rows($query1);
?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Students</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $students;?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Monthly) Card Example -->
             <?php 
$query1=mysqli_query($conn,"SELECT * from tblclass");                       
$class = mysqli_num_rows($query1);
?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Classes</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class;?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                        <span>Since last month</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Annual) Card Example -->
             <?php 
$query1=mysqli_query($conn,"SELECT * from tblclassarms");                       
$classArms = mysqli_num_rows($query1);
?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Class Arms</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classArms;?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                        <span>Since last years</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-code-branch fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Pending Requests Card Example -->
            <?php 
$query1=mysqli_query($conn,"SELECT * from tblattendance where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]'");                       
$totAttendance = mysqli_num_rows($query1);
?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Attendance</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totAttendance;?></div>
                      <div class="mt-2 mb-0 text-muted text-xs">
                        <!-- <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                        <span>Since yesterday</span> -->
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!--Row: Attendance Summary Stats-->
<div class="row">
  <div class="col-md-6 col-xl-3 mb-4">
    <div class="card bg-primary text-white shadow">
      <div class="card-body">
        Maanta (Today)
        <div class="text-white-50 small">
          Guud: <?php echo $dataToday['totalToday']; ?> |
          Joogay: <?php echo $dataToday['presentToday']; ?> |
          Maqan: <?php echo $dataToday['absentToday']; ?>
          <!-- Link to view detailed attendance for today -->
          <a href="view_attendance.php?date=2025-04-15&classId=1&classArmId=1" class="btn btn-info btn-sm mt-2">View Today's Attendance</a>

        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-4">
    <div class="card bg-success text-white shadow">
      <div class="card-body">
        Usbuucan (This Week)
        <div class="text-white-50 small">
          Guud: <?php echo $dataWeek['totalWeek']; ?> |
          Joogay: <?php echo $dataWeek['presentWeek']; ?> |
          Maqan: <?php echo $dataWeek['absentWeek']; ?>
          <!-- Link to view detailed attendance for this week -->
          <a href="view_attendance.php?start_date=<?php echo $monday; ?>&end_date=<?php echo $sunday; ?>&classId=<?php echo $classId; ?>&classArmId=<?php echo $classArmId; ?>" class="btn btn-success btn-sm mt-2">View This Week's Attendance</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-4">
    <div class="card bg-warning text-white shadow">
      <div class="card-body">
        Bishan (This Month)
        <div class="text-white-50 small">
          Guud: <?php echo $dataMonth['totalMonth']; ?> |
          Joogay: <?php echo $dataMonth['presentMonth']; ?> |
          Maqan: <?php echo $dataMonth['absentMonth']; ?>
          <!-- Link to view detailed attendance for this month -->
          <a href="view_attendance.php?start_date=<?php echo $firstDay; ?>&end_date=<?php echo $lastDay; ?>&classId=<?php echo $classId; ?>&classArmId=<?php echo $classArmId; ?>" class="btn btn-warning btn-sm mt-2">View This Month's Attendance</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-4">
    <div class="card bg-danger text-white shadow">
      <div class="card-body">
        Sanadkan (This Year)
        <div class="text-white-50 small">
          Guud: <?php echo $dataYear['totalYear']; ?> |
          Joogay: <?php echo $dataYear['presentYear']; ?> |
          Maqan: <?php echo $dataYear['absentYear']; ?>
          <!-- Link to view detailed attendance for this year -->
          <a href="view_attendance.php?start_date=<?php echo $yearStart; ?>&end_date=<?php echo $yearEnd; ?>&classId=<?php echo $classId; ?>&classArmId=<?php echo $classArmId; ?>" class="btn btn-danger btn-sm mt-2">View This Year's Attendance</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!--End Row-->

          <!--Row-->

          <!-- <div class="row">
            <div class="col-lg-12 text-center">
              <p>Do you like this template ? you can download from <a href="https://github.com/indrijunanda/RuangAdmin"
                  class="btn btn-primary btn-sm" target="_blank"><i class="fab fa-fw fa-github"></i>&nbsp;GitHub</a></p>
            </div>
          </div> -->

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include 'includes/footer.php';?>
      <!-- Footer -->
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>  

  <head>
    <style>
        /* Include the CSS code here */
        /* General Card Styling */
.card {
    border-radius: 10px;
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.card-body {
    padding: 20px;
}

.card .small {
    font-size: 14px;
    font-weight: normal;
}

.card .btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

/* Card Colors */
.bg-primary {
    background-color: #007bff !important;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

/* Card Header */
.card-body > h5 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
}

/* Button Styling */
.card-body .btn {
    width: 100%;
    text-align: center;
    text-transform: uppercase;
}

.card-body .btn-info {
    background-color: #17a2b8 !important;
    border: none;
}

.card-body .btn-success {
    background-color: #28a745 !important;
    border: none;
}

.card-body .btn-warning {
    background-color: #ffc107 !important;
    border: none;
}

.card-body .btn-danger {
    background-color: #dc3545 !important;
    border: none;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 20px;
    }
}

@media (min-width: 768px) {
    .col-md-6 {
        margin-bottom: 30px;
    }
}

    </style>
</head>

 

</body>

</html>