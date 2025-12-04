<?php
require '../database.php'; 
include '../header.php';
include '../background.php'; 
// --- 逻辑处理区域 ---

// 获取参数
$mode = $_GET['mode'] ?? 'individual'; // individual 或 team
$period = $_GET['period'] ?? 'all';    // all, 7d, 30d

// 定义日期筛选条件
$dateCondition = "";
if ($period === '7d') {
    $dateCondition = "AND p.Earned_Date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($period === '30d') {
    $dateCondition = "AND p.Earned_Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// 构建 SQL 查询
if ($mode === 'individual') {
    if ($period === 'all') {
        // [个人 + 总榜]
        $sql = "SELECT CONCAT(First_Name, ' ', Last_Name) AS Name, Avatar, Point AS totalPoints, NULL AS LastUpdate FROM user ORDER BY Point DESC LIMIT 50";
    } else {
        // [个人 + 时间段]
        $sql = "SELECT CONCAT(u.First_Name, ' ', u.Last_Name) AS Name, u.Avatar, COALESCE(SUM(p.Points_Earned), 0) AS totalPoints, MAX(p.Earned_Date) AS LastUpdate FROM user u LEFT JOIN pointsledger p ON u.User_ID = p.User_ID $dateCondition GROUP BY u.User_ID ORDER BY totalPoints DESC LIMIT 50";
    }
} else {
    // [团队榜]
    // 简化 SQL 逻辑以适应展示
    $joinPart = ($period === 'all') ? "LEFT JOIN pointsledger p ON u.User_ID = p.User_ID" : "LEFT JOIN pointsledger p ON u.User_ID = p.User_ID $dateCondition";
    $calcPart = ($period === 'all') ? "COALESCE(SUM(u.Point), 0)" : "COALESCE(SUM(p.Points_Earned), 0)";
    
    $sql = "SELECT t.Team_ID, t.Team_name AS Name, NULL as Avatar, $calcPart AS totalPoints, MAX(p.Earned_Date) AS LastUpdate FROM team t LEFT JOIN user u ON t.Team_ID = u.Team_ID $joinPart GROUP BY t.Team_ID, t.Team_name ORDER BY totalPoints DESC LIMIT 50";
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 辅助函数：处理头像 ---
function getAvatarUrl($avatarPath, $name, $mode) {
    $default = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random&color=fff&size=128";
    if ($mode === 'team') return $default;
    if (!empty($avatarPath)) return "/ecotrip/avatars/" . basename($avatarPath);
    return $default;
}

// --- 预处理数据 ---
foreach ($rows as &$row) {
    $row['display_avatar'] = getAvatarUrl($row['Avatar'] ?? '', $row['Name'], $mode);
}
unset($row);

// 分离前三名和其余名单
$top3 = array_slice($rows, 0, 3);
$rest = array_slice($rows, 3);
$rank = 4;

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
                    <option value="7d" <?= $period == '7d' ? 'selected' : '' ?>>Last 7 days</option>
                    <option value="30d" <?= $period == '30d' ? 'selected' : '' ?>>Last 30 days</option>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($top3)): ?>
    <div class="flex justify-center items-end gap-4 mb-12 mt-8">
        
        <?php if (isset($top3[1])): $p2 = $top3[1]; ?>
        <div class="flex flex-col items-center order-1">
            <div class="relative">
                <img src="<?= $p2['display_avatar'] ?>" class="w-16 h-16 rounded-full border-4 border-gray-300 shadow-md object-cover">
                <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-gray-200 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full border border-gray-300">#2</div>
            </div>
            <div class="mt-4 text-center">
                <p class="font-bold text-gray-800 text-sm truncate w-24"><?= htmlspecialchars($p2['Name']) ?></p>
                <p class="text-gray-500 text-xs font-semibold"><?= number_format($p2['totalPoints']) ?> pts</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($top3[0])): $p1 = $top3[0]; ?>
        <div class="flex flex-col items-center order-2 z-10 -mt-6">
            <div class="relative">
                <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-2xl animate-bounce">👑</div>
                <img src="<?= $p1['display_avatar'] ?>" class="w-24 h-24 rounded-full border-4 border-yellow-400 shadow-lg object-cover">
                <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-yellow-400 text-yellow-900 text-sm font-bold px-3 py-0.5 rounded-full border border-yellow-500">#1</div>
            </div>
            <div class="mt-5 text-center">
                <p class="font-bold text-gray-900 text-base truncate w-32"><?= htmlspecialchars($p1['Name']) ?></p>
                <p class="text-yellow-600 text-sm font-bold"><?= number_format($p1['totalPoints']) ?> pts</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($top3[2])): $p3 = $top3[2]; ?>
        <div class="flex flex-col items-center order-3">
            <div class="relative">
                <img src="<?= $p3['display_avatar'] ?>" class="w-16 h-16 rounded-full border-4 border-orange-300 shadow-md object-cover">
                <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-orange-100 text-orange-800 text-xs font-bold px-2 py-0.5 rounded-full border border-orange-200">#3</div>
            </div>
            <div class="mt-4 text-center">
                <p class="font-bold text-gray-800 text-sm truncate w-24"><?= htmlspecialchars($p3['Name']) ?></p>
                <p class="text-gray-500 text-xs font-semibold"><?= number_format($p3['totalPoints']) ?> pts</p>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        
        <div class="flex border-b border-gray-200">
            <a href="?mode=individual&period=<?= $period ?>" class="<?= $mode === 'individual' ? $activeTab : $inactiveTab ?>">Individual</a>
            <a href="?mode=team&period=<?= $period ?>" class="<?= $mode === 'team' ? $activeTab : $inactiveTab ?>">Teams</a>
        </div>

        <div class="grid grid-cols-12 gap-4 px-6 py-3 bg-gray-50/50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
            <div class="col-span-2 md:col-span-1">Rank</div>
            <div class="col-span-6 md:col-span-7">Name</div>
            <div class="col-span-2 text-right">Points</div>
            <div class="col-span-2 text-right hidden md:block">Last Update</div>
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
                <?php foreach($rest as $row): 
                    if ($row['totalPoints'] == 0) continue; 
                    $rowClass = "bg-white border-b border-gray-50 hover:bg-gray-50 items-center"; 
                ?>
                <div class="grid grid-cols-12 gap-4 px-6 py-4 <?php echo $rowClass; ?>">
                    <div class="col-span-2 md:col-span-1 flex items-center">
                        <span class="text-gray-400 font-bold">#<?= $rank ?></span>
                    </div>

                    <div class="col-span-6 md:col-span-7 flex items-center gap-3">
                        <img src="<?= htmlspecialchars($row['display_avatar']) ?>" class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover shadow-sm bg-white" alt="Avatar">
                        <span class="font-bold text-gray-800 truncate text-sm md:text-base">
                            <?= htmlspecialchars($row['Name']) ?>
                        </span>
                    </div>

                    <div class="col-span-2 flex items-center justify-end">
                        <span class="font-bold text-brand-600 text-sm md:text-base"><?= number_format($row['totalPoints']) ?></span>
                    </div>

                    <div class="col-span-2 hidden md:flex items-center justify-end text-xs text-gray-400">
                        <?= $row['LastUpdate'] ? date('M d', strtotime($row['LastUpdate'])) : '-' ?>
                    </div>
                </div>
                <?php $rank++; endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-center text-sm text-gray-500">
            Keep participating to improve your rank!
        </div>
    </div>
</main>
<?php
include '../footer.php';
?>
</body>
</html>