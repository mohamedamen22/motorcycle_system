<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Enhanced permission check
if($_SESSION['userType'] != 'Administrator') {
    $_SESSION['error'] = "You don't have permission to access this page";
    header("Location: unauthorized.php");
    exit();
}

// Create new user with enhanced validation
if(isset($_POST['createUser'])) {
    // Sanitize inputs
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lastName']));
    $email = mysqli_real_escape_string($conn, strtolower(trim($_POST['email'])));
    $userType = mysqli_real_escape_string($conn, $_POST['userType']);
    
    // Additional fields for teachers
    $classId = isset($_POST['classId']) ? mysqli_real_escape_string($conn, $_POST['classId']) : NULL;
    $classArmId = isset($_POST['classArmId']) ? mysqli_real_escape_string($conn, $_POST['classArmId']) : NULL;
    
    // Generate random password
    $randomPassword = bin2hex(random_bytes(4)); // 8 character random password
    $password = password_hash($randomPassword, PASSWORD_DEFAULT);
    
    // Validate inputs
    $errors = [];
    
    if(empty($firstName) || strlen($firstName) < 2) {
        $errors[] = "First name must be at least 2 characters";
    }
    
    if(empty($lastName) || strlen($lastName) < 2) {
        $errors[] = "Last name must be at least 2 characters";
    }
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($userType) {
        $errors[] = "User type is required";
    }
    
    // Additional validation for teachers
    if(in_array($userType, ['ClassTeacher', 'ExamTeacher']) && (empty($classId) || empty($classArmId))) {
        $errors[] = "Class and class arm are required for teachers";
    }
    
    // Check if email exists
    $checkQuery = mysqli_query($conn, "SELECT * FROM tbladmin WHERE emailAddress = '$email'");
    if(mysqli_num_rows($checkQuery) {
        $errors[] = "Email address already exists";
    }
    
    if(empty($errors)) {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert into admin table
            $insertAdmin = mysqli_query($conn, "INSERT INTO tbladmin 
                (firstName, lastName, emailAddress, password, userType) 
                VALUES ('$firstName', '$lastName', '$email', '$password', '$userType')");
            
            if(!$insertAdmin) {
                throw new Exception("Error creating user: ".mysqli_error($conn));
            }
            
            $userId = mysqli_insert_id($conn);
            
            // If teacher, insert into teacher table
            if(in_array($userType, ['ClassTeacher', 'ExamTeacher'])) {
                $insertTeacher = mysqli_query($conn, "INSERT INTO tblclassteacher
                    (userId, firstName, lastName, emailAddress, classId, classArmId)
                    VALUES ('$userId', '$firstName', '$lastName', '$email', '$classId', '$classArmId')");
                
                if(!$insertTeacher) {
                    throw new Exception("Error creating teacher record: ".mysqli_error($conn));
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            $_SESSION['success'] = "User created successfully! Temporary password: $randomPassword";
            header("Location: create_user.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = $e->getMessage();
            header("Location: create_user.php");
            exit();
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: create_user.php");
        exit();
    }
}

// Get classes for dropdown
$classes = [];
$classArms = [];
$classQuery = mysqli_query($conn, "SELECT * FROM tblclass");
while($row = mysqli_fetch_assoc($classQuery)) {
    $classes[$row['Id']] = $row['className'];
}

$armQuery = mysqli_query($conn, "SELECT * FROM tblclassarms");
while($row = mysqli_fetch_assoc($armQuery)) {
    $classArms[$row['Id']] = $row['classArmName'];
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
      transition: all 0.3s ease;
    }
    
    .create-user-card:hover {
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      transform: translateY(-5px);
    }
    
    .create-user-header {
      background: linear-gradient(135deg, #6777ef, #3511ad);
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 25px;
      text-align: center;
    }
    
    .create-user-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 5px solid white;
      margin: 0 auto 15px;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 36px;
      color: #6777ef;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background-color: #5166ea;
      border-color: #5166ea;
      transform: translateY(-2px);
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
    
    .teacher-fields {
      display: none;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .password-generator {
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .password-generator:hover {
      color: #6777ef;
      transform: scale(1.1);
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
                  // Display messages
                  if(isset($_SESSION['success'])) {
                      echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
                      unset($_SESSION['success']);
                  }
                  
                  if(isset($_SESSION['error'])) {
                      echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                      unset($_SESSION['error']);
                  }
                  ?>
                  
                  <form method="POST" action="" id="userForm">
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">First Name <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($_POST['firstName'] ?? ''); ?>" required minlength="2">
                        <small class="form-text text-muted">Minimum 2 characters</small>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Last Name <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($_POST['lastName'] ?? ''); ?>" required minlength="2">
                        <small class="form-text text-muted">Minimum 2 characters</small>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Email Address <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <small class="form-text text-muted">This will be used for login</small>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">User Type <span class="text-danger">*</span></label>
                      <div class="col-sm-9">
                        <select class="form-control" name="userType" id="userType" required>
                          <option value="">-- Select User Type --</option>
                          <option value="Administrator" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'Administrator' ? 'selected' : ''); ?>>Administrator</option>
                          <option value="ClassTeacher" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'ClassTeacher' ? 'selected' : ''); ?>>Class Teacher</option>
                          <option value="ExamTeacher" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'ExamTeacher' ? 'selected' : ''); ?>>Exam Teacher</option>
                        </select>
                      </div>
                    </div>
                    
                    <!-- Teacher-specific fields -->
                    <div id="teacherFields" class="teacher-fields">
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Class <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                          <select class="form-control" name="classId" id="classId">
                            <option value="">-- Select Class --</option>
                            <?php foreach($classes as $id => $name): ?>
                              <option value="<?php echo $id; ?>" <?php echo (isset($_POST['classId']) && $_POST['classId'] == $id ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($name); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      
                      <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Class Arm <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                          <select class="form-control" name="classArmId" id="classArmId">
                            <option value="">-- Select Class Arm --</option>
                            <?php foreach($classArms as $id => $name): ?>
                              <option value="<?php echo $id; ?>" <?php echo (isset($_POST['classArmId']) && $_POST['classArmId'] == $id ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($name); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12">
                        <div class="alert alert-info">
                          <i class="fas fa-info-circle"></i> 
                          <strong>Password Information:</strong> 
                          A random password will be generated automatically and displayed after account creation. 
                          User should change this after first login.
                          <span class="float-right password-generator" title="Generate Password">
                            <i class="fas fa-key"></i>
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="col-sm-12 text-right">
                        <button type="reset" class="btn btn-secondary mr-2">
                          <i class="fas fa-undo mr-2"></i> Reset
                        </button>
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
      // Show/hide teacher fields based on user type
      $('#userType').change(function() {
        if($(this).val() === 'ClassTeacher' || $(this).val() === 'ExamTeacher') {
          $('#teacherFields').slideDown();
          $('#classId, #classArmId').prop('required', true);
        } else {
          $('#teacherFields').slideUp();
          $('#classId, #classArmId').prop('required', false);
        }
      });
      
      // Trigger change event on page load if teacher type is selected
      if($('#userType').val() === 'ClassTeacher' || $('#userType').val() === 'ExamTeacher') {
        $('#teacherFields').show();
        $('#classId, #classArmId').prop('required', true);
      }
      
      // Form validation
      $('#userForm').on('submit', function(e) {
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
        
        // Check name lengths
        if($('input[name="firstName"]').val().length < 2) {
          $('input[name="firstName"]').addClass('is-invalid');
          valid = false;
        }
        
        if($('input[name="lastName"]').val().length < 2) {
          $('input[name="lastName"]').addClass('is-invalid');
          valid = false;
        }
        
        if(!valid) {
          e.preventDefault();
          $('.alert-danger').remove();
          $('.create-user-body').prepend(
            '<div class="alert alert-danger">Please fill all required fields correctly!</div>'
          );
          
          // Scroll to first error
          $('html, body').animate({
            scrollTop: $('.is-invalid').first().offset().top - 100
          }, 500);
        }
      });
      
      // Password generator demo (just for show)
      $('.password-generator').click(function() {
        const randomPass = Math.random().toString(36).slice(-8);
        alert('Demo: A password like this will be generated:\n\n' + randomPass);
      });
    });
  </script>
</body>
</html>