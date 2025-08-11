<?php
session_start();
include '../register/db_connect.php';

// Check if session exists
if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];
$courseId = $_GET['course_id'] ?? null;
$tab = $_GET['tab'] ?? 'materials';

if (!$courseId) die("Missing course ID.");

function getUserId($conn, $role) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT {$role}_id FROM {$role} WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_doc'])) {
        $assignment = $_POST['assignment'];
        $file = $_FILES['document_file'];
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . '_' . basename($file['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO document (course_id, assignment, document_link) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $courseId, $assignment, $targetFile);
            $stmt->execute();
        }
    }

    if (isset($_POST['create_assignment'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $deadline = $_POST['deadline'];
        $stmt = $conn->prepare("INSERT INTO assignment (course_id, title, description, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $courseId, $title, $description, $deadline);
        $stmt->execute();
    }

    if (isset($_POST['post_forum'])) {
        $content = $_POST['content'];
        $studentId = $userRole === 'student' ? getUserId($conn, 'student') : null;
        $teacherId = $userRole === 'teacher' ? getUserId($conn, 'teacher') : null;
        $stmt = $conn->prepare("SELECT forum_id FROM forum WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $forumId = $stmt->get_result()->fetch_assoc()['forum_id'] ?? null;

        if ($forumId) {
            $stmt = $conn->prepare("INSERT INTO post (student_id, teacher_id, forum_id, content) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $studentId, $teacherId, $forumId, $content);
            $stmt->execute();
        }
    }

    if (isset($_POST['comment_submit'])) {
        $postId = $_POST['post_id'];
        $commentContent = $_POST['comment_content'];
        $studentId = $userRole === 'student' ? getUserId($conn, 'student') : null;
        $teacherId = $userRole === 'teacher' ? getUserId($conn, 'teacher') : null;

        $stmt = $conn->prepare("INSERT INTO comment (post_id, student_id, teacher_id, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $postId, $studentId, $teacherId, $commentContent);
        $stmt->execute();
    }
}

// ===== HANDLE DELETING DOCUMENTS/ASSIGNMENTS =====
if ($userRole === 'teacher') {
    // Delete document
    if (isset($_GET['delete_doc_id'])) {
        $deleteId = intval($_GET['delete_doc_id']);
        $stmt = $conn->prepare("DELETE FROM document WHERE document_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $deleteId, $courseId);
        $stmt->execute();
        header("Location: class.php?course_id=$courseId&tab=materials");
        exit;
    }
    // Delete assignment
    if (isset($_GET['delete_assignment_id'])) {
        $deleteId = intval($_GET['delete_assignment_id']);
        $stmt = $conn->prepare("DELETE FROM assignment WHERE assignment_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $deleteId, $courseId);
        $stmt->execute();
        header("Location: class.php?course_id=$courseId&tab=materials");
        exit;
    }
}

// Define the current page for dynamic sidebar highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$pageParam = $_GET['page'] ?? 'home';
$isCoursesPage = ($currentPage === 'class.php' || $currentPage === 'courses.php');
$isHomePage = ($currentPage === 'main.php' && $pageParam === 'home');
$isSettingsPage = ($currentPage === 'settings.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Details</title>
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-school"></i>
            <span>BTEC Logo</span>
        </div>
        <!-- Updated links with dynamic 'active' class -->
        <a href="main.php?page=home" class="<?= $isHomePage ? 'active' : '' ?>">🏠 Home</a>
        <a href="courses.php" class="<?= $isCoursesPage ? 'active' : '' ?>">📚 Courses</a>
        <a href="settings.php" class="<?= $isSettingsPage ? 'active' : '' ?>">⚙️ Settings</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main-wrapper">
        <div class="inner-topbar">
            <div class="btn-group">
                <a href="class.php?course_id=<?= $courseId ?>&tab=materials" class="<?= $tab === 'materials' ? 'active-tab' : '' ?>">📁 Materials & Assignments</a>
                <a href="class.php?course_id=<?= $courseId ?>&tab=forum" class="<?= $tab === 'forum' ? 'active-tab' : '' ?>">💬 Forum</a>
            </div>
        </div>

        <div class="main-content">
        <?php if ($tab === 'materials'): ?>
            <h2>Materials and Assignments</h2>
            
            <?php if ($userRole === 'teacher'): ?>
            <form method="POST" enctype="multipart/form-data">
                <h3>📂 Upload Documents</h3>
                <input type="hidden" name="upload_doc" value="1">
                <input type="text" name="assignment" placeholder="Document Title" required>
                <input type="file" name="document_file" required>
                <button type="submit">Upload</button>
            </form>

            <form method="POST">
                <h3>📝 Create Assignment</h3>
                <input type="hidden" name="create_assignment" value="1">
                <input type="text" name="title" placeholder="Assignment Title" required>
                <textarea name="description" placeholder="Assignment Description..." rows="3" required></textarea>
                <input type="date" name="deadline" required>
                <button type="submit">Create</button>
            </form>
            <?php endif; ?>

            <h3>📚 Documents</h3>
            <ul class="document-list">
            <?php
            $stmt = $conn->prepare("SELECT * FROM document WHERE course_id = ?");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $docs = $stmt->get_result();
            while ($doc = $docs->fetch_assoc()):
            ?>
                <li>
                    <span><?= htmlspecialchars($doc['assignment']) ?></span>
                    <div>
                        <a href="<?= $doc['document_link'] ?>" target="_blank">Download</a>
                        <?php if ($userRole === 'teacher'): ?>
                            <a href="?course_id=<?= $courseId ?>&tab=materials&delete_doc_id=<?= $doc['document_id'] ?>"
                               onclick="return confirm('Are you sure you want to delete this document?');"
                               class="delete-link">Delete</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
            </ul>

            <h3 style="margin-top:20px;">📝 Assignments</h3>
            <ul class="assignment-list">
            <?php
            $stmt = $conn->prepare("SELECT * FROM assignment WHERE course_id = ?");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $assignments = $stmt->get_result();
            while ($asm = $assignments->fetch_assoc()):
            ?>
                <li>
                    <div>
                        <strong>
                            <a href="assignment.php?course_id=<?= $courseId ?>&assignment_id=<?= $asm['assignment_id'] ?>">
                                <?= htmlspecialchars($asm['title']) ?>
                            </a>
                        </strong>
                        <p>Deadline: <?= $asm['deadline'] ?></p>
                        <p><?= nl2br(htmlspecialchars($asm['description'])) ?></p>
                    </div>
                    <?php if ($userRole === 'teacher'): ?>
                        <div>
                            <a href="?course_id=<?= $courseId ?>&tab=materials&delete_assignment_id=<?= $asm['assignment_id'] ?>"
                               onclick="return confirm('Are you sure you want to delete this assignment?');"
                               class="delete-link">Delete</a>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
            </ul>

        <?php elseif ($tab === 'forum'): ?>
            <h2>💬 Course Forum</h2>
            <form method="POST">
                <h3>Create a New Post</h3>
                <input type="hidden" name="post_forum" value="1">
                <textarea name="content" rows="3" placeholder="Write a discussion post..." required></textarea>
                <button type="submit">Post</button>
            </form>

            <div class="posts-container">
            <?php
            $stmt = $conn->prepare("SELECT p.*, COALESCE(s.email, t.email) AS author_email 
                FROM post p
                LEFT JOIN student s ON p.student_id = s.student_id
                LEFT JOIN teacher t ON p.teacher_id = t.teacher_id
                WHERE p.forum_id = (SELECT forum_id FROM forum WHERE course_id = ?)
                ORDER BY p.post_id DESC");
            $stmt->bind_param("i", $courseId);
            $stmt->execute();
            $posts = $stmt->get_result();

            while ($post = $posts->fetch_assoc()):
            ?>
                <div class="forum-post">
                    <div class="post-header">
                        <strong><?= htmlspecialchars($post['author_email']) ?></strong>
                        <span><?= date('H:i, d/m/Y', strtotime($post['created_at'] ?? 'now')) ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    
                    <div class="comments-section">
                    <?php
                    $pid = $post['post_id'];
                    $stmt2 = $conn->prepare("SELECT c.*, COALESCE(s.email, t.email) AS commenter_email 
                        FROM comment c
                        LEFT JOIN student s ON c.student_id = s.student_id
                        LEFT JOIN teacher t ON c.teacher_id = t.teacher_id
                        WHERE c.post_id = ? ORDER BY comment_id ASC");
                    $stmt2->bind_param("i", $pid);
                    $stmt2->execute();
                    $comments = $stmt2->get_result();
                    while ($c = $comments->fetch_assoc()):
                    ?>
                        <div class="comment-box">
                            <strong><?= htmlspecialchars($c['commenter_email']) ?></strong>: 
                            <p><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                        </div>
                    <?php endwhile; ?>
                    </div>

                    <form method="POST" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $pid ?>">
                        <input type="text" name="comment_content" placeholder="Write a comment..." required>
                        <button type="submit" name="comment_submit">Comment</button>
                    </form>
                </div>
            <?php endwhile; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
