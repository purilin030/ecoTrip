<?php
require '../database.php';

// 1. 设置 HTTP 头，告诉浏览器这是一个要下载的 CSV 文件
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="redemption_report_' . date('Y-m-d') . '.csv"');

// 2. 打开 PHP 输出流
$output = fopen('php://output', 'w');

// 3. 写入表头 (CSV Header)
fputcsv($output, ['Record ID', 'User ID', 'User Name', 'Reward Name', 'Quantity', 'Status', 'Date']);

// 4. 查询数据
$sql = "SELECT r.RedeemRecord_ID, r.Redeem_By, CONCAT(u.First_Name, ' ', u.Last_Name) as UserName, 
               r.Reward_Name, r.Redeem_Quantity, r.Status, r.Redeem_Date 
        FROM redeemrecord r 
        JOIN user u ON r.Redeem_By = u.User_ID 
        ORDER BY r.Redeem_Date DESC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// 5. 写入数据行
foreach ($rows as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>