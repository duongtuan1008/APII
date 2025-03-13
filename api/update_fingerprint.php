<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root"; 
$password = "100803"; 
$dbname = "Users"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// 🟢 Kiểm tra dữ liệu từ Python
if (!isset($_POST['username']) || !isset($_FILES["fingerprint"])) {
    die("❌ Thiếu dữ liệu đầu vào.");
}

$username = trim($_POST['username']);
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 🟢 Kiểm tra user đã tồn tại chưa
$sql = "SELECT image_path FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_images = json_decode($row['image_path'], true);
    if (!is_array($current_images)) {
        $current_images = [];
    }
    echo "🔹 Người dùng '$username' đã tồn tại. Cập nhật vân tay và ảnh...\n";
} else {
    echo "🔹 Người dùng '$username' chưa có, thêm mới...\n";
    $current_images = [];
}

// 🟢 Lưu mẫu vân tay vào thư mục
$fingerprint_path = $upload_dir . "fingerprint_" . $username . ".dat";
if (!move_uploaded_file($_FILES["fingerprint"]["tmp_name"], $fingerprint_path)) {
    die("❌ Không thể lưu mẫu vân tay.");
}

// 🟢 Kiểm tra nếu có ảnh được gửi từ Python
if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
    echo "📥 Đã nhận " . count($_FILES['images']['name']) . " ảnh từ Python.\n";
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['tmp_name'][$key] != "") {
            $image_path = $upload_dir . basename($_FILES['images']['name'][$key]);
            if (move_uploaded_file($tmp_name, $image_path)) {
                echo "✅ Lưu ảnh: $image_path\n";
                $current_images[] = $image_path;  // Thêm tất cả ảnh vào danh sách
            } else {
                echo "❌ Không thể lưu ảnh: " . $_FILES['images']['name'][$key] . "\n";
            }
        }
    }
} else {
    echo "⚠ Không có ảnh nào được gửi lên từ Python. Giữ nguyên danh sách ảnh cũ.\n";
}

// 🟢 Chuyển danh sách ảnh thành JSON để lưu vào MySQL
$image_json = json_encode(array_unique($current_images)); // Loại bỏ ảnh trùng
echo "📤 Lưu vào MySQL: $image_json\n";

// 🟢 Cập nhật hoặc thêm mới vào MySQL
$sql = "INSERT INTO users (username, image_path, fingerprint_id, register_time) 
        VALUES ('$username', '$image_json', '$fingerprint_path', NOW())
        ON DUPLICATE KEY UPDATE 
        image_path = '$image_json', fingerprint_id = '$fingerprint_path', register_time = NOW()";

if ($conn->query($sql) === TRUE) {
    echo "✅ Cập nhật dữ liệu thành công cho '$username'!";
} else {
    echo "❌ Lỗi MySQL: " . $conn->error;
}

$conn->close();
?>
