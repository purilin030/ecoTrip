<?php
// 这个文件负责把 MySQL 的用户数据变成 JSON 格式
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // 允许跨域访问

$conn = new mysqli("localhost", "root", "", "ecotrip");

if ($conn->connect_error) {
    die(json_encode(["error" => "连接失败"]));
}

// 查出所有用户的关键信息
$sql = "SELECT User_ID, First_Name, Last_Name,Caption,User_DOB,Phone_num, Email, Point, Team_ID,RedeemPoint,Role, FROM user WHERE Account_Status = 'active' AND Email IS NOT NULL AND Role != 1";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// 输出 JSON
echo json_encode($users);
$conn->close();
?>