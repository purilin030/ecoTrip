<?php
// 1. 引入数据库连接 (确保里面定义了 $pdo)
require '../database.php'; 

// 2. 引入头部 (它包含了 session_start, html head, nav bar)
include '../header.php'; // 你的文件在同级目录，不需要 ../

// --- 逻辑处理区域 ---

// 获取参数
$mode = $_GET['mode'] ?? 'individual'; // individual 或 team
$period = $_GET['period'] ?? 'all';    // all, weekly, monthly

// 定义日期筛选条件 (SQL片段)
$dateCondition = "";
if ($period === '7d') {
    // 过去 7 天 (Rolling 7 Days)
    $dateCondition = "AND p.Earned_Date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($period === '30d') {
    // 过去 30 天 (Rolling 30 Days)
    $dateCondition = "AND p.Earned_Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}
// 构建 SQL 查询
if ($mode === 'individual') {
    if ($period === 'all') {
        // [个人 + 总榜]
        // 🔥 修改点：添加 Avatar 字段
        $sql = "
            SELECT 
                CONCAT(First_Name, ' ', Last_Name) AS Name,
                Avatar, 
                Point AS totalPoints,
                NULL AS LastUpdate
            FROM user 
            ORDER BY Point DESC
            LIMIT 50
        ";
    } else {
        // [个人 + 周/月榜]
        // 🔥 修改点：添加 u.Avatar 字段
        $sql = "
            SELECT 
                CONCAT(u.First_Name, ' ', u.Last_Name) AS Name,
                u.Avatar,
                COALESCE(SUM(p.Points_Earned), 0) AS totalPoints,
                MAX(p.Earned_Date) AS LastUpdate
            FROM user u
            LEFT JOIN pointsledger p 
                ON u.User_ID = p.User_ID
                $dateCondition 
            GROUP BY u.User_ID
            ORDER BY totalPoints DESC
            LIMIT 50
        ";
    }
} else {
    // [团队榜]
    if ($period === 'all') {
        // [修改点]：All Time 模式下，直接累加 User 表中的 Point 字段
        $sql = "
            SELECT 
                t.Team_ID,
                t.Team_name AS Name,
                NULL as Avatar, -- 团队模式占位，保持字段一致性方便后续处理（虽然这里没用到）
                COALESCE(SUM(u.Point), 0) AS totalPoints, 
                MAX(p.Earned_Date) AS LastUpdate
            FROM team t
            LEFT JOIN user u ON t.Team_ID = u.Team_ID
            LEFT JOIN pointsledger p ON u.User_ID = p.User_ID
            GROUP BY t.Team_ID, t.Team_name
            ORDER BY totalPoints DESC
            LIMIT 50
        ";
    } else {
        // [保持不变]：时间段筛选 (7d/30d) 必须依然使用 pointsledger 计算增量
        $sql = "
            SELECT 
                t.Team_ID,
                t.Team_name AS Name,
                NULL as Avatar, -- 团队模式占位
                COALESCE(SUM(p.Points_Earned), 0) AS totalPoints,
                MAX(p.Earned_Date) AS LastUpdate
            FROM team t
            LEFT JOIN user u ON t.Team_ID = u.Team_ID
            LEFT JOIN pointsledger p 
                ON u.User_ID = p.User_ID
                $dateCondition            
            GROUP BY t.Team_ID, t.Team_name
            ORDER BY totalPoints DESC
            LIMIT 50
        ";
    }
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rank = 1;

// 样式定义
$activeTab = "flex-1 py-4 text-center text-sm font-semibold text-brand-600 border-b-2 border-brand-600 bg-brand-50/50";
$inactiveTab = "flex-1 py-4 text-center text-sm font-medium text-gray-500 hover:text-gray-700";
?>

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="text-center md:text-left">
            <h1 class="text-3xl font-bold text-gray-900">Leaderboard</h1>
            <p class="mt-2 text-gray-500">See who's leading the charge.</p>
        </div>
        
        <form action="" method="GET" class="relative">
            <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
            
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <i class="fa-regular fa-calendar text-gray-400"></i>
                <select name="period" onchange="this.form.submit()" class="text-sm font-medium text-gray-700 bg-transparent outline-none cursor-pointer">
                    <option value="all" <?= $period == 'all' ? 'selected' : '' ?>>All Time</option>
                    <option value="7d" <?= $period == '7d' ? 'selected' : '' ?>>Last 7day</option>
                    <option value="30d" <?= $period == '30d' ? 'selected' : '' ?>>Last 30days</option>
                </select>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="flex border-b border-gray-200">
            <a href="?mode=individual&period=<?= $period ?>" class="<?= $mode === 'individual' ? $activeTab : $inactiveTab ?>">
                Individual
            </a>
            <a href="?mode=team&period=<?= $period ?>" class="<?= $mode === 'team' ? $activeTab : $inactiveTab ?>">
                Teams
            </a>
        </div>

        <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
            <div class="col-span-2">Rank</div>
            <div class="col-span-6">Name</div>
            <div class="col-span-2 text-right">Points</div>
            <div class="col-span-2 text-right">Last Update</div>
        </div>

        <div class="divide-y divide-gray-100">
            
            <?php if(empty($rows) || ($rows[0]['totalPoints'] == 0 && count($rows) == 1 && $rows[0]['totalPoints'] !== null)): ?> 
                <div class="p-12 text-center flex flex-col items-center justify-center text-gray-500">
                    <div class="bg-gray-100 p-4 rounded-full mb-3">
                        <i class="fa-solid fa-chart-simple text-gray-400 text-xl"></i>
                    </div>
                    <p>No points recorded for this period yet.</p>
                </div>
            <?php else: ?>
                
                <?php foreach($rows as $row): 
                    // 如果分数为0，跳过
                    if ($row['totalPoints'] == 0) continue; 

                    $rankDisplay = '';
                    if ($rank == 1) {
                        $rankDisplay = '<i class="fa-solid fa-trophy text-yellow-400 text-lg"></i>';
                    } elseif ($rank == 2) {
                        $rankDisplay = '<i class="fa-solid fa-trophy text-gray-400 text-lg"></i>';
                    } elseif ($rank == 3) {
                        $rankDisplay = '<i class="fa-solid fa-trophy text-amber-600 text-lg"></i>';
                    } else {
                        $rankDisplay = '<span class="text-gray-400 font-medium">#' . $rank . '</span>';
                    }

                    // === 🔥 头像处理逻辑 ===
                    $display_avatar = '';
                    $default_avatar = "https://ui-avatars.com/api/?name=" . urlencode($row['Name']) . "&background=random&color=fff&size=128";
                    
                    if ($mode === 'individual') {
                        // 个人模式：检查数据库是否有头像
                        if (!empty($row['Avatar'])) {
                            // 注意：假设 Leaderboard.php 在子目录，图片在根目录，需要 "../"
                            $display_avatar = "../" . $row['Avatar'];
                        } else {
                            $display_avatar = $default_avatar;
                        }
                    } else {
                        // 团队模式：继续使用首字母头像
                        $display_avatar = $default_avatar;
                    }
                ?>
                    <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center hover:bg-gray-50 transition-colors">
                        <div class="col-span-2 flex items-center pl-1">
                            <?= $rankDisplay ?>
                        </div>
                        
                        <div class="col-span-6 flex items-center gap-3">
                            <img src="<?= htmlspecialchars($display_avatar) ?>" class="w-8 h-8 rounded-full object-cover shadow-sm" alt="Avatar">
                            <span class="font-semibold text-gray-900 truncate">
                                <?= htmlspecialchars($row['Name']) ?>
                            </span>
                        </div>

                        <div class="col-span-2 text-right font-bold text-brand-600">
                            <?= number_format($row['totalPoints']) ?>
                        </div>

                        <div class="col-span-2 text-right text-xs text-gray-500">
                            <?= $row['LastUpdate'] ? date('M d', strtotime($row['LastUpdate'])) : '-' ?>
                        </div>
                    </div>
                <?php $rank++; endforeach; ?>

            <?php endif; ?>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-center text-sm text-gray-500">
            <?php if($period == 'weekly'): ?>
                Rankings reset every Monday.
            <?php elseif($period == 'monthly'): ?>
                Rankings reset on the 1st of every month.
            <?php else: ?>
                Keep participating to improve your rank!
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>