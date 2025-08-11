<?php
session_start();
include '../register/db_connect.php';

if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: ../register/login.php");
    exit;
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];
$assignmentId = $_GET['assignment_id'] ?? null;
$courseId = $_GET['course_id'] ?? null;

if (!$assignmentId || !$courseId) {
    die("Thiếu thông tin bài tập hoặc khóa học.");
}

function getUserId($conn, $role) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT {$role}_id FROM {$role} WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0] ?? null;
}

// Lấy student_id ngay từ đầu nếu người dùng là sinh viên
$studentId = ($userRole === 'student') ? getUserId($conn, 'student') : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assignment']) && $userRole === 'student') {
    $file = $_FILES['submission_file'];
    $uploadDir = 'student_submissions/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO submission (assignment_id, student_id, file_path, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $assignmentId, $studentId, $targetFile);
        $stmt->execute();
    }
}

// Lấy thông tin bài tập
$stmt = $conn->prepare("SELECT * FROM assignment WHERE assignment_id = ?");
$stmt->bind_param("i", $assignmentId);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết bài tập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="stylee.css">
    <style>
        /* Google Fonts - Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --background-color: #f8f9fa;
            --card-bg-color: #ffffff;
            --sidebar-bg-color: #2c3e50;
            --sidebar-link-color: #ecf0f1;
            --sidebar-active-color: #ffffff;
            --border-color: #e9ecef;
            --text-color: #34495e;
            --heading-color: #2c3e50;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg-color);
            color: var(--sidebar-link-color);
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: width 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar img {
            width: 100%;
            max-width: 150px;
            margin: 0 auto 30px;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .sidebar a {
            color: var(--sidebar-link-color);
            text-decoration: none;
            padding: 15px 20px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
        }

        .sidebar a i {
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-active-color);
            transform: translateX(5px);
        }

        .sidebar a.active {
            background-color: var(--primary-color);
            color: var(--sidebar-active-color);
            font-weight: 600;
            box-shadow: var(--shadow);
        }

        /* Main Wrapper */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Topbar for Class Page */
        .inner-topbar {
            background-color: var(--card-bg-color);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            padding: 0;
            display: flex;
            justify-content: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .btn-group a {
            flex-grow: 1;
            text-align: center;
            padding: 20px;
            text-decoration: none;
            color: var(--secondary-color);
            transition: color 0.3s, background-color 0.3s;
            border-bottom: 3px solid transparent;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .btn-group a:hover {
            color: var(--primary-color);
        }
        
        .btn-group a.active-tab {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            padding: 40px;
            flex: 1;
            overflow-y: auto;
        }

        h2, h3 {
            color: black;
            font-weight: 600;
            margin-bottom: 15px;
        }

        h3 {
            font-size: 1.5rem;
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
            margin-bottom: 20px;
        }
        
        h2 {
            font-size: 1.8rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
            display: inline-block;
        }

        /* Form Styles */
        form {
            background-color: var(--card-bg-color);
            padding: 25px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }

        form h3 {
            margin: 0 0 10px 0;
            border: none;
            padding: 0;
            font-size: 1.3rem;
            color: var(--heading-color);
        }

        form input[type="text"], form textarea, form input[type="file"], form input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        form textarea {
            resize: vertical;
        }

        form input[type="text"]:focus, form textarea:focus, form input[type="file"]:focus, form input[type="date"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        form button {
            padding: 12px 25px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
        }

        form button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Lists Styles */
        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            background-color: var(--card-bg-color);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        ul.assignment-list li {
            flex-direction: column;
            align-items: flex-start;
        }

        ul li:hover {
            box-shadow: var(--hover-shadow);
            transition: box-shadow 0.3s ease;
        }
        
        ul li div a.delete-link {
            color: red !important;
            margin-left: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        ul li div a.delete-link:hover {
            text-decoration: underline;
        }

        ul li a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        ul li a:hover {
            text-decoration: underline;
        }

        ul li strong {
            font-size: 1.1rem;
            color: var(--heading-color);
        }
        
        ul.assignment-list li p {
            margin: 5px 0;
            color: #555;
        }

        /* Forum Styles */
        .forum-post {
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .forum-post .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .forum-post .post-header strong {
            font-size: 1.1rem;
            color: var(--primary-color);
        }
        
        .forum-post .post-header span {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .forum-post p {
            margin: 10px 0;
            line-height: 1.6;
        }
        
        .comments-section {
            margin-top: 20px;
        }

        .comment-box {
            background-color: #f7f9fc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            margin-left: 30px;
        }
        
        .comment-box strong {
            color: var(--heading-color);
            font-size: 1rem;
        }
        
        .comment-box p {
            margin: 5px 0;
            font-size: 0.95rem;
        }

        .comment-form {
            display: flex;
            gap: 10px;
            margin-left: 30px;
            margin-top: 10px;
        }
        
        .comment-form input[type="text"] {
            flex-grow: 1;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
        }
        
        .comment-form button {
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .comment-form button:hover {
            background-color: #0056b3;
            transform: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar a span {
                display: none;
            }
            .sidebar a {
                justify-content: center;
                padding: 15px 10px;
            }
            .main-wrapper {
                margin-left: 80px;
            }
            .main-content {
                padding: 20px;
            }
            .inner-topbar {
                padding: 15px 20px;
            }
            .page-title {
                font-size: 1.5rem;
            }
            .btn-group a {
                padding: 15px 10px;
                font-size: 1rem;
            }
            .forum-comments {
                margin-left: 15px;
            }
            form {
                padding: 15px;
            }
            ul li {
                flex-direction: column;
                align-items: flex-start;
            }
            ul li div {
                display: flex;
                flex-direction: row;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    
        <div class="sidebar-logo">
            <i class="fas fa-school"></i>
            <span>BTEC Logo</span>
        </div>
    <a href="main.php?page=home" class="<?= ($page === 'home') ? 'active' : '' ?>">🏠 Home</a>
    <a href="courses.php" class="<?= ($page === 'courses') ? 'active' : '' ?>">📚 Courses</a>
    <a href="settings.php" class="<?= ($page === 'settings') ? 'active' : '' ?>">⚙️ Settings</a>
    <a href="logout.php" class="<?= ($page === 'logout') ? 'active' : '' ?>">🚪 Logout</a>
  </div>

    <div class="main-wrapper">
        <div class="main-content">
            <h2>📘 Chi tiết bài tập</h2>

            <?php if ($assignment): ?>
                <div class="assignment-details">
                    <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($assignment['title']) ?></p>
                    <p><strong>Hạn nộp:</strong> <?= $assignment['deadline'] ?></p>
                    <p><strong>Mô tả:</strong><br><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
                </div>
            <?php else: ?>
                <p style="color:red;">Bài tập không tồn tại.</p>
            <?php endif; ?>

            <?php if ($userRole === 'student'): ?>
                <hr>
                <h3>📤 Nộp bài</h3>
                <form class="submission-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="submit_assignment" value="1">
                    <input type="file" name="submission_file" required>
                    <button type="submit">Gửi bài</button>
                </form>
            <?php endif; ?>

            <?php if ($userRole === 'student'): ?>
                <hr>
                <h3>📤 Bài bạn đã nộp</h3>
                <ul class="submission-list">
                <?php
                // Sử dụng biến $studentId đã được lấy ở trên
                $stmt = $conn->prepare("
                    SELECT file_path, submitted_at 
                    FROM submission 
                    WHERE assignment_id = ? AND student_id = ?
                ");
                $stmt->bind_param("ii", $assignmentId, $studentId);
                $stmt->execute();
                $subs = $stmt->get_result();

                if ($subs->num_rows > 0):
                    while ($row = $subs->fetch_assoc()):
                ?>
                    <li>
                        <span>⏱ <?= htmlspecialchars($row['submitted_at']) ?></span>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">📄 Xem bài</a>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" download>⬇ Tải về</a>
                    </li>
                <?php 
                    endwhile;
                else:
                    echo "<li>Bạn chưa nộp bài nào cho bài tập này.</li>";
                endif;
                ?>
                </ul>
            <?php endif; ?>

            <?php if ($userRole === 'teacher'): ?>
                <hr>
                <h3>📥 Bài nộp của sinh viên</h3>
                <ul class="submission-list">
                <?php
                $stmt = $conn->prepare("SELECT s.file_path, s.submitted_at, st.email 
                    FROM submission s 
                    JOIN student st ON s.student_id = st.student_id 
                    WHERE s.assignment_id = ?");
                $stmt->bind_param("i", $assignmentId);
                $stmt->execute();
                $subs = $stmt->get_result();
                while ($row = $subs->fetch_assoc()):
                ?>
                    <li>
                        <span><strong><?= htmlspecialchars($row['email']) ?></strong> - <?= $row['submitted_at'] ?></span>
                        <a href="<?= $row['file_path'] ?>" target="_blank">Xem bài</a>
                    </li>
                <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>