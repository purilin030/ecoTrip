<?php
session_start();
require '../database.php';

// 1. 安全检查
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. 查询用户信息
$sql = "SELECT u.*, t.Team_name 
        FROM user u 
        LEFT JOIN team t ON u.Team_ID = t.Team_ID 
        WHERE u.User_ID = '$user_id'";

$result = mysqli_query($con, $sql);
$user_info = mysqli_fetch_assoc($result);

// 3. 处理数据显示逻辑
$dob_display = !empty($user_info['User_DOB']) ? $user_info['User_DOB'] : '<span class="text-gray-400 italic">N/A</span>';
$team_display = !empty($user_info['Team_name']) ? $user_info['Team_name'] : '<span class="text-gray-400 italic">No Team joined</span>';

// Detect role and change 
$role_code = $user_info['Role'];
if ($role_code == 1) {
    $role_display = "Admin";
    $role_badge_color = "bg-red-900 text-white"; // 可选：给不同角色不同颜色
} elseif ($role_code == 2) {
    $role_display = "Moderator";
    $role_badge_color = "bg-blue-900 text-white";
} else {
    $role_display = "Member";
    $role_badge_color = "bg-green-500 text-white"; // 默认颜色
}

// 设置页面标题，并引入 Header (Header 会自动处理 HTML 头部、Tailwind、导航栏和头像)
$page_title = "User Profile - ecoTrip";
include '../header.php';
?>

<!-- Start code here -->

<main class="flex-grow w-full px-8 py-12">
    

</main>

<footer class="bg-white border-t border-gray-200">
    <div class="w-full py-8 px-8">
        <p class="text-center text-sm text-gray-400">
            &copy; 2025 ecoTrip Inc. All rights reserved. Designed for a greener tomorrow.
        </p>
    </div>
</footer>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ecotrip/background.php'; ?>
</body>

</html>