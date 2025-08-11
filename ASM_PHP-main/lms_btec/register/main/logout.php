<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Quay lại trang login. Đường dẫn này là từ main/ đến register/
header("Location: ../register/login.php");
exit;
?>