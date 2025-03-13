<?php
// Kết nối MySQL
$servername = "localhost";
$username = "root";  // Thay bằng user MySQL của bạn
$password = "100803";  // Thay bằng mật khẩu MySQL
$database = "door_access";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ bảng access_log
$sql_access = "SELECT id, user_name, access_method, event_description, timestamp FROM access_log ORDER BY timestamp DESC";
$result_access = $conn->query($sql_access);

// Lấy dữ liệu từ bảng motion_log
$sql_motion = "SELECT id, detect_time, description FROM motion_log ORDER BY detect_time DESC";
$result_motion = $conn->query($sql_motion);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhật ký hệ thống</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #007BFF; color: white; }
    </style>
</head>
<body>

    <h2>📋 Nhật ký truy cập cửa</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Người dùng</th>
            <th>Phương thức</th>
            <th>Mô tả</th>
            <th>Thời gian</th>
        </tr>
        <?php
        if ($result_access->num_rows > 0) {
            while ($row = $result_access->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['user_name']}</td>
                        <td>{$row['access_method']}</td>
                        <td>{$row['event_description']}</td>
                        <td>{$row['timestamp']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Không có dữ liệu.</td></tr>";
        }
        ?>
    </table>

    <h2>📋 Nhật ký phát hiện chuyển động</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Thời gian</th>
            <th>Mô tả</th>
        </tr>
        <?php
        if ($result_motion->num_rows > 0) {
            while ($row = $result_motion->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['detect_time']}</td>
                        <td>{$row['description']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Không có dữ liệu.</td></tr>";
        }
        ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>
