<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// 连接数据库
$conn = new mysqli("localhost", "root", "", "ecotrip");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "数据库连接失败"]));
}

$email = $_POST['email'];
$password = $_POST['password']; // 这里的 password 是 APP 发来的明文 (如 123456)

// 1. 先只根据 Email 查出用户 (同时要把 Password 字段查出来用于比对)
// ⚠️ 注意：这里最好用 bind_param 防止 SQL 注入，但为了配合你的 XAMPP 简易环境，这里先用 real_escape_string
$safe_email = $conn->real_escape_string($email);
$sql = "SELECT User_ID, First_Name, Last_Name, Email, Password FROM user WHERE Email = '$safe_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $db_password_hash = $row['Password']; // 数据库里的加密字符串

    // 2. 关键步骤：验证密码
    // 你的 Web 端如果是用 password_hash() 加密的，这里必须用 password_verify()
    if (md5($password) === $db_password_hash) {
        // ✅ 密码正确！
        echo json_encode([
            "status" => "success",
            "user_id" => $row['User_ID'],
            "name" => $row['First_Name'] . " " . $row['Last_Name'],
            "email" => $row['Email']
        ]);
    } else {
        // ❌ 密码错误
        echo json_encode(["status" => "error", "message" => "密码错误"]);
    }
} else {
    // ❌ 账号不存在
    echo json_encode(["status" => "error", "message" => "账号不存在"]);
}

$conn->close();
?>