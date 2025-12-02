<?php
// database.php (混合版 - 兼容所有人的代码)
 
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ecotrip";
 
// --- 1. MySQLi 连接 (保留给队友和 header.php 使用) ---
$con = mysqli_connect($host, $user, $pass, $dbname);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
 
// --- 2. PDO 连接 (添加给你自己的 Module 4 使用) ---
// 你的事务处理和防注入查询需要用到 $pdo
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}
?>