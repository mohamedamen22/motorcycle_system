<?php 
include 'Includes/dbcon.php';
session_start();

$dateToday = date('l, F j, Y');
$attendanceCount = 0;

if (isset($_SESSION['userId']) && isset($_SESSION['classId'])) {
    $classId = $_SESSION['classId'];
    $classArmId = $_SESSION['classArmId'];
    $teacherId = $_SESSION['userId'];

    $query = "SELECT COUNT(*) as total FROM tblattendance WHERE classId = '$classId' AND classArmId = '$classArmId' AND createdBy = '$teacherId'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $attendanceCount = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moumin Group </title>
    <link href="img/logo/images.jpg" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --white: #ffffff;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: var(--dark-color);
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
            background: url('img/logo/pic.jpg') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(67, 97, 238, 0.85);
            z-index: 0;
        }

        .date-time-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            color: var(--white);
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
            backdrop-filter: blur(5px);
        }

        .date-time-box p {
            margin: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .date-time-box p strong {
            margin-right: 8px;
            font-weight: 500;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            text-align: center;
            transition: var(--transition);
            animation: fadeIn 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--gradient);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h5 {
            color: var(--primary-color);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-card h2 {
            color: var(--dark-color);
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            line-height: 1.3;
        }

        .logo-container {
            margin-bottom: 25px;
            position: relative;
        }

        .logo-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .logo-container::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }

        .login-card h1 {
            color: var(--dark-color);
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
        }

        .login-card h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gradient);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
            font-size: 14px;
        }

        .form-select, .form-group input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--white);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        .form-select:focus, .form-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(72, 149, 239, 0.2);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3f37c9 0%, #4361ee 100%);
            transition: var(--transition);
            z-index: -1;
        }

        .btn:hover::before {
            left: 0;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            font-size: 14px;
        }

        .login-options a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .login-options a i {
            margin-right: 6px;
            font-size: 13px;
        }

        .login-options a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        .alert {
            margin-top: 20px;
            padding: 14px;
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffeeba;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 10px;
            font-size: 16px;
        }

        .language-selector {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }

        .language-btn {
            background: var(--white);
            color: var(--primary-color);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .language-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 30px;
            }
            
            .date-time-box {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 15px;
            }
            
            .date-time-box p {
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body> 
    
<div class="date-time-box">
    <p><strong><i class="far fa-calendar-alt"></i> Today:</strong> <?php echo $dateToday; ?></p>
    <p><strong><i class="far fa-clock"></i> Time:</strong> <span id="liveClock"></span></p>
</div>

<div class="login-container">
    <div class="login-card">
        <h5>Moumin Group of Companies</h5>
        <div class="logo-container">
            <img src="img/logo/images.jpg"alt="School Logo">
        </div>
        <h2>Moumin Group of Companies</h2>
        <h1>Login Panel</h1>

        <form method="POST" action="">
            <div class="form-group">
                <label for="userType"><i class="fas fa-user-tag"></i> User Role</label>
                <select required name="userType" class="form-select" id="userType">
                    <option value="">-- Select User Role --</option>
                    <option value="Administrator">Administrator</option>
                    <option value="ClassTeacher">users </option>
                   
                </select>
            </div>

            <div class="form-group">
                <label for="username"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="text" name="username" id="username" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <input type="submit" class="btn" value="Login" name="login">
            
            <div class="login-options">
                <a href="#"><i class="fas fa-key"></i> Forgot Password?</a>
                <a href="#"><i class="fas fa-headset"></i> Support</a>
            </div>
        </form>
<!-- Add this inside your form, before the submit button -->
<div class="form-group">
    <label><i class="fas fa-fingerprint"></i> Authentication Method</label>
    <div class="auth-methods">
        <div class="auth-option" onclick="initiateFaceAuth()">
            <i class="fas fa-user-circle"></i>
            <span>Face Recognition</span>
        </div>
        <div class="auth-option" onclick="initiateFingerprintAuth()">
            <i class="fas fa-fingerprint"></i>
            <span>Fingerprint</span>
        </div>
    </div>
</div>

<!-- Add this modal for face recognition -->
<div id="faceAuthModal" class="auth-modal">
    <div class="auth-modal-content">
        <span class="close-modal" onclick="closeModal('faceAuthModal')">&times;</span>
        <h3><i class="fas fa-user-circle"></i> Face Recognition</h3>
        <div class="camera-container">
            <video id="video" width="320" height="240" autoplay></video>
            <canvas id="canvas" width="320" height="240"></canvas>
        </div>
        <button class="btn" onclick="captureFace()">Capture</button>
        <div id="faceAuthStatus"></div>
    </div>
</div>

<!-- Add this modal for fingerprint -->
<div id="fingerprintModal" class="auth-modal">
    <div class="auth-modal-content">
        <span class="close-modal" onclick="closeModal('fingerprintModal')">&times;</span>
        <h3><i class="fas fa-fingerprint"></i> Fingerprint Authentication</h3>
        <div class="fingerprint-icon">
            <i class="fas fa-fingerprint"></i>
        </div>
        <div id="fingerprintStatus">Place your finger on the scanner</div>
        <button class="btn" onclick="simulateFingerprint()">Authenticate</button>
    </div>
</div>

<!-- Add this CSS to your style section -->
<style>
    .auth-methods {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }
    
    .auth-option {
        flex: 1;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .auth-option:hover {
        border-color: var(--accent-color);
        background-color: rgba(72, 149, 239, 0.1);
    }
    
    .auth-option i {
        font-size: 24px;
        margin-bottom: 8px;
        color: var(--primary-color);
    }
    
    .auth-option span {
        font-size: 14px;
        font-weight: 500;
    }
    
    .auth-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
    }
    
    .auth-modal-content {
        background-color: var(--white);
        margin: 5% auto;
        padding: 25px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        position: relative;
    }
    
    .close-modal {
        position: absolute;
        right: 20px;
        top: 15px;
        font-size: 24px;
        cursor: pointer;
    }
    
    .camera-container {
        position: relative;
        margin: 15px 0;
    }
    
    #canvas {
        position: absolute;
        top: 0;
        left: 0;
        display: none;
    }
    
    .fingerprint-icon {
        text-align: center;
        margin: 20px 0;
    }
    
    .fingerprint-icon i {
        font-size: 60px;
        color: var(--primary-color);
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    #faceAuthStatus, #fingerprintStatus {
        margin: 15px 0;
        text-align: center;
        font-size: 14px;
        min-height: 20px;
    }
</style>

<!-- Add this JavaScript -->
<script>
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Face recognition functions
    function initiateFaceAuth() {
        openModal('faceAuthModal');
        startCamera();
    }
    
    function startCamera() {
        const video = document.getElementById('video');
        
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    document.getElementById('faceAuthStatus').innerHTML = 
                        'Error accessing camera: ' + err.message;
                });
        } else {
            document.getElementById('faceAuthStatus').innerHTML = 
                'Camera access not supported by your browser';
        }
    }
    
    function captureFace() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        
        // Draw the current video frame to the canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Show the captured image
        canvas.style.display = 'block';
        video.style.display = 'none';
        
        // Here you would normally send the image to your backend for verification
        // For demo purposes, we'll simulate a successful verification
        document.getElementById('faceAuthStatus').innerHTML = 
            '<i class="fas fa-spinner fa-spin"></i> Verifying face...';
        
        setTimeout(() => {
            // Simulate successful verification
            document.getElementById('faceAuthStatus').innerHTML = 
                '<span style="color:green"><i class="fas fa-check-circle"></i> Face recognized!</span>';
            
            // In a real app, you would submit the form here or auto-login
            setTimeout(() => {
                closeModal('faceAuthModal');
                alert('Face authentication successful! Proceeding with login...');
            }, 1000);
        }, 2000);
    }
    
    // Fingerprint functions
    function initiateFingerprintAuth() {
        openModal('fingerprintModal');
    }
    
    function simulateFingerprint() {
        document.getElementById('fingerprintStatus').innerHTML = 
            '<i class="fas fa-spinner fa-spin"></i> Scanning fingerprint...';
        
        setTimeout(() => {
            // Simulate successful fingerprint scan
            document.getElementById('fingerprintStatus').innerHTML = 
                '<span style="color:green"><i class="fas fa-check-circle"></i> Fingerprint verified!</span>';
            
            // In a real app, you would submit the form here or auto-login
            setTimeout(() => {
                closeModal('fingerprintModal');
                alert('Fingerprint authentication successful! Proceeding with login...');
            }, 1000);
        }, 2000);
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'auth-modal') {
            event.target.style.display = 'none';
        }
    }
</script>
        <?php
        if(isset($_POST['login'])){
            $userType = $_POST['userType'];
            $username = $_POST['username'];
            $password = md5($_POST['password']);

            if($userType == "Administrator"){
                $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
                $rs = $conn->query($query);
                $num = $rs->num_rows;
                $rows = $rs->fetch_assoc();

                if($num > 0){
                    $_SESSION['userId'] = $rows['Id'];
                    $_SESSION['firstName'] = $rows['firstName'];
                    $_SESSION['lastName'] = $rows['lastName'];
                    $_SESSION['emailAddress'] = $rows['emailAddress'];
                    $_SESSION['userType'] = 'Administrator';

                    echo "<script>window.location = 'Admin/index.php';</script>";
                } else {
                    echo "<div class='alert'><i class='fas fa-exclamation-circle'></i> Invalid Username/Password!</div>";
                }
            }
            elseif($userType == "ClassTeacher"){
                $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
                $rs = $conn->query($query);
                $num = $rs->num_rows;
                $rows = $rs->fetch_assoc();

                if($num > 0){
                    $_SESSION['userId'] = $rows['Id'];
                    $_SESSION['firstName'] = $rows['firstName'];
                    $_SESSION['lastName'] = $rows['lastName'];
                    $_SESSION['emailAddress'] = $rows['emailAddress'];
                    $_SESSION['classId'] = $rows['classId'];
                    $_SESSION['classArmId'] = $rows['classArmId'];
                    $_SESSION['userType'] = 'ClassTeacher';

                    echo "<script>window.location = 'ClassTeacher/index.php';</script>";
                } else {
                    echo "<div class='alert'><i class='fas fa-exclamation-circle'></i> Invalid Username/Password!</div>";
                }
            }
            elseif($userType == "ExamTeacher"){
                $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
                $rs = $conn->query($query);
                $num = $rs->num_rows;
                $rows = $rs->fetch_assoc();

                if($num > 0){
                    $_SESSION['userId'] = $rows['Id'];
                    $_SESSION['firstName'] = $rows['firstName'];
                    $_SESSION['lastName'] = $rows['lastName'];
                    $_SESSION['emailAddress'] = $rows['emailAddress'];
                    $_SESSION['classId'] = $rows['classId'];
                    $_SESSION['classArmId'] = $rows['classArmId'];
                    $_SESSION['userType'] = 'ExamTeacher';

                    echo "<script>window.location = 'ExamTeacher/index.php';</script>";
                } else {
                    echo "<div class='alert'><i class='fas fa-exclamation-circle'></i> Invalid Username/Password!</div>";
                }
            } else {
                echo "<div class='alert'><i class='fas fa-exclamation-circle'></i> Invalid User Role!</div>";
            }
        }
        ?>
    </div>
</div>

<div class="language-selector">
    <button class="language-btn" title="Change Language">
        <i class="fas fa-language"></i>
    </button>
</div>

<!-- Live Clock Script -->
<script>
    function updateClock() {
        const now = new Date();
        const options = { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        };
        const timeString = now.toLocaleTimeString('en-US', options);
        document.getElementById('liveClock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

</body>
</html>