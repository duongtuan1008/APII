<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root"; 
$password = "100803"; 
$dbname = "Users"; 

// 🟢 Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// 🟢 Kiểm tra dữ liệu từ Python
if (!isset($_POST['username']) || !isset($_POST['image_paths']) || !isset($_FILES["fingerprint"])) {
    die("❌ Thiếu dữ liệu đầu vào từ Python.");
}

$username = $_POST['username'];
$image_paths = $_POST['image_paths'];  // Lấy danh sách ảnh dưới dạng JSON

// 🟢 Lưu mẫu vân tay
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$fingerprint_path = $upload_dir . basename($_FILES["fingerprint"]["name"]);
if (!move_uploaded_file($_FILES["fingerprint"]["tmp_name"], $fingerprint_path)) {
    die("❌ Không thể lưu mẫu vân tay.");
}

// 🟢 Lưu vào MySQL (lưu JSON trong cột image_path)
$sql = "INSERT INTO users (username, image_path, fingerprint_id, register_time) 
        VALUES ('$username', '$image_paths', '$fingerprint_path', NOW())";

if ($conn->query($sql) === TRUE) {
    echo "✅ Dữ liệu đã được lưu thành công!";
} else {
    echo "❌ Lỗi MySQL: " . $conn->error;
}

$conn->close();
?>
