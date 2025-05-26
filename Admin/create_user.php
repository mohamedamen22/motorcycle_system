<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check if admin has permission to create users
if($_SESSION['userType'] != 'Administrator') {
    header("Location: unauthorized.php");
    exit();
}

// Create new user
if(isset($_POST['createUser'])) {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $userType = mysqli_real_escape_string($conn, $_POST['userType']);
    $password = md5('default123'); // Default password
    
    // Check if email already exists
    $checkQuery = mysqli_query($conn, "SELECT * FROM tbladmin WHERE emailAddress = '$email'");
    if(mysqli_num_rows($checkQuery) > 0) {
        $errorMsg = "<div class='alert alert-danger'>Email address already exists!</div>";
    } else {
        $insertQuery = mysqli_query($conn, "INSERT INTO tbladmin 
            (firstName, lastName, emailAddress, password, userType) 
            VALUES ('$firstName', '$lastName', '$email', '$password', '$userType')");
        
        if($insertQuery) {
            $successMsg = "<div class='alert alert-success'>User created successfully! Default password: default123</div>";
            // Clear form
            $_POST = array();
        } else {
            $errorMsg = "<div class='alert alert-danger'>Error creating user: ".mysqli_error($conn)."</div>";
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
  <title>Create User Account</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .create-user-card {
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .create-user-header {
      background: linear-gradient(135deg, #6777ef, #3511ad);
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 20px;
      text-align: center;
    }
    .create-user-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 5px solid white;
      margin: 0 auto;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 36px;
      color: #6777ef;
    }
    .create-user-body {
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
    .user-type-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    .badge-admin {
      background-color: rgba(103, 119, 239, 0.2);
      color: #6777ef;
    }
    .badge-teacher {
      background-color: rgba(40, 167, 69, 0.2);
      color: #28a745;
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
            <h1 class="h3 mb-0 text-gray-800">Create User Account</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create User</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card create-user-card mb-4">
                <div class="create-user-header">
                  <div class="create-user-icon">
                    <i class="fas fa-user-plus"></i>
                  </div>
                  <h4 class="mt-3">Create New User Account</h4>
                  <p class="text-light">Add new administrator or teacher accounts</p>
                </div>
                <div class="create-user-body">
                  <?php 
                  if(isset($successMsg)) { echo $successMsg; }
                  if(isset($errorMsg)) { echo $errorMsg; }
                  ?>
                  
                  <form method="POST" action="">
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">First Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="firstName" value="<?php echo $_POST['firstName'] ?? ''; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Last Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="lastName" value="<?php echo $_POST['lastName'] ?? ''; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Email Address</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                        <small class="form-text text-muted">This will be used for login</small>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">User Type</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="userType" required>
                          <option value="">-- Select User Type --</option>
                          <option value="Administrator" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'Administrator' ? 'selected' : ''); ?>>Administrator</option>
                          <option value="ClassTeacher" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'ClassTeacher' ? 'selected' : ''); ?>>Class Teacher</option>
                          <option value="ExamTeacher" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'ExamTeacher' ? 'selected' : ''); ?>>Exam Teacher</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12">
                        <div class="alert alert-info">
                          <i class="fas fa-info-circle"></i> <strong>Default Password:</strong> "default123" - User should change this after first login
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12 text-right">
                        <button type="submit" name="createUser" class="btn btn-primary">
                          <i class="fas fa-user-plus mr-2"></i> Create User Account
                        </button>
                      </div>
                    </div>
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
    $(document).ready(function() {
      // Form validation
      $('form').on('submit', function(e) {
        let valid = true;
        
        // Check empty fields
        $(this).find('input[required], select[required]').each(function() {
          if($(this).val() === '') {
            $(this).addClass('is-invalid');
            valid = false;
          } else {
            $(this).removeClass('is-invalid');
          }
        });
        
        // Check email format
        const email = $('input[name="email"]').val();
        if(email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          $('input[name="email"]').addClass('is-invalid');
          valid = false;
        }
        
        if(!valid) {
          e.preventDefault();
          $('.alert-danger').remove();
          $('.create-user-body').prepend(
            '<div class="alert alert-danger">Please fill all required fields correctly!</div>'
          );
        }
      });
    });
  </script>
</body>
</html>