<?php

$servername = "localhost";
$dbname = "esp_data";
$username = "root";
$password = "100803";

// API Key cần trùng với ESP32 để xác thực dữ liệu
$api_key_value = "tPmAT5Ab3j7F9";

$api_key = $device_id = $sensor = $location = $temperature = $humidity = $light = $gas = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);

    if ($api_key == $api_key_value) {
        $device_id = test_input($_POST["device_id"]);
        $sensor = test_input($_POST["sensor"]);
        $location = test_input($_POST["location"]);
        $temperature = test_input($_POST["temperature"]);
        $humidity = test_input($_POST["humidity"]);
        $light = test_input($_POST["light"]);
        $gas = test_input($_POST["gas"]);

        // Tạo kết nối đến MySQL
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Chuẩn bị truy vấn SQL với prepared statement để tránh SQL Injection
        $sql = "INSERT INTO SensorData (device_id, sensor, location, temperature, humidity, light, gas) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssdddd", $device_id, $sensor, $location, $temperature, $humidity, $light, $gas);
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing query: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Wrong API Key provided.";
    }
} else {
    echo "No data posted with HTTP POST.";
}

// Hàm kiểm tra dữ liệu đầu vào để tránh lỗi và tấn công XSS
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
