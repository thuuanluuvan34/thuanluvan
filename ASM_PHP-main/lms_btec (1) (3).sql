-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2025 at 07:20 AM
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
  `password` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `role_id`) VALUES
(4, 'admin@gmail.com', '$2y$10$ZozE62naDSss77EwYuBOgOnC0L6.mJlU/kqdnhQUuuQcV4NT/DDhG', 1);

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
(19, '10 weeks', 'PHP Advanced', '2025-09-01', '2025-11-10', 7, 'Khóa học \"PHP Advanced\"...'),
(20, '8 weeks', 'JavaScript Mastery', '2025-09-15', '2025-11-10', 7, 'Khóa học \"JavaScript Mastery\"...'),
(21, '12 weeks', 'Python for Data Science', '2025-10-01', '2025-12-24', 8, 'Khóa học \"Python for Data Science\"...'),
(22, '10 weeks', 'Machine Learning Basics', '2025-09-20', '2025-11-29', 8, 'Khóa học \"Machine Learning Basics\"...'),
(23, '10 weeks', 'Android App Development', '2025-09-05', '2025-11-14', 9, 'Khóa học \"Android App Development\"...'),
(24, '8 weeks', 'iOS App Development', '2025-09-18', '2025-11-13', 9, 'Khóa học \"iOS App Development\"...'),
(25, '10 weeks', 'C++ Programming', '2025-09-10', '2025-11-19', 2, 'Khóa học \"C++ Programming\"...'),
(26, '8 weeks', 'Algorithms & Data Structures', '2025-09-22', '2025-11-17', 1, 'Khóa học \"Algorithms & Data Structures\"...');

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
  `password` varchar(255) DEFAULT NULL,
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
(1, 'nguyenvana@btec.edu.vn', '$2y$10$rc7TSlfn/RBsnfvv3vIfyutPqZ9N78B1HMBFxmalFDPXUuIqsB2yq', 3, NULL, NULL, NULL, NULL),
(2, 'tranthib@btec.edu.vn', '$2y$10$VsR.UO9W/IpKGoL3VmMdou3t9U3.5xDA6y9aP61cWKo4fNRvA4NKq', 3, NULL, NULL, NULL, NULL),
(3, 'lethic@btec.edu.vn', '$2y$10$rrVOT9jD0pM.QDrGNDLWR.rHS0g9ChZT.ap3qMYbg0/Zpv330gpHa', 3, NULL, NULL, NULL, NULL);

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

--
-- Dumping data for table `submission`
--

INSERT INTO `submission` (`submission_id`, `assignment_id`, `student_id`, `file_path`, `submitted_at`) VALUES
(1, 1, 1, 'student_submissions/1754665834_lms_btec.sql', '2025-08-08 22:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
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
(1, 'thaylong@btec.edu.vn', '$2y$10$WdomzGEhM1KLA4h9Yi757.PF6Z0i9PZoYfEtcnjwVJRafcWINMTWW', 2, 'Thầy Long', '1980-05-15', 'Hà Nội', 'Nam'),
(2, 'coga@btec.edu.vn', '$2y$10$ZNm4vw42v.oFkSS4ArseE.5KPHF4qno75v00Z070vzh/qj6vyxAYq', 2, 'Cô Gà', '1985-08-22', 'Đà Nẵng', 'Nữ'),
(7, 'teacher4@btec.edu.vn', '$2y$10$F4ZQ/FtXOXDOAhT0yR4i6OFLqV2IGxmwbsfPXYbV/yo6wA70AXUWe', 2, 'Nguyen Van D', '1980-05-10', NULL, 'male'),
(8, 'teacher5@btec.edu.vn', '$2y$10$vyxuO9ISOEg0ZFeMAzCq3uWgsUdXIfu7aMoO1Dk8j/CV871.CChlG', 2, 'Pham Thi E', '1985-08-20', NULL, 'female'),
(9, 'teacher6@btec.edu.vn', '$2y$10$vwDFtRK2nYWGPkaheS5wI.RPtAIedqebPeHN/m1Y66uZ4PROcirIy', 2, 'Le Van F', '1979-12-01', NULL, 'male');

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
