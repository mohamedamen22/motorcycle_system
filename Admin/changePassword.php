<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------
if(isset($_POST['save'])){
    $className = $_POST['className'];
   
    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE className = '$className'");
    $ret = mysqli_fetch_array($query);

    if($ret > 0){ 
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Class Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblclass(className) VALUES('$className')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

//---------------------------------------EDIT------------------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblclass WHERE Id = '$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE-----------------------------
    if(isset($_POST['update'])) {
        $className = $_POST['className'];
    
        $query = mysqli_query($conn, "UPDATE tblclass SET className='$className' WHERE Id='$Id'");

        if ($query) {
            echo "<script type=\"text/javascript\">
                window.location = (\"createClass.php\")
                </script>"; 
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

//--------------------------------DELETE------------------------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "DELETE FROM tblclass WHERE Id='$Id'");

    if ($query == TRUE) {
        echo "<script type=\"text/javascript\">
            window.location = (\"createClass.php\")
            </script>";  
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>"; 
    }
}
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
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
/* Professional Dashboard Styles */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    --danger-color: #e74c3c;
    --light-bg: #f8f9fa;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background-color: #f5f7fb;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: var(--card-shadow);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 12px 12px 0 0 !important;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
}

.form-control {
    border-radius: 8px;
    padding: 12px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.btn-primary {
    background-color: var(--secondary-color);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: var(--primary-color);
    transform: translateY(-2px);
}

.table {
    border-collapse: separate;
    border-spacing: 0 8px;
}

.table thead th {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 15px;
}

.table tbody tr {
    background: white;
    transition: all 0.3s ease;
    box-shadow: var(--card-shadow);
}

.table tbody tr:hover {
    transform: translateX(8px);
}

.dataTables_wrapper {
    padding: 0;
}

.dataTables_filter input {
    border-radius: 8px;
    padding: 8px 12px;
}

.alert {
    border-radius: 8px;
    padding: 15px 20px;
    margin: 20px 0;
}

.action-buttons .btn {
    padding: 8px 12px;
    margin: 0 3px;
    border-radius: 6px;
}

.btn-warning {
    background-color: #f39c12;
    border: none;
}

.btn-danger {
    background-color: var(--danger-color);
    border: none;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .form-group.row > div {
        margin-bottom: 1rem;
    }
}
</style>
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
            <h1 class="h3 mb-0 text-gray-800">Change Password</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Security</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                </div>
                <div class="card-body">
                  <?php
                  if (isset($_POST['changePassword'])) {
                      $userType = $_POST['userType'];
                      $username = mysqli_real_escape_string($conn, $_POST['username']);
                      $newPassword = mysqli_real_escape_string($conn, md5($_POST['newPassword']));
                      $confirmPassword = mysqli_real_escape_string($conn, md5($_POST['confirmPassword']));

                      // Checking if passwords match
                      if ($newPassword != $confirmPassword) {
                          echo "<div class='alert alert-danger'>Passwords do not match!</div>";
                      } else {
                          // Prepare the query based on the user type
                          if ($userType == "Administrator") {
                              $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username'";
                              $table = "tbladmin";
                          } elseif ($userType == "ClassTeacher" || $userType == "ExamTeacher") {
                              $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username'";
                              $table = "tblclassteacher";
                          } else {
                              echo "<div class='alert alert-danger'>Invalid User Role!</div>";
                              exit;
                          }

                          $result = mysqli_query($conn, $query);

                          if ($result === false) {
                              echo "<div class='alert alert-danger'>Error executing query: " . mysqli_error($conn) . "</div>";
                          } else {
                              $numRows = mysqli_num_rows($result);
                              if ($numRows > 0) {
                                  $updateQuery = "UPDATE $table SET password = '$newPassword' WHERE emailAddress = '$username'";
                                  if (mysqli_query($conn, $updateQuery)) {
                                      echo "<div class='alert alert-success'>Password successfully updated!</div>";
                                  } else {
                                      echo "<div class='alert alert-danger'>Error updating password: " . mysqli_error($conn) . "</div>";
                                  }
                              } else {
                                  echo "<div class='alert alert-danger'>User not found!</div>";
                              }
                          }
                      }
                  }
                  ?>
                  
                  <form method="POST" action="">
                    <div class="form-group">
                      <label for="userType">User Type</label>
                      <select class="form-control" id="userType" name="userType" required>
                        <option value="">-- Select User Role --</option>
                        <option value="Administrator">Administrator</option>
                        <option value="ClassTeacher">Class Teacher</option>
                        <option value="ExamTeacher">Exam Teacher</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="username">Email Address</label>
                      <input type="email" class="form-control" id="username" name="username" placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="form-group">
                      <label for="newPassword">New Password</label>
                      <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password" required>
                      <small class="form-text text-muted">Password must be at least 8 characters long</small>
                    </div>
                    
                    <div class="form-group">
                      <label for="confirmPassword">Confirm Password</label>
                      <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required>
                    </div>
                    
                    <button type="submit" name="changePassword" class="btn btn-primary">Change Password</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php";?>
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
  
  <script>
    // Password validation
    $(document).ready(function() {
      $('#newPassword, #confirmPassword').on('keyup', function() {
        if ($('#newPassword').val() != '' && $('#confirmPassword').val() != '') {
          if ($('#newPassword').val() == $('#confirmPassword').val()) {
            $('#confirmPassword').removeClass('is-invalid');
            $('#confirmPassword').addClass('is-valid');
          } else {
            $('#confirmPassword').removeClass('is-valid');
            $('#confirmPassword').addClass('is-invalid');
          }
        }
      });
    });
  </script>
</body>
</html>