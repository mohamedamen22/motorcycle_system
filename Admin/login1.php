<?php
session_start();
include '../Includes/dbcon.php';

$statusMsg = '';
if(isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' LIMIT 1");
    $user = mysqli_fetch_assoc($query);

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['admin_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $statusMsg = "<div class='alert alert-danger text-center'>Invalid username or password!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Motorcycle System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #4a6baf 0%, #3a56a0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(74,107,175,0.18);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            width: 100%;
        }
        .login-card .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 18px auto;
            display: block;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(74,107,175,0.10);
        }
        .login-card h3 {
            font-weight: 700;
            color: #3a56a0;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .btn-login {
            background: linear-gradient(120deg, #4a6baf 0%, #3a56a0 100%);
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.08rem;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: linear-gradient(120deg, #3a56a0 0%, #4a6baf 100%);
            color: #fff;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.98rem;
            margin-bottom: 1.2rem;
        }
        @media (max-width: 480px) {
            .login-card { padding: 1.5rem 0.7rem; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="img/logo/attnlg.jpg" alt="Logo" class="logo">
        <h3>Admin Login</h3>
        <?php if($statusMsg) echo $statusMsg; ?>
        <form method="post" autocomplete="off">
            <div class="form-group mb-3">
                <label for="username"><i class="fas fa-user mr-2"></i>Username</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="form-group mb-4">
                <label for="password"><i class="fas fa-lock mr-2"></i>Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-login btn-block">
                <i class="fas fa-sign-in-alt mr-2"></i> Gal paga
            </button>
        </form>
    </div>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>