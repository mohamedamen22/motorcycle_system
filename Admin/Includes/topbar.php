<?php 
// Secure database query
$query = "SELECT firstName, lastName FROM tbladmin WHERE Id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rows = $result->fetch_assoc();
    $fullName = htmlspecialchars($rows['firstName']." ".$rows['lastName']);
} else {
    $fullName = "Administrator"; // Fallback if user not found
}
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">
    <!-- Sidebar Toggle -->
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars text-primary"></i>
    </button>
    
    <!-- Center Title -->
    <div class="d-flex align-items-center">
        <div class="text-dark font-weight-bold" style="font-size: 1.25rem;">
            <?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard'); ?>
        </div>
    </div>
    
    <!-- Right Side Navigation -->
    <ul class="navbar-nav ml-auto">
        <!-- Search Dropdown -->
        <li class="nav-item dropdown no-arrow d-none d-md-inline">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw text-gray-600"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown" style="min-width: 300px;">
                <form class="navbar-search" action="search.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control border-0 small" 
                            placeholder="Search for something..."
                            aria-label="Search" style="background-color: #f8f9fa;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>
        
     <!-- Notification Dropdown -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
        <span class="position-relative">
            <i class="fas fa-bell fa-fw text-gray-600"></i>
            <?php if (!empty($consecutiveAbsentStudents)): ?>
                <span class="badge badge-danger badge-counter" style="position: absolute; top: -5px; right: -5px;">
                    <?= count($consecutiveAbsentStudents) ?>+
                </span>
            <?php endif; ?>
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown" style="width: 320px;">
        <h6 class="dropdown-header bg-light py-2">
            Notifications Center
        </h6>
        
        <?php if (!empty($consecutiveAbsentStudents)): ?>
            <!-- Consecutive Absences Notification -->
            <a class="dropdown-item d-flex align-items-center" href="#students-consecutive-absences">
                <div class="mr-3">
                    <div class="icon-circle bg-danger">
                        <i class="fas fa-user-clock text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">Attendance Alert</div>
                    <span class="font-weight-bold">
                        <?= count($consecutiveAbsentStudents) ?> students with 3+ consecutive absences
                    </span>
                </div>
            </a>
        <?php endif; ?>
        
        <!-- Regular Notifications -->
        <a class="dropdown-item d-flex align-items-center" href="#">
            <div class="mr-3">
                <div class="icon-circle bg-primary">
                    <i class="fas fa-user text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-gray-500">Today</div>
                <span class="font-weight-bold">5 new students registered</span>
            </div>
        </a>
        <a class="dropdown-item d-flex align-items-center" href="#">
            <div class="mr-3">
                <div class="icon-circle bg-success">
                    <i class="fas fa-calendar-check text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-gray-500">Yesterday</div>
                New term session starts next week
            </div>
        </a>
        
        <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
    </div>
</li>

<div class="topbar-divider d-none d-sm-block"></div>
        
        <!-- User Dropdown -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <div class="d-flex align-items-center">
                    <img class="img-profile rounded-circle" src="img/user-icn.png" style="width: 40px; height: 40px; object-fit: cover;" alt="User Profile">
                    <span class="ml-2 d-none d-lg-inline text-gray-800 small font-weight-bold"><?php echo $fullName; ?></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" style="border: none;">
                <div class="dropdown-header bg-primary text-white py-2">
                    <h6 class="m-0 font-weight-bold">Account Settings</h6>
                </div>
                <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-primary"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="settings.php">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-primary"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="changePassword.php">
                    <i class="fas fa-key fa-sm fa-fw mr-2 text-primary"></i>
                    Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-fw mr-2 text-danger"></i>
                    Logout
                </a>
                <!-- Alternative with highlighted Create User button -->
<a class="dropdown-item bg-light-success" href="create_user.php" style="background-color: rgba(40, 167, 69, 0.1);">
    <i class="fas fa-user-plus fa-sm fa-fw mr-2 text-success"></i>
    <span class="font-weight-bold text-success">Create User Account</span>
</a>
            </div>
            
        </li>
    </ul>
</nav>

<style>
    .topbar {
        height: 70px;
        background-color: white;
        box-shadow: 0 0.125rem 0.625rem rgba(0, 0, 0, 0.08);
        padding: 0 1.5rem;
    }
    
    .img-profile {
        border: 2px solid #e3e6f0;
        transition: all 0.3s;
    }
    
    .nav-link:hover .img-profile {
        border-color: #4e73df;
    }
    
    .dropdown-menu {
        border-radius: 0.5rem;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        margin-top: 0.5rem;
    }
    
    .dropdown-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .dropdown-item {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #4e73df;
    }
    
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 100%;
    }
    
    .badge-counter {
        font-size: 0.6rem;
        padding: 0.25rem 0.4rem;
    }
    
    @media (max-width: 768px) {
        .topbar {
            padding: 0 1rem;
        }
        
        .navbar-nav .nav-item {
            margin-left: 0.5rem;
        }
        
        .d-none.d-lg-inline {
            display: none !important;
        }
    }
</style>