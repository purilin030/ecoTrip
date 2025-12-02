<?php
// === module3/admin_process_approval.php ===
// 引入数据库连接
require_once __DIR__ . '/../database.php';

// 开启 Session 以获取当前登录的管理员 ID
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 检查是否登录 (假设 $_SESSION['user_id'] 是当前管理员)
if (!isset($_SESSION['user_id'])) {
    die("Error: Access denied. Please log in first.");
}
$admin_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sub_id = $_POST['submission_id'];
    $action = $_POST['action']; // 'approve' 或 'deny'
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    $action_date = date("Y-m-d"); 
    
    // 准备 SQL 语句变量
    $status_text = "";
    
    if ($action == 'approve') {
        $status_text = "Approved";
        
        // --- 1. QR Code 生成逻辑 ---
        $folder_path = "../qr_code/"; 
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        
        // 准备文件名
        $file_name = "qr_" . $sub_id . "_" . time() . ".png";
        $local_file_path = $folder_path . $file_name;
        // 存入数据库的相对路径 (根据你其他文件的习惯，这里可能需要调整路径前缀，保持一致)
        $db_qr_path = "../qr_code/" . $file_name;

        // 从 API 下载图片
        $qr_content = "Submission-" . $sub_id . "-Verified"; 
        $api_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_content);
        
        // 使用 curl 或 file_get_contents
        $image_data = file_get_contents($api_url);
        
        if ($image_data) {
            file_put_contents($local_file_path, $image_data);
            
            // 更新 Submission 表 (状态 + QR Code + Note)
            // 注意：表名为 submission (单数)
            $sql = "UPDATE submissions SET Status = 'Approved', Verification_note = ?, QR_Code = ? WHERE Submission_ID = ?";
            $stmt = $con->prepare($sql);
            // bind_param: s (note), s (qr_path), i (id)
            $stmt->bind_param("ssi", $note, $db_qr_path, $sub_id);
            $stmt->execute();
        } else {
            die("Error generating QR Code API response.");
        }

    } elseif ($action == 'deny') {
        $status_text = "Denied";
        
        // 更新 Submission 表 (只更新状态和备注)
        $sql = "UPDATE submissions SET Status = 'Denied', Verification_note = ? WHERE Submission_ID = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $note, $sub_id);
        $stmt->execute();
    }

    // --- 2. 插入记录到 Moderation 表 ---
    if (!empty($status_text)) {
        // 假设 moderation 表字段对应正确
        $mod_sql = "INSERT INTO moderation (Submission_ID, User_ID, Action, Action_date) VALUES (?, ?, ?, ?)";
        $mod_stmt = $con->prepare($mod_sql);
        $mod_stmt->bind_param("iiss", $sub_id, $admin_id, $status_text, $action_date);
        
        if ($mod_stmt->execute()) {
            // 成功后跳转回列表
            echo "<script>
                alert('Submission has been " . strtolower($status_text) . " successfully.'); 
                window.location.href='admin_verification_list.php';
            </script>";
        } else {
            echo "Error logging moderation history: " . $con->error;
        }
    }
}
?>