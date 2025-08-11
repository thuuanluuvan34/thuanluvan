<?php
// Bắt đầu bộ đệm đầu ra để đảm bảo hàm header() hoạt động
ob_start();

// Bật hiển thị lỗi để dễ dàng debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Đảm bảo không có khoảng trắng nào trước thẻ này
require_once 'db_connect.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $user = null;
    $role = null;

    $roles_tables = [
        'admin' => ['table' => 'admin', 'id_col' => 'admin_id', 'role_id' => 1],
        'teacher' => ['table' => 'teacher', 'id_col' => 'teacher_id', 'role_id' => 2],
        'student' => ['table' => 'student', 'id_col' => 'student_id', 'role_id' => 3]
    ];

    foreach ($roles_tables as $role_name => $role_data) {
        $table = $role_data['table'];
        $id_col = $role_data['id_col'];
        $role_id = $role_data['role_id'];

        $stmt = $conn->prepare("SELECT `$id_col`, email, password FROM `$table` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $role = $role_name;
            $user['id'] = $user[$id_col];
            $user['role_id'] = $role_id;
            $stmt->close();
            break;
        }
        $stmt->close();
    }

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $role;
            $_SESSION['role_id'] = $user['role_id'];

            if ($role === 'admin') {
                header("Location: manager.php");
                exit();
            } else {
                // Kiểm tra xem đường dẫn có đúng không
                $redirect_path = '../main/main.php';
                if (!file_exists($redirect_path)) {
                    $error = "Lỗi: Tệp '{$redirect_path}' không tồn tại. Vui lòng kiểm tra lại đường dẫn.";
                } else {
                    header("Location: {$redirect_path}");
                    exit();
                }
            }
        } else {
            $error = "Mật khẩu không đúng.";
        }
    } else {
        $error = "Email không tồn tại.";
    }
    $conn->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login LMS System</title>
    <style>
        /* CSS không thay đổi */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body, html { height: 100%; background: #f0f4f8; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .login-container { background: white; width: 90vw; max-width: 1200px; padding: 60px 50px; border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,0.1); text-align: center; }
        .login-container img { width: 100px; margin-bottom: 30px; }
        .login-container h2 { color: #333; margin-bottom: 40px; font-weight: 700; letter-spacing: 1.2px; font-size: 32px; }
        .input-group { margin-bottom: 30px; text-align: left; max-width: 700px; margin-left: auto; margin-right: auto; }
        label { display: block; margin-bottom: 10px; color: #555; font-weight: 600; font-size: 16px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 16px 20px; font-size: 18px; border: 1.8px solid #ddd; border-radius: 8px; transition: border-color 0.3s ease; }
        input[type="email"]:focus, input[type="password"]:focus { outline: none; border-color: #0078D7; box-shadow: 0 0 10px rgba(0, 120, 215, 0.3); }
        button { width: 700px; max-width: 100%; background-color: #0078D7; border: none; padding: 18px; color: white; font-weight: 700; font-size: 22px; border-radius: 8px; cursor: pointer; transition: background-color 0.3s ease; margin-top: 10px; display: block; margin-left: auto; margin-right: auto; }
        button:hover { background-color: #005ea2; }
        .error-message { color: #d93025; margin-bottom: 25px; font-weight: 600; font-size: 16px; max-width: 700px; margin-left: auto; margin-right: auto; text-align: center; }
        @media (max-width: 768px) {
            .login-container { width: 95vw; padding: 40px 25px; }
            .input-group, button, .error-message { max-width: 100%; width: 100%; }
            .login-container h2 { font-size: 26px; margin-bottom: 30px; }
            input[type="email"], input[type="password"] { font-size: 16px; padding: 14px 16px; }
            button { font-size: 18px; padding: 14px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="https://cdn-icons-png.flaticon.com/512/1055/1055646.png" alt="LMS Logo" />
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<div class='error-message'>$error</div>"; ?>
        <form method="POST" novalidate>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
// Gửi bộ đệm ra ngoài và kết thúc
ob_end_flush();
?>


