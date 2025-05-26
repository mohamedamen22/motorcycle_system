<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moumin Group</title>
    <link href="img/logo/images.jpg" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color:rgb(49, 98, 154);
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --white: #ffffff;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --gradient-accent: linear-gradient(135deg, #4895ef 0%, #4361ee 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--white);
            line-height: 1.7;
            min-height: 100vh;
            background: url('img/logo/istock-car-parts.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(67, 98, 238, 0.27) 0%, rgb(57, 12, 163) 100%);
            z-index: 0;
        }

        .welcome-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .welcome-content {
            max-width: 800px;
            animation: fadeInUp 1s ease forwards;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow);
        }

        .school-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .school-logo:hover {
            transform: scale(1.05) rotate(5deg);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .welcome-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .welcome-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 400;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.9);
        }

        .welcome-text {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            opacity: 0.85;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
        }

        .login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--white);
            background: var(--gradient-accent);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(72, 149, 239, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .login-btn:hover::before {
            opacity: 1;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(72, 149, 239, 0.4);
        }

        .login-btn:active {
            transform: translateY(-2px);
        }

        .login-btn i {
            margin-left: 10px;
            transition: var(--transition);
        }

        .login-btn:hover i {
            transform: translateX(5px);
        }

        .date-time-box {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.15);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .date-time-box p {
            margin: 0;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            opacity: 0.9;
        }

        .date-time-box p strong {
            margin-right: 0.5rem;
            font-weight: 500;
        }

        .date-time-box i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(2px);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .welcome-content {
                padding: 2rem;
                width: 90%;
            }
            
            .welcome-title {
                font-size: 2.2rem;
            }
            
            .welcome-subtitle {
                font-size: 1.2rem;
            }
            
            .school-logo {
                width: 120px;
                height: 120px;
            }
            
            .welcome-text {
                font-size: 1rem;
            }
            
            .login-btn {
                padding: 0.9rem 2rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-title {
                font-size: 1.8rem;
            }
            
            .date-time-box {
                flex-direction: column;
                padding: 0.8rem;
                text-align: center;
            }
            
            .date-time-box p {
                margin-bottom: 0.3rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body> 
    
<div class="date-time-box">
    <p><strong><i class="far fa-calendar-alt"></i> Today:</strong> <span id="currentDate"><?php echo date('F j, Y'); ?></span></p>
    <p><strong><i class="far fa-clock"></i> Time:</strong> <span id="liveClock"></span></p>
</div>

<div class="floating-shapes">
    <div class="shape" style="width: 150px; height: 150px; top: 10%; left: 5%; animation: float 8s ease-in-out infinite;"></div>
    <div class="shape" style="width: 100px; height: 100px; top: 70%; left: 80%; animation: float 6s ease-in-out infinite 2s;"></div>
    <div class="shape" style="width: 200px; height: 200px; top: 40%; left: 70%; animation: float 10s ease-in-out infinite 1s;"></div>
    <div class="shape" style="width: 80px; height: 80px; top: 80%; left: 10%; animation: float 7s ease-in-out infinite 1.5s;"></div>
</div>

<div class="welcome-container">
    <div class="welcome-content">
        <img src="img/logo/images.jpg" alt="School Logo" class="school-logo">
        <h1 class="welcome-title">Welcome to Moumin Group of Companies</h1>
        <h2 class="welcome-subtitle">Motorcycle Spare Parts  Management System</h2>
        <p class="welcome-text">
        Our advanced motorcycle spare parts management system ensures efficient inventory control, seamless order tracking, and smooth service operations. Designed for workshops and dealers, it enhances productivity, reduces errors, and keeps your parts business running at full speed.
        Experience the future of motorcycle parts management today.
        </p>
        <a href="login.php" class="login-btn">
            Access Your Account <i class="fas fa-arrow-right"></i>
        </a>
    </div>  
</div>

<script>
    // Live clock function
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
    
    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Animate floating shapes
    document.addEventListener('DOMContentLoaded', function() {
        const shapes = document.querySelectorAll('.shape');
        shapes.forEach(shape => {
            // Randomize initial position and animation duration
            const size = Math.random() * 100 + 50;
            shape.style.width = `${size}px`;
            shape.style.height = `${size}px`;
            shape.style.top = `${Math.random() * 100}%`;
            shape.style.left = `${Math.random() * 100}%`;
            shape.style.animationDuration = `${Math.random() * 5 + 5}s`;
            shape.style.animationDelay = `${Math.random() * 3}s`;
        });
    });
</script>
</body>
</html>