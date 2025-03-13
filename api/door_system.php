<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
// 1️⃣ Kết nối MySQL
$servername = "localhost";
$username = "root";  // Thay bằng user MySQL của bạn
$password = "100803";      // Nếu có mật khẩu, nhập vào đây
$database = "door_access";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// 2️⃣ Nếu nhận dữ liệu từ ESP32 (Gửi bằng phương thức POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_uid = $_POST["rfid_uid"] ?? '';
    $action = $_POST["action"] ?? '';

    if ($rfid_uid != '' && $action != '') {
        $sql = "INSERT INTO access_logs (rfid_uid, action) VALUES ('$rfid_uid', '$action')";
        if ($conn->query($sql) === TRUE) {
            echo "✅ Dữ liệu đã được lưu!";
        } else {
            echo "❌ Lỗi khi lưu dữ liệu: " . $conn->error;
        }
    } else {
        echo "⚠️ Dữ liệu không hợp lệ!";
    }
}

// 3️⃣ Nếu không có dữ liệu POST, hiển thị lịch sử quẹt thẻ
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "<h2>📋 Lịch sử mở cửa</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>RFID UID</th>
                <th>Hành động</th>
                <th>Thời gian</th>
            </tr>";

    $sql = "SELECT * FROM access_logs ORDER BY timestamp DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id"]."</td>
                    <td>".$row["rfid_uid"]."</td>
                    <td>".$row["action"]."</td>
                    <td>".$row["timestamp"]."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Chưa có dữ liệu</td></tr>";
    }
    echo "</table>";
}

$conn->close();
?>
