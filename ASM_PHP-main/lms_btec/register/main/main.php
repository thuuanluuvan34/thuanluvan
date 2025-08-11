<?php
session_start();
$page = $_GET['page'] ?? 'home';

// Kiểm tra nếu chưa đăng nhập thì quay lại login
if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: ../register/login.php");
    exit;
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>BTEC LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="stylee.css">
    <style>
       
    </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    
        <div class="sidebar-logo">
            <i class="fas fa-school"></i>
            <span>BTEC</span>
        </div>
    <a href="main.php?page=home" class="<?= ($page === 'home') ? 'active' : '' ?>">🏠 Home</a>
    <a href="courses.php" class="<?= ($page === 'courses') ? 'active' : '' ?>">📚 Courses</a>
    <a href="settings.php" class="<?= ($page === 'settings') ? 'active' : '' ?>">⚙️ Settings</a>
    <a href="logout.php" class="<?= ($page === 'logout') ? 'active' : '' ?>">🚪 Logout</a>
  </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar -->
        <div class="inner-topbar">
            <div class="page-title">Home</div>
            <div class="right-icons">
                <div class="icon">
                    <i class="fas fa-bell"></i>
                    <div class="notification-count">0</div>
                </div>
                <div class="avatar"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="welcome-msg">
                👋 Welcome to <?= $userRole === 'teacher' ? 'Giảng viên' : 'Student' ?>, <b><?= htmlspecialchars($userEmail) ?></b>!
            </div>

            <?php
            $allowed_pages = ['home', 'courses', 'settings', 'logout'];
            $file = "pages/$page.php";
            if (in_array($page, $allowed_pages) && file_exists($file)) {
                include($file);
            } else {
                echo "<h2>Welcome to the LMS Home Page</h2>";
            }
            ?>
        </div>

    </div>
</div>
</body>
</html>
