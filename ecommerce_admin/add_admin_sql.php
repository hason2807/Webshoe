<?php
// Tạo hash password cho admin
$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Hiển thị câu lệnh SQL để copy vào phpMyAdmin hoặc công cụ quản lý MySQL
echo "-- Tạo bảng admins nếu chưa tồn tại\n";
echo "CREATE TABLE IF NOT EXISTS `admins` (\n";
echo "    `id` INT AUTO_INCREMENT PRIMARY KEY,\n";
echo "    `username` VARCHAR(50) UNIQUE NOT NULL,\n";
echo "    `password` VARCHAR(255) NOT NULL\n";
echo ");\n\n";

echo "-- Thêm tài khoản admin với mật khẩu đã hash\n";
echo "-- Mật khẩu: $password\n";
echo "INSERT INTO `admins` (`username`, `password`) VALUES \n";
echo "('$username', '$hashed_password');\n";

echo "\n-- Thông tin đăng nhập:\n";
echo "-- Username: $username\n";
echo "-- Password: $password\n";
?>