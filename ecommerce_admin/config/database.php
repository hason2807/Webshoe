<?php
// Thông tin kết nối database
$host = 'localhost';
$dbname = 'shoe_store'; // Đảm bảo tên database chính xác
$username = 'root';     // Thay đổi nếu cần
$password = '';         // Thay đổi nếu cần

// Tạo kết nối PDO với try-catch để bắt lỗi
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Cấu hình PDO để hiển thị lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

// Hàm để sanitize dữ liệu đầu vào
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>