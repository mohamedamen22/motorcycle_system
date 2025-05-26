<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get current admin data
$adminId = $_SESSION['userId'];
$query = mysqli_query($conn, "SELECT * FROM tbladmin WHERE Id = '$adminId'");
$adminData = mysqli_fetch_array($query);

// Update profile
if(isset($_POST['updateProfile'])) {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $updateQuery = mysqli_query($conn, "UPDATE tbladmin SET 
        firstName = '$firstName',
        lastName = '$lastName',
        emailAddress = '$email'
        WHERE Id = '$adminId'");
    
    if($updateQuery) {
        $successMsg = "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Refresh data
        $query = mysqli_query($conn, "SELECT * FROM tbladmin WHERE Id = '$adminId'");
        $adminData = mysqli_fetch_array($query);
    } else {
        $errorMsg = "<div class='alert alert-danger'>Error updating profile: ".mysqli_error($conn)."</div>";
    }
}

// Change password
if(isset($_POST['changePassword'])) {
    $currentPassword = md5($_POST['currentPassword']);
    $newPassword = md5($_POST['newPassword']);
    $confirmPassword = md5($_POST['confirmPassword']);
    
    // Verify current password
    if($currentPassword != $adminData['password']) {
        $errorMsg = "<div class='alert alert-danger'>Current password is incorrect!</div>";
    } elseif($newPassword != $confirmPassword) {
        $errorMsg = "<div class='alert alert-danger'>New passwords do not match!</div>";
    } else {
        $updatePassQuery = mysqli_query($conn, "UPDATE tbladmin SET password = '$newPassword' WHERE Id = '$adminId'");
        if($updatePassQuery) {
            $successMsg = "<div class='alert alert-success'>Password changed successfully!</div>";
        } else {
            $errorMsg = "<div class='alert alert-danger'>Error changing password: ".mysqli_error($conn)."</div>";
        }
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
  <title>Admin Profile</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .profile-card {
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .profile-header {
      background: linear-gradient(135deg, #6777ef, #3511ad);
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 20px;
      text-align: center;
    }
    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 5px solid white;
      margin: 0 auto;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: #6777ef;
    }
    .profile-body {
      padding: 30px;
    }
    .form-control:focus {
      border-color: #6777ef;
      box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
    }
    .btn-primary {
      background-color: #6777ef;
      border-color: #6777ef;
    }
    .btn-primary:hover {
      background-color: #5166ea;
      border-color: #5166ea;
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
            <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card profile-card mb-4">
                <div class="profile-header">
                  <div class="profile-avatar">
                    <?php echo strtoupper(substr($adminData['firstName'], 0, 1).substr($adminData['lastName'], 0, 1)); ?>
                  </div>
                  <h4 class="mt-3"><?php echo $adminData['firstName'].' '.$adminData['lastName']; ?></h4>
                  <p class="text-light">Administrator</p>
                </div>
                <div class="profile-body">
                  <?php 
                  if(isset($successMsg)) { echo $successMsg; }
                  if(isset($errorMsg)) { echo $errorMsg; }
                  ?>
                  
                  <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">Profile Information</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">Change Password</a>
                    </li>
                  </ul>
                  
                  <div class="tab-content mt-4" id="profileTabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                      <form method="POST" action="">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">First Name</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" name="firstName" value="<?php echo $adminData['firstName']; ?>" required>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Last Name</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" name="lastName" value="<?php echo $adminData['lastName']; ?>" required>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Email</label>
                          <div class="col-sm-9">
                            <input type="email" class="form-control" name="email" value="<?php echo $adminData['emailAddress']; ?>" required>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <div class="col-sm-12 text-right">
                            <button type="submit" name="updateProfile" class="btn btn-primary">Update Profile</button>
                          </div>
                        </div>
                      </form>
                    </div>
                    
                    <!-- Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                      <form method="POST" action="">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Current Password</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" name="currentPassword" required>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">New Password</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" name="newPassword" required>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">Confirm Password</label>
                          <div class="col-sm-9">
                            <input type="password" class="form-control" name="confirmPassword" required>
                          </div>
                        </div>
                        
                        <div class="form-group row">
                          <div class="col-sm-12 text-right">
                            <button type="submit" name="changePassword" class="btn btn-primary">Change Password</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
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
      $('input[name="newPassword"], input[name="confirmPassword"]').on('keyup', function() {
        if($('input[name="newPassword"]').val() != '' && $('input[name="confirmPassword"]').val() != '') {
          if($('input[name="newPassword"]').val() == $('input[name="confirmPassword"]').val()) {
            $('input[name="confirmPassword"]').removeClass('is-invalid');
            $('input[name="confirmPassword"]').addClass('is-valid');
          } else {
            $('input[name="confirmPassword"]').removeClass('is-valid');
            $('input[name="confirmPassword"]').addClass('is-invalid');
          }
        }
      });
    });
  </script>
</body>
</html>