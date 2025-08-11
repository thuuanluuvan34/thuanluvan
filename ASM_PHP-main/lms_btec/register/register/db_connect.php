<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "lms_btec";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>