-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 09:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_btec`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `role_id`) VALUES
(1, 'admin1@btec.edu.vn', 'admin123', 1);

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment`
--

INSERT INTO `assignment` (`assignment_id`, `course_id`, `title`, `description`, `deadline`) VALUES
(1, 1, '1', '23', '2025-07-02'),
(2, 1, 'test', 'adojawpdwop', '2025-07-25');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `post_id`, `student_id`, `teacher_id`, `content`) VALUES
(1, 1, 2, NULL, 'Bạn có thể xem lại ví dụ ở bài 3 nhé!'),
(2, 1, NULL, 1, 'Thầy sẽ nhắc lại phần này ở buổi sau.'),
(3, 2, 3, NULL, 'Cảm ơn thầy ạ!'),
(4, 4, 2, NULL, '2');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_duration` varchar(50) DEFAULT NULL,
  `course_information` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_duration`, `course_information`, `start_date`, `end_date`, `teacher_id`, `description`) VALUES
(1, '8 weeks', 'Lập trình Web cơ bản với HTML, CSS và PHP', '2025-08-01', '2025-09-26', 1, NULL),
(2, '10 weeks', 'Phân tích thiết kế hệ thống thông tin', '2025-09-01', '2025-11-10', 2, NULL),
(19, '10 weeks', 'PHP Advanced', '2025-09-01', '2025-11-10', 7, 'Khóa học \"PHP Advanced\" giúp bạn nâng cao kỹ năng lập trình PHP, tập trung vào các chủ đề như hướng đối tượng (OOP), xây dựng ứng dụng web động, bảo mật, và tối ưu hiệu năng. Bạn sẽ được thực hành với các mô hình MVC, sử dụng Composer để quản lý thư viện, và tích hợp các API phổ biến. Ngoài ra, khóa học còn hướng dẫn cách xây dựng RESTful API, xác thực người dùng, xử lý upload file, và triển khai ứng dụng thực tế trên server. Kết thúc khóa học, bạn sẽ tự tin phát triển các dự án web phức tạp, hiểu sâu về kiến trúc phần mềm, và sẵn sàng ứng tuyển vào các vị trí lập trình viên PHP chuyên nghiệp.'),
(20, '8 weeks', 'JavaScript Mastery', '2025-09-15', '2025-11-10', 7, 'Khóa học \"JavaScript Mastery\" dành cho những ai muốn làm chủ JavaScript hiện đại. Bạn sẽ học về ES6+, lập trình bất đồng bộ với Promise, async/await, thao tác DOM nâng cao, và xây dựng ứng dụng web tương tác. Khóa học còn hướng dẫn sử dụng các framework phổ biến như ReactJS, quản lý trạng thái với Redux, và tối ưu hiệu năng front-end. Ngoài ra, bạn sẽ thực hành xây dựng Single Page Application (SPA), kết nối API, và triển khai ứng dụng trên môi trường thực tế. Sau khóa học, bạn sẽ có nền tảng vững chắc để phát triển web hiện đại và ứng tuyển vào các vị trí front-end developer.'),
(21, '12 weeks', 'Python for Data Science', '2025-10-01', '2025-12-24', 8, 'Khóa học \"Python for Data Science\" trang bị cho bạn kiến thức và kỹ năng sử dụng Python trong phân tích dữ liệu. Bạn sẽ học cách xử lý dữ liệu với pandas, trực quan hóa dữ liệu bằng matplotlib và seaborn, làm sạch dữ liệu, và thực hiện các phép toán thống kê cơ bản. Khóa học còn giới thiệu về NumPy, SciPy, và các thư viện hỗ trợ machine learning như scikit-learn. Bạn sẽ thực hành với các dự án thực tế như phân tích dữ liệu kinh doanh, dự báo xu hướng, và xây dựng mô hình dự đoán. Kết thúc khóa học, bạn sẽ tự tin ứng dụng Python vào các dự án phân tích dữ liệu và khoa học dữ liệu.'),
(22, '10 weeks', 'Machine Learning Basics', '2025-09-20', '2025-11-29', 8, 'Khóa học \"Machine Learning Basics\" giúp bạn tiếp cận nền tảng của học máy với Python. Bạn sẽ tìm hiểu về các thuật toán supervised và unsupervised, cách chuẩn bị dữ liệu, huấn luyện và đánh giá mô hình. Khóa học hướng dẫn sử dụng scikit-learn để xây dựng các mô hình phân loại, hồi quy, clustering, và giảm chiều dữ liệu. Ngoài ra, bạn sẽ học cách xử lý overfitting, lựa chọn tham số tối ưu, và đánh giá hiệu quả mô hình qua các chỉ số phổ biến. Khóa học phù hợp cho người mới bắt đầu muốn khám phá lĩnh vực trí tuệ nhân tạo và ứng dụng machine learning vào thực tế.'),
(23, '10 weeks', 'Android App Development', '2025-09-05', '2025-11-14', 9, 'Khóa học \"Android App Development\" hướng dẫn bạn xây dựng ứng dụng di động trên nền tảng Android bằng Java và Kotlin. Bạn sẽ học về cấu trúc dự án Android, giao diện người dùng (UI), quản lý dữ liệu với SQLite, và tích hợp các API phổ biến như Google Maps, Firebase. Khóa học còn hướng dẫn xử lý đa luồng, tối ưu hiệu năng, và triển khai ứng dụng lên Google Play Store. Qua các dự án thực tế, bạn sẽ phát triển kỹ năng thiết kế UI/UX, xử lý sự kiện, và xây dựng ứng dụng hoàn chỉnh. Sau khóa học, bạn có thể tự tin phát triển ứng dụng Android chuyên nghiệp.'),
(24, '8 weeks', 'iOS App Development', '2025-09-18', '2025-11-13', 9, 'Khóa học \"iOS App Development\" giúp bạn làm chủ lập trình ứng dụng iOS với Swift và Xcode. Bạn sẽ học về giao diện người dùng với UIKit, quản lý dữ liệu bằng Core Data, và tích hợp các dịch vụ như iCloud, Apple Maps. Khóa học còn hướng dẫn xây dựng ứng dụng đa màn hình, xử lý sự kiện cảm ứng, và tối ưu hóa hiệu năng ứng dụng. Bạn sẽ thực hành phát triển các ứng dụng thực tế, từ ý tưởng đến triển khai lên App Store. Kết thúc khóa học, bạn sẽ có kỹ năng vững chắc để phát triển ứng dụng iOS hiện đại và sáng tạo.'),
(25, '10 weeks', 'C++ Programming', '2025-09-10', '2025-11-19', 2, 'Khóa học \"C++ Programming\" cung cấp nền tảng vững chắc về lập trình C++, từ cơ bản đến nâng cao. Bạn sẽ học về cú pháp, kiểu dữ liệu, hàm, con trỏ, quản lý bộ nhớ, và lập trình hướng đối tượng (OOP). Khóa học còn hướng dẫn sử dụng thư viện chuẩn STL, xử lý file, và xây dựng các ứng dụng thực tế như quản lý sinh viên, hệ thống bán hàng. Ngoài ra, bạn sẽ được làm quen với các thuật toán cơ bản, tối ưu hóa mã nguồn, và chuẩn bị cho các kỳ thi lập trình. Sau khóa học, bạn sẽ tự tin phát triển các dự án phần mềm bằng C++.'),
(26, '8 weeks', 'Algorithms & Data Structures', '2025-09-22', '2025-11-17', 1, 'Khóa học \"Algorithms & Data Structures\" giúp bạn hiểu sâu về các cấu trúc dữ liệu (mảng, danh sách liên kết, stack, queue, cây, đồ thị) và thuật toán cơ bản (sắp xếp, tìm kiếm, đệ quy, chia để trị). Bạn sẽ học cách phân tích độ phức tạp thuật toán, tối ưu hóa chương trình, và áp dụng vào giải quyết các bài toán thực tế. Khóa học còn hướng dẫn xây dựng và triển khai các thuật toán trên nhiều ngôn ngữ lập trình, thực hành qua các bài tập và dự án nhỏ. Đây là nền tảng quan trọng cho mọi lập trình viên muốn phát triển kỹ năng giải quyết vấn đề và chuẩn bị cho phỏng vấn kỹ thuật.');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `assignment` text DEFAULT NULL,
  `document_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`document_id`, `course_id`, `assignment`, `document_link`) VALUES
(3, 1, 'mẫu', 'uploads/1753621663_SDLC_Lecture-No.2_-_Project_Selection_and_Management.pdf'),
(4, 26, 'tài liệu ', 'uploads/1753769940_Business Process Support - Asm 1 Guideline.docx'),
(5, 26, 'bài tập ', 'uploads/1753769955_FRONTSHEET ASM FINAL REPORT (IND) (9).docx'),
(6, 26, 'bài tập ', 'uploads/1753770049_FRONTSHEET ASM FINAL REPORT (IND) (9).docx');

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `forum_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`forum_id`, `course_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `student_id`, `teacher_id`, `forum_id`, `content`) VALUES
(1, 1, NULL, 1, 'Em chưa hiểu phần div trong HTML ạ'),
(2, NULL, 1, 1, 'Các bạn lưu ý deadline nộp bài tuần này là Chủ nhật.'),
(3, 2, NULL, 2, 'Cho em xin tài liệu tham khảo về UML'),
(4, NULL, 1, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`registration_id`, `student_id`, `course_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 2),
(4, 1, 2),
(5, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'teacher'),
(3, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `email`, `password`, `role_id`, `full_name`, `dob`, `address`, `gender`) VALUES
(1, 'nguyenvana@btec.edu.vn', 'student123', 3, NULL, NULL, NULL, NULL),
(2, 'tranthib@btec.edu.vn', 'student456', 3, NULL, NULL, NULL, NULL),
(3, 'lethic@btec.edu.vn', 'student789', 3, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `email`, `password`, `role_id`, `full_name`, `dob`, `address`, `gender`) VALUES
(1, 'thaylong@btec.edu.vn', 'teach123', 2, 'Thầy Long', '1980-05-15', 'Hà Nội', 'Nam'),
(2, 'coga@btec.edu.vn', 'teach456', 2, 'Cô Gà', '1985-08-22', 'Đà Nẵng', 'Nữ'),
(7, 'teacher4@btec.edu.vn', '123456', 2, 'Nguyen Van D', '1980-05-10', NULL, 'male'),
(8, 'teacher5@btec.edu.vn', '123456', 2, 'Pham Thi E', '1985-08-20', NULL, 'female'),
(9, 'teacher6@btec.edu.vn', '123456', 2, 'Le Van F', '1979-12-01', NULL, 'male');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`forum_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `forum_id` (`forum_id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `forum`
--
ALTER TABLE `forum`
  MODIFY `forum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `assignment`
--
ALTER TABLE `assignment`
  ADD CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `document_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `forum_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`forum_id`);

--
-- Constraints for table `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `registration_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `submission`
--
ALTER TABLE `submission`
  ADD CONSTRAINT `submission_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`),
  ADD CONSTRAINT `submission_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
