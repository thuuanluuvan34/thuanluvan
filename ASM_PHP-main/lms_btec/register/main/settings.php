<?php
session_start();
include '../register/db_connect.php';

// Kiểm tra session tồn tại
if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];

// Biến lưu thông báo
$message = '';
$messageType = '';

// Lấy thông báo từ URL nếu có
if (isset($_GET['message']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = htmlspecialchars($_GET['type']);
}

// Xử lý cập nhật tài khoản khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentEmail = $_SESSION['email'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];
    $fullName = $_POST['full_name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';

    $update_query = "";
    $params = [];
    $types = "";

    // Xây dựng truy vấn cập nhật dựa trên vai trò
    switch ($userRole) {
        case 'admin':
            $update_query = "UPDATE admin SET email = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
            $types = "ssssss";
            $params = [$newEmail, $fullName, $dob, $address, $gender, $currentEmail];
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update_query = "UPDATE admin SET email = ?, password = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
                $types = "sssssss";
                $params = [$newEmail, $hashedPassword, $fullName, $dob, $address, $gender, $currentEmail];
            }
            break;
        case 'teacher':
            $update_query = "UPDATE teacher SET email = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
            $types = "ssssss";
            $params = [$newEmail, $fullName, $dob, $address, $gender, $currentEmail];
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update_query = "UPDATE teacher SET email = ?, password = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
                $types = "sssssss";
                $params = [$newEmail, $hashedPassword, $fullName, $dob, $address, $gender, $currentEmail];
            }
            break;
        case 'student':
            $update_query = "UPDATE student SET email = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
            $types = "ssssss";
            $params = [$newEmail, $fullName, $dob, $address, $gender, $currentEmail];
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update_query = "UPDATE student SET email = ?, password = ?, full_name = ?, dob = ?, address = ?, gender = ? WHERE email = ?";
                $types = "sssssss";
                $params = [$newEmail, $hashedPassword, $fullName, $dob, $address, $gender, $currentEmail];
            }
            break;
        default:
            header("Location: settings.php?message=Lỗi: Vai trò không hợp lệ&type=error");
            exit;
    }

    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            if ($newEmail !== $currentEmail) {
                $_SESSION['email'] = $newEmail;
            }
            $message = 'Cập nhật thành công';
            $messageType = 'success';
        } else {
            $message = 'Cập nhật thất bại: ' . $stmt->error;
            $messageType = 'error';
        }
        $stmt->close();
    } else {
        $message = 'Lỗi chuẩn bị truy vấn';
        $messageType = 'error';
    }
}

// Lấy thông tin người dùng để hiển thị sau khi có thể đã cập nhật
$userData = null;
switch ($userRole) {
    case 'admin':
        $query = "SELECT admin_id AS id, email, full_name, dob, address, gender FROM admin WHERE email = ?";
        break;
    case 'teacher':
        $query = "SELECT teacher_id AS id, email, full_name, dob, address, gender FROM teacher WHERE email = ?";
        break;
    case 'student':
        $query = "SELECT student_id AS id, email, full_name, dob, address, gender FROM student WHERE email = ?";
        break;
    default:
        die("Không xác định được vai trò.");
}

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Lỗi truy vấn: " . $conn->error);
}

// Function để hiển thị thông báo
function display_message($message, $type) {
    if (!empty($message)) {
        $class = '';
        switch ($type) {
            case 'success':
                $class = 'message-success';
                break;
            case 'error':
                $class = 'message-error';
                break;
            default:
                $class = '';
                break;
        }
        echo "<p class='message-box {$class}'>{$message}</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BTEC LMS - Cài đặt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="stylee.css">
    <style>
        
    </style>
</head>

<body>
<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-school"></i>
            <span>BTEC Logo</span>
        </div>
        <a href="main.php?page=home">🏠 Home</a>
        <a href="courses.php">📚 Courses</a>
        <a href="settings.php" class="active">⚙️ Settings</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar -->
        <div class="inner-topbar">
            <div class="page-title">Account information</div>
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
            <?php display_message($message, $messageType); ?>
            <form action="" method="post" class="account-form">
                <table>
                    <tr>
                        <td><label for="id">ID:</label></td>
                        <td><input type="text" name="id" id="id" value="<?= htmlspecialchars($userData['id']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input type="email" name="email" id="email" value="<?= htmlspecialchars($userData['email']); ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="password">Mật khẩu:</label></td>
                        <td><input type="password" name="password" id="password" placeholder="Nhập mật khẩu mới (nếu muốn)"></td>
                    </tr>

                    <?php if ($userRole === 'teacher' || $userRole === 'student' || $userRole === 'admin'): ?>
                    <tr>
                        <td><label for="full_name">Họ tên:</label></td>
                        <td><input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($userData['full_name'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="dob">Ngày sinh:</label></td>
                        <td><input type="date" name="dob" id="dob" value="<?= htmlspecialchars($userData['dob'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="address">Địa chỉ:</label></td>
                        <td><input type="text" name="address" id="address" value="<?= htmlspecialchars($userData['address'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="gender">Giới tính:</label></td>
                        <td>
                            <select name="gender" id="gender">
                                <option value="Nam" <?= ($userData['gender'] ?? '') === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?= ($userData['gender'] ?? '') === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?= ($userData['gender'] ?? '') === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr>
                        <td><label for="role">Vai trò:</label></td>
                        <td><input type="text" name="role" id="role" value="<?= ucfirst($userRole); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <button type="submit">Cập nhật</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
</body>
</html>
