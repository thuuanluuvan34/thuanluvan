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

// Thêm dòng này để đặt biến $page thành 'courses'
$page = 'courses';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Courses</title>
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
        <a href="main.php?page=home" class="<?= ($page === 'home') ? 'active' : '' ?>">🏠 Home</a>
        <a href="courses.php" class="<?= ($page === 'courses') ? 'active' : '' ?>">📚 Courses</a>
        <a href="settings.php" class="<?= ($page === 'settings') ? 'active' : '' ?>">⚙️ Settings</a>
        <a href="logout.php" class="<?= ($page === 'logout') ? 'active' : '' ?>">🚪 Logout</a>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <div class="inner-topbar">
            <div class="page-title"> Courses </div>
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
            <?php if ($userRole === 'teacher'): ?>
                <?php
                // Lấy teacher_id theo email
                $query = "SELECT teacher_id, full_name FROM teacher WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $teacher = $result->fetch_assoc();

                $teacherId = $teacher['teacher_id'];
                $teacherName = $teacher['full_name'];

                // Truy vấn danh sách khóa học do giáo viên phụ trách
                $courseQuery = "SELECT * FROM course WHERE teacher_id = $teacherId";
                $courseResult = $conn->query($courseQuery);
                ?>
                <h2>Teacher: <?= htmlspecialchars($teacherName) ?></h2>
                <?php if ($courseResult->num_rows > 0): ?>
                    <table class="course-table">
                        <tr>
                            <th>#</th>
                            <th>Duration</th>
                            <th>Information</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                        <?php $index = 1; while ($row = $courseResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= htmlspecialchars($row['course_duration']) ?></td>
                                <td>
                                    <a href="class.php?course_id=<?= $row['course_id'] ?>">
                                        <?= htmlspecialchars($row['course_information']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['start_date']) ?></td>
                                <td><?= htmlspecialchars($row['end_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>You are not in charge of any courses.</p>
                <?php endif; ?>

            <?php elseif ($userRole === 'student'): ?>
                <?php
                // Lấy student_id theo email
                $query = "SELECT student_id, full_name FROM student WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $student = $result->fetch_assoc();

                $studentId = $student['student_id'];
                $studentName = $student['full_name'];

                // Lấy danh sách khóa học đã đăng ký
                $courseQuery = "
                    SELECT c.* FROM course c
                    JOIN registration r ON c.course_id = r.course_id
                    WHERE r.student_id = $studentId
                ";
                $courseResult = $conn->query($courseQuery);

                // Lấy danh sách tất cả các khóa học để đăng ký mới
                $allCourseQuery = "SELECT * FROM course";
                $allCourseResult = $conn->query($allCourseQuery);

                // Xử lý đăng ký khóa học mới
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course_id'])) {
                    $registerCourseId = intval($_POST['register_course_id']);
                    // Kiểm tra đã đăng ký chưa
                    $check = $conn->query("SELECT * FROM registration WHERE student_id = $studentId AND course_id = $registerCourseId");
                    if ($check->num_rows == 0) {
                        $conn->query("INSERT INTO registration (student_id, course_id) VALUES ($studentId, $registerCourseId)");
                        echo "<p style='color:green'>Registered successfully!</p>";
                        // Reload để cập nhật danh sách
                        echo "<meta http-equiv='refresh' content='1'>";
                    } else {
                        echo "<p style='color:orange'>You have already registered this course.</p>";
                    }
                }
                ?>
                <h2>Student: <?= htmlspecialchars($studentName) ?></h2>
                <h3>Your Registered Courses</h3>
                <?php if ($courseResult->num_rows > 0): ?>
                    <table class="course-table">
                        <tr>
                            <th>#</th>
                            <th>Duration</th>
                            <th>Information</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                        <?php $index = 1; while ($row = $courseResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td><?= htmlspecialchars($row['course_duration']) ?></td>
                                <td>
                                    <a href="class.php?course_id=<?= $row['course_id'] ?>">
                                        <?= htmlspecialchars($row['course_information']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['start_date']) ?></td>
                                <td><?= htmlspecialchars($row['end_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>You have not registered for any courses yet.</p>
                <?php endif; ?>

                <h3>Register New Course</h3>
                <form method="post" id="registerForm">
                    <select name="register_course_id" id="register_course_id" required onchange="showCourseInfo()">
                        <option value="">-- Select Course --</option>
                        <?php
                        // Reset lại con trỏ để lấy lại dữ liệu
                        $allCourseResult->data_seek(0);
                        $coursesData = [];
                        while ($row = $allCourseResult->fetch_assoc()):
                            // Lấy tên giảng viên
                            $teacherId = $row['teacher_id'];
                            $teacherName = '';
                            $teacherQuery = $conn->query("SELECT full_name FROM teacher WHERE teacher_id = $teacherId");
                            if ($teacherRow = $teacherQuery->fetch_assoc()) {
                                $teacherName = $teacherRow['full_name'];
                            }
                            // Lưu thông tin cho JS
                            $coursesData[$row['course_id']] = [
                                'info' => $row['course_information'],
                                'teacher' => $teacherName,
                                'start' => $row['start_date'],
                                'end' => $row['end_date'],
                                'longtext' => isset($row['description']) ? $row['description'] : $row['course_information']
                            ];
                        ?>
                            <option value="<?= $row['course_id'] ?>">
                                <?= htmlspecialchars($row['course_information']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Register</button>
                </form>

                <div id="courseDetail" style="display:none;margin-top:16px;">
                    <div id="courseDetailBox" style="
                        background: #fff;
                        border: 1px solid #ccc;
                        border-radius: 8px;
                        padding: 20px;
                        max-width: 600px;
                        max-height: 300px;
                        overflow-y: auto;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                        font-size: 1.1rem;
                        color: #222;
                    ">
                        <h3 id="cd_info" style="margin-top:0;"></h3>
                        <p><b>Teacher:</b> <span id="cd_teacher"></span></p>
                        <p><b>Start Date:</b> <span id="cd_start"></span></p>
                        <p><b>End Date:</b> <span id="cd_end"></span></p>
                        <div id="cd_longtext" style="margin-top:18px;white-space:pre-line;"></div>
                    </div>
                </div>

                <script>
                    // Dữ liệu khóa học cho JS
                    const coursesData = <?= json_encode($coursesData) ?>;
                    function showCourseInfo() {
                        const select = document.getElementById('register_course_id');
                        const val = select.value;
                        if (val && coursesData[val]) {
                            document.getElementById('cd_info').innerText = coursesData[val].info;
                            document.getElementById('cd_teacher').innerText = coursesData[val].teacher;
                            document.getElementById('cd_start').innerText = coursesData[val].start;
                            document.getElementById('cd_end').innerText = coursesData[val].end;
                            // Nếu có trường longtext/description thì hiển thị, không thì dùng info
                            document.getElementById('cd_longtext').innerText = coursesData[val].longtext ? coursesData[val].longtext : coursesData[val].info;
                            document.getElementById('courseDetail').style.display = 'block';
                        } else {
                            document.getElementById('courseDetail').style.display = 'none';
                        }
                    }
                </script>
            <?php else: ?>
                <p>Access denied.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
