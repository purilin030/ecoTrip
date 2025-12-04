<?php
// 允许跨域，方便 JS 调用
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "ecotrip");

// 联合查询，获取挑战详情和分类名
$sql = "
    SELECT 
        c.Challenge_ID, c.Title, c.Detailed_Description, c.Difficulty, c.Points, 
        cat.CategoryName 
    FROM challenge c
    JOIN category cat ON c.Category_ID = cat.CategoryID
    WHERE c.status = 'Active'
";

$result = $conn->query($sql);
$data = array();

while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>