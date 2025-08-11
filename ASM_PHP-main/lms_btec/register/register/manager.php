<?php
// Bật hiển thị lỗi để dễ dàng debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

// ---------------- Xử lý Đăng xuất ----------------
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Kiểm tra quyền admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Lấy thông báo từ session và xóa sau khi hiển thị
$message = '';
$error = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'users';

// ---------------- Xử lý thêm tài khoản ----------------
if (isset($_POST['add_user'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $table = '';
    $role_id = 0;
    switch ($role) {
        case 'student': $table = 'student'; $role_id = 3; break;
        case 'teacher': $table = 'teacher'; $role_id = 2; break;
        case 'admin': $table = 'admin'; $role_id = 1; break;
        default:
            $_SESSION['error'] = "Vai trò không hợp lệ.";
            header("Location: manager.php?tab=users");
            exit();
    }

    $email_exists = false;
    $check_tables = ['admin', 'teacher', 'student'];
    foreach ($check_tables as $check_table) {
        $check = $conn->prepare("SELECT email FROM `$check_table` WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $email_exists = true;
            break;
        }
        $check->close();
    }

    if ($email_exists) {
        $_SESSION['error'] = "Email đã tồn tại trong hệ thống.";
        header("Location: manager.php?tab=users");
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `$table` (email, password, role_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $hashed_password, $role_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Thêm tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi thêm tài khoản: " . $stmt->error;
        }
        $stmt->close();
        header("Location: manager.php?tab=users");
        exit();
    }
}

// ---------------- Xử lý xóa tài khoản ----------------
if (isset($_GET['delete_user_id']) && isset($_GET['delete_user_role'])) {
    $userId = intval($_GET['delete_user_id']);
    $userRole = $_GET['delete_user_role'];
    $currentAdminId = $_SESSION['user_id'] ?? null;

    if ($userRole === 'admin' && $userId === $currentAdminId) {
        $_SESSION['error'] = "Không thể tự xóa tài khoản của chính mình.";
        header("Location: manager.php?tab=users");
        exit();
    }

    $table = '';
    $id_col = '';
    if ($userRole == 'student') { $table = 'student'; $id_col = 'student_id'; } 
    elseif ($userRole == 'teacher') { $table = 'teacher'; $id_col = 'teacher_id'; } 
    elseif ($userRole == 'admin') { $table = 'admin'; $id_col = 'admin_id'; }

    if (!empty($table) && $userId > 0) {
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$id_col` = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Đã xóa tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa tài khoản: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Thông tin xóa tài khoản không hợp lệ.";
    }
    header("Location: manager.php?tab=users");
    exit();
}

// ---------------- Xử lý sửa tài khoản ----------------
if (isset($_POST['edit_user'])) {
    $userId = intval($_POST['user_id']);
    $currentRole = $_POST['role'];
    $newEmail = trim($_POST['email']);
    $newPassword = trim($_POST['password']);
    $newRole = $_POST['new_role'];

    $currentTable = '';
    $currentIdCol = '';
    if ($currentRole == 'student') { $currentTable = 'student'; $currentIdCol = 'student_id'; } 
    elseif ($currentRole == 'teacher') { $currentTable = 'teacher'; $currentIdCol = 'teacher_id'; } 
    elseif ($currentRole == 'admin') { $currentTable = 'admin'; $currentIdCol = 'admin_id'; }

    if (empty($newEmail) || empty($currentTable) || $userId <= 0) {
        $_SESSION['error'] = "Thông tin sửa tài khoản không hợp lệ.";
        header("Location: manager.php?tab=users");
        exit();
    }

    $original_user = $conn->prepare("SELECT email, password FROM `$currentTable` WHERE `$currentIdCol` = ?");
    $original_user->bind_param("i", $userId);
    $original_user->execute();
    $original_user_data = $original_user->get_result()->fetch_assoc();
    $original_user->close();

    $email_exists = false;
    $check_tables = ['admin', 'teacher', 'student'];
    if ($newEmail !== $original_user_data['email']) {
        foreach ($check_tables as $check_table) {
            $check = $conn->prepare("SELECT email FROM `$check_table` WHERE email = ?");
            $check->bind_param("s", $newEmail);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $email_exists = true;
                break;
            }
            $check->close();
        }
    }

    if ($email_exists) {
        $_SESSION['error'] = "Email đã tồn tại trong hệ thống.";
        header("Location: manager.php?tab=users");
        exit();
    }

    if ($currentRole !== $newRole) {
        // Thay đổi vai trò: Xóa khỏi bảng cũ, thêm vào bảng mới
        $stmt_delete = $conn->prepare("DELETE FROM `$currentTable` WHERE `$currentIdCol` = ?");
        $stmt_delete->bind_param("i", $userId);
        $stmt_delete->execute();
        $stmt_delete->close();

        $newTable = '';
        $newRoleId = 0;
        if ($newRole == 'student') { $newTable = 'student'; $newRoleId = 3; } 
        elseif ($newRole == 'teacher') { $newTable = 'teacher'; $newRoleId = 2; } 
        elseif ($newRole == 'admin') { $newTable = 'admin'; $newRoleId = 1; }
        
        $hashedPassword = !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : $original_user_data['password'];
        $stmt_insert = $conn->prepare("INSERT INTO `$newTable` (email, password, role_id) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("ssi", $newEmail, $hashedPassword, $newRoleId);

        if ($stmt_insert->execute()) {
            $_SESSION['message'] = "Cập nhật tài khoản và thay đổi vai trò thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi thay đổi vai trò: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    } else {
        // Cập nhật thông tin trong cùng một bảng
        $sql = "UPDATE `$currentTable` SET email = ?";
        $params = "s";
        $bind_values = [$newEmail];

        if (!empty($newPassword)) {
            $sql .= ", password = ?";
            $params .= "s";
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $bind_values[] = $hashedPassword;
        }
        
        $sql .= " WHERE `$currentIdCol` = ?";
        $params .= "i";
        $bind_values[] = $userId;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($params, ...$bind_values);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Cập nhật tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật tài khoản: " . $stmt->error;
        }
        $stmt->close();
    }
    
    header("Location: manager.php?tab=users");
    exit();
}

// ---------------- Các đoạn code khác (thêm, sửa, xóa khóa học) không thay đổi ----------------
// ... (Giữ nguyên code từ bạn)
// ---------------- Lấy danh sách tài khoản ----------------
$students = $conn->query("SELECT * FROM student");
$teachers = $conn->query("SELECT * FROM teacher");
$admins   = $conn->query("SELECT * FROM admin");

// ---------------- Lấy danh sách khóa học ----------------
$courses = $conn->query("SELECT c.*, t.email AS teacher_email FROM course c JOIN teacher t ON c.teacher_id = t.teacher_id");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý - LMS</title>
    <style>
        /* CSS không thay đổi */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; display: inline-block; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout-btn { padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background-color 0.3s; }
        .logout-btn:hover { background: #c82333; }
        .tab-buttons { display: flex; justify-content: flex-start; margin-bottom: 20px; }
        .tab-buttons button { padding: 12px 24px; margin-right: 10px; border: none; background: #e9ecef; color: #495057; font-size: 16px; font-weight: bold; cursor: pointer; border-radius: 6px; transition: background-color 0.3s, color 0.3s; }
        .tab-buttons button.active { background: #007bff; color: white; }
        .tab-buttons button:hover { background: #007bff; color: white; }
        .tab-content { padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; margin-top: 10px; }
        form { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        form h2 { width: 100%; margin-top: 0; }
        form input[type="email"], form input[type="password"], form input[type="text"], form input[type="date"], form select { flex: 1 1 200px; padding: 12px; border: 1px solid #ced4da; border-radius: 6px; font-size: 16px; }
        form button { padding: 12px 20px; border: none; background: #28a745; color: white; font-size: 16px; cursor: pointer; border-radius: 6px; transition: background-color 0.3s; }
        form button:hover { background: #218838; }
        .message { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f1f1f1; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e9ecef; }
        .action-links a { text-decoration: none; padding: 5px 10px; border-radius: 4px; transition: background-color 0.3s; }
        .action-links a:hover { background-color: #e9ecef; }
        .add-new-btn { margin-top: 10px; padding: 10px 15px; border: none; background: #007bff; color: white; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Quản lý hệ thống</h1>
        <a href="?action=logout" class="logout-btn">Đăng xuất</a>
    </div>

    <?php if ($message): ?>
        <div class='message'><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class='error'><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="tab-buttons">
        <button onclick="showTab('users')" class="<?php echo $active_tab == 'users' ? 'active' : ''; ?>">Quản lý tài khoản</button>
        <button onclick="showTab('courses')" class="<?php echo $active_tab == 'courses' ? 'active' : ''; ?>">Quản lý khóa học</button>
    </div>

    <div id="users" class="tab-content" style="display:<?php echo $active_tab == 'users' ? 'block' : 'none'; ?>;">
        <div id="add-user-form" style="display:block;">
            <h2>Thêm tài khoản</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="add_user">Thêm</button>
            </form>
        </div>

        <div id="edit-user-form" style="display:none;">
            <h2>Sửa tài khoản</h2>
            <form method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <input type="hidden" name="role" id="edit_user_current_role">
                <input type="email" name="email" id="edit_user_email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mật khẩu mới (để trống nếu không đổi)">
                <label for="edit_user_new_role">Thay đổi vai trò:</label>
                <select name="new_role" id="edit_user_new_role" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="edit_user">Cập nhật tài khoản</button>
            </form>
            <button class="add-new-btn" onclick="showAddForm('users')">Thêm tài khoản mới</button>
        </div>

        <h3>Danh sách tài khoản</h3>
        <h4>Admin</h4>
        <table>
            <tr><th>Email</th><th>Hành động</th></tr>
            <?php $admins->data_seek(0); while($a = $admins->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                    <td class="action-links">
                        <a href="?tab=users&delete_user_id=<?= $a['admin_id'] ?>&delete_user_role=admin" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                        |
                        <a href="#" onclick="editUser('<?= $a['admin_id'] ?>', '<?= htmlspecialchars($a['email']) ?>', 'admin')">Sửa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h4>Teacher</h4>
        <table>
            <tr><th>Email</th><th>Hành động</th></tr>
            <?php $teachers->data_seek(0); while($t = $teachers->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td class="action-links">
                        <a href="?tab=users&delete_user_id=<?= $t['teacher_id'] ?>&delete_user_role=teacher" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                        |
                        <a href="#" onclick="editUser('<?= $t['teacher_id'] ?>', '<?= htmlspecialchars($t['email']) ?>', 'teacher')">Sửa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h4>Student</h4>
        <table>
            <tr><th>Email</th><th>Hành động</th></tr>
            <?php $students->data_seek(0); while($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td class="action-links">
                        <a href="?tab=users&delete_user_id=<?= $s['student_id'] ?>&delete_user_role=student" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                        |
                        <a href="#" onclick="editUser('<?= $s['student_id'] ?>', '<?= htmlspecialchars($s['email']) ?>', 'student')">Sửa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    
    <div id="courses" class="tab-content" style="display:<?php echo $active_tab == 'courses' ? 'block' : 'none'; ?>;">
        <div id="add-course-form" style="display:block;">
            <h2>Thêm khóa học</h2>
            <form method="POST">
                <input type="text" name="course_information" placeholder="Tên khóa học" required>
                <input type="text" name="course_duration" placeholder="Thời lượng (ví dụ: 8 weeks)" required>
                <input type="date" name="start_date" required>
                <input type="date" name="end_date" required>
                <input type="text" name="description" placeholder="Mô tả">
                <select name="teacher_id" required>
                    <?php
                    $teacher_list = $conn->query("SELECT * FROM teacher");
                    while($t = $teacher_list->fetch_assoc()):
                    ?>
                        <option value="<?= $t['teacher_id'] ?>"><?= htmlspecialchars($t['email']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="add_course">Thêm</button>
            </form>
        </div>

        <div id="edit-course-form" style="display:none;">
            <h2>Sửa khóa học</h2>
            <form method="POST">
                <input type="hidden" name="course_id" id="edit_id">
                <input type="text" name="course_information" id="edit_name" required>
                <input type="text" name="course_duration" id="edit_duration" required>
                <input type="date" name="start_date" id="edit_start_date" required>
                <input type="date" name="end_date" id="edit_end_date" required>
                <input type="text" name="description" id="edit_description" placeholder="Mô tả">
                <select name="teacher_id" id="edit_teacher" required>
                    <?php
                    $teacher_list2 = $conn->query("SELECT * FROM teacher");
                    while($t = $teacher_list2->fetch_assoc()):
                    ?>
                        <option value="<?= $t['teacher_id'] ?>"><?= htmlspecialchars($t['email']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="edit_course">Cập nhật</button>
            </form>
            <button class="add-new-btn" onclick="showAddForm('courses')">Thêm khóa học mới</button>
        </div>

        <h3>Danh sách khóa học</h3>
        <table>
            <tr>
                <th>Tên khóa học</th><th>Giảng viên</th><th>Hành động</th>
            </tr>
            <?php
            $courses->data_seek(0);
            while($c = $courses->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($c['course_information']) ?></td>
                <td><?= htmlspecialchars($c['teacher_email']) ?></td>
                <td class="action-links">
                    <a href="?tab=courses&delete_course=<?= $c['course_id'] ?>" onclick="return confirm('Xóa khóa học này và tất cả dữ liệu liên quan?')">Xóa</a>
                    |
                    <a href="#" onclick="editCourse('<?= $c['course_id'] ?>', '<?= htmlspecialchars($c['course_information']) ?>', '<?= htmlspecialchars($c['course_duration']) ?>', '<?= htmlspecialchars($c['start_date']) ?>', '<?= htmlspecialchars($c['end_date']) ?>', '<?= $c['teacher_id'] ?>', '<?= htmlspecialchars($c['description']) ?>')">Sửa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.querySelectorAll('.tab-buttons button').forEach(b => b.classList.remove('active'));
        document.getElementById(tabId).style.display = 'block';
        
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);

        document.querySelector(`.tab-buttons button[onclick="showTab('${tabId}')"]`).classList.add('active');
    }

    function showAddForm(tab) {
        if (tab === 'users') {
            document.getElementById('add-user-form').style.display = 'block';
            document.getElementById('edit-user-form').style.display = 'none';
        } else if (tab === 'courses') {
            document.getElementById('add-course-form').style.display = 'block';
            document.getElementById('edit-course-form').style.display = 'none';
        }
    }

    function editCourse(id, name, duration, start, end, teacher_id, description) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_duration').value = duration;
        document.getElementById('edit_start_date').value = start;
        document.getElementById('edit_end_date').value = end;
        document.getElementById('edit_teacher').value = teacher_id;
        document.getElementById('edit_description').value = description;
        
        document.getElementById('add-course-form').style.display = 'none';
        document.getElementById('edit-course-form').style.display = 'block';
        
        showTab('courses');
    }

    function editUser(id, email, role) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_user_email').value = email;
        document.getElementById('edit_user_current_role').value = role;
        document.getElementById('edit_user_new_role').value = role;
        
        document.getElementById('add-user-form').style.display = 'none';
        document.getElementById('edit-user-form').style.display = 'block';
        
        showTab('users');
    }
    
    window.addEventListener('DOMContentLoaded', (event) => {
        const params = new URLSearchParams(window.location.search);
        const activeTab = params.get('tab') || 'users';
        showTab(activeTab);
    });
</script>

</body>
</html>