<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "ecotrip");
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) { die(json_encode(["status" => "error", "msg" => "无数据"])); }

// 1. 处理用户 (如果 MySQL 里没有这个 Google 用户，为了不报错，我们可以先分配给一个默认用户，或者新建一个)
// 这里简单处理：假设你已经做过用户同步，或者根据 email 查找
$email = $conn->real_escape_string($data['email']);
$user_id = 15; // ⚠️ 偷懒写法：默认归给 John Sam (User_ID 15)，你可以写查询逻辑去匹配 Email

$sql_check_user = "SELECT User_ID FROM user WHERE Email = '$email'";
$res = $conn->query($sql_check_user);
if($res->num_rows > 0) {
    $user_id = $res->fetch_assoc()['User_ID'];
}

// 2. 插入 Submissions 表
$challenge_title = $conn->real_escape_string($data['challengeTitle']);
// 尝试通过标题反查 Challenge_ID，查不到就设为 1
$c_id = 1; 
$res_c = $conn->query("SELECT Challenge_ID FROM challenge WHERE Title = '$challenge_title' LIMIT 1");
if($res_c->num_rows > 0) $c_id = $res_c->fetch_assoc()['Challenge_ID'];

$photo = $conn->real_escape_string($data['photoUrl']);
$date = date("Y-m-d");
$status = "Pending";
$caption = "From App: " . $challenge_title;

// 构造 SQL (对应你的 database.sql)
$sql = "INSERT INTO submissions (Challenge_ID, User_ID, Caption, Photo, Submission_date, Status, image_hash) 
        VALUES ('$c_id', '$user_id', '$caption', '$photo', '$date', '$status', 'app_hash_placeholder')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "error", "error" => $conn->error]);
}
$conn->close();
?>