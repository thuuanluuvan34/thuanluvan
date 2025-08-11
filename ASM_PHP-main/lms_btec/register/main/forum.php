<?php
session_start();
include '../register/db_connect.php';

if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

$userEmail = $_SESSION['email'];
$userRole = $_SESSION['role'];
$courseId = $_GET['course_id'] ?? null;
$tab = $_GET['tab'] ?? 'forum';

if (!$courseId) {
    die("Thiếu mã khóa học.");
}

// Lấy thông tin người dùng
function getUserInfo($conn, $email, $role) {
    $stmt = $conn->prepare("SELECT {$role}_id, full_name FROM {$role} WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$userInfo = getUserInfo($conn, $userEmail, $userRole);
$userId = $userInfo[$userRole . '_id'];
$userName = $userInfo['full_name'];

// Lấy forum_id theo course
$forumId = null;
$stmt = $conn->prepare("SELECT forum_id FROM forum WHERE course_id = ?");
$stmt->bind_param("i", $courseId);
$stmt->execute();
if ($row = $stmt->get_result()->fetch_assoc()) {
    $forumId = $row['forum_id'];
}

// Xử lý bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'], $_POST['post_id'])) {
    $content = trim($_POST['comment_content']);
    $postId = intval($_POST['post_id']);

    if ($content && $forumId) {
        $stmt = $conn->prepare("
            INSERT INTO comment (post_id, student_id, teacher_id, content, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $studentId = $userRole === 'student' ? $userId : null;
        $teacherId = $userRole === 'teacher' ? $userId : null;
        $stmt->bind_param("iiis", $postId, $studentId, $teacherId, $content);
        $stmt->execute();
        header("Location: forum.php?course_id=$courseId&tab=forum");
        exit;
    }
}

// Lấy danh sách bài viết
$posts = [];
$commentsMap = [];

if ($forumId) {
    $postStmt = $conn->prepare("
        SELECT post.*, COALESCE(student.full_name, teacher.full_name) AS author_name
        FROM post
        LEFT JOIN student ON post.student_id = student.student_id
        LEFT JOIN teacher ON post.teacher_id = teacher.teacher_id
        WHERE forum_id = ?
        ORDER BY post_id DESC
    ");
    $postStmt->bind_param("i", $forumId);
    $postStmt->execute();
    $posts = $postStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Lấy toàn bộ bình luận
    $commentStmt = $conn->prepare("
        SELECT comment.*, COALESCE(student.full_name, teacher.full_name) AS commenter_name
        FROM comment
        LEFT JOIN student ON comment.student_id = student.student_id
        LEFT JOIN teacher ON comment.teacher_id = teacher.teacher_id
        WHERE post_id IN (SELECT post_id FROM post WHERE forum_id = ?)
        ORDER BY comment_id ASC
    ");
    $commentStmt->bind_param("i", $forumId);
    $commentStmt->execute();
    $commentResult = $commentStmt->get_result();

    while ($cmt = $commentResult->fetch_assoc()) {
        $commentsMap[$cmt['post_id']][] = $cmt;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Forum - LMS</title>
  
  <link rel="stylesheet" href="style.css">
  <style>
    /* CSS forum đẹp, hiện đại, chuẩn LMS */
    .main-content {
      max-width: 900px;
      margin: 30px auto;
      background-color: #fff9e6;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #222;
    }

    .main-content h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #fbb040;
      text-align: center;
      letter-spacing: 1.2px;
    }

    .forum-post {
      background: #fefefe;
      border: 1.5px solid #ffdca8;
      border-radius: 12px;
      padding: 20px 25px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(251,176,64,0.15);
      transition: box-shadow 0.3s ease;
    }

    .forum-post:hover {
      box-shadow: 0 6px 20px rgba(251,176,64,0.35);
    }

    .forum-post strong {
      color: #d97800;
      font-size: 18px;
    }

    .forum-post p {
      margin: 10px 0 20px 0;
      font-size: 16px;
      line-height: 1.5;
      white-space: pre-wrap;
      color: #444;
    }

    .forum-comment {
      background: #fff;
      border-left: 5px solid #fbb040;
      margin-left: 30px;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: inset 0 1px 4px rgba(251,176,64,0.15);
      margin-bottom: 15px;
    }

    .forum-comment strong {
      color: #d97800;
      font-weight: 700;
      font-size: 15px;
    }

    .forum-comment em {
      color: #888;
      font-style: italic;
    }

    .comment-form {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .comment-form textarea {
      width: 100%;
      min-height: 70px;
      padding: 12px 15px;
      font-size: 15px;
      border: 1.8px solid #fbb040;
      border-radius: 10px;
      resize: vertical;
      font-family: inherit;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .comment-form textarea:focus {
      outline: none;
      border-color: #d97800;
      box-shadow: 0 0 10px rgba(217,120,0,0.4);
    }

    .comment-form button {
      align-self: flex-end;
      background-color: #fbb040;
      color: white;
      font-weight: 700;
      font-size: 16px;
      padding: 12px 24px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 5px 10px rgba(251,176,64,0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .comment-form button:hover {
      background-color: #d97800;
      box-shadow: 0 8px 15px rgba(217,120,0,0.6);
    }

    @media (max-width: 600px) {
      .main-content {
        padding: 20px 25px;
        margin: 20px;
      }
      .forum-post {
        padding: 18px 20px;
      }
      .comment-form button {
        width: 100%;
        padding: 14px;
      }
    }
  </style>
</head>
<body>
<div class="main-content">
  <h2>📢 Courses Forum</h2>

  <?php if (count($posts) > 0): ?>
    <?php foreach ($posts as $post): ?>
      <div class="forum-post">
        <strong><?= htmlspecialchars($post['author_name']) ?></strong>:
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <?php if (!empty($commentsMap[$post['post_id']])): ?>
          <?php foreach ($commentsMap[$post['post_id']] as $comment): ?>
            <div class="forum-comment">
              <strong><?= htmlspecialchars($comment['commenter_name']) ?></strong>: 
              <?= nl2br(htmlspecialchars($comment['content'])) ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="forum-comment"><em>No comments yet.</em></div>
        <?php endif; ?>

        <!-- Form bình luận -->
        <form class="comment-form" method="post">
          <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
          <textarea name="comment_content" rows="2" placeholder="Enter comment..." required></textarea>
          <button type="submit">💬 Submit a comment</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>There are no posts in the forum yet.</p>
  <?php endif; ?>
</div>
</body>
</html>
