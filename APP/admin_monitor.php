<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>系统数据迁移</title>
    <style>
        body { font-family: sans-serif; padding: 50px; text-align: center; }
        button { padding: 15px 30px; font-size: 18px; cursor: pointer; background: #00695C; color: white; border: none; border-radius: 8px; }
        button:disabled { background: gray; }
        #log { margin-top: 20px; text-align: left; background: #eee; padding: 20px; border-radius: 8px; height: 300px; overflow-y: scroll; }
    </style>
</head>
<body>

    <h1>🚀 数据库迁移工具</h1>
    <p>将 MySQL (XAMPP) 的老用户数据，搬运到 Firebase Cloud Firestore。</p>
    <p>这样老用户用 Google 登录 App 时，就能继承以前的积分！</p>
    
    <button id="btnMigrate" onclick="startMigration()">开始迁移</button>

    <div id="log">等待操作...</div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getFirestore, collection, doc, setDoc, getDoc } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

        // 🔴 换成你的 Web Config
        const firebaseConfig = {
            apiKey: "你的API_KEY",
            authDomain: "ecotrip-miniproject.firebaseapp.com",
            projectId: "ecotrip-miniproject",
            storageBucket: "ecotrip-miniproject.firebasestorage.app",
            messagingSenderId: "...",
            appId: "..."
        };

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);

        window.startMigration = async () => {
            const btn = document.getElementById('btnMigrate');
            const log = document.getElementById('log');
            btn.disabled = true;
            btn.innerText = "正在迁移...";
            
            log.innerHTML += "<br>1. 正在从 PHP 获取 MySQL 数据...";

            try {
                // 1. 从刚才写的 PHP 接口拿数据
                const response = await fetch('http://localhost/ecotrip/app/api/get_users.php');
                const mysqlUsers = await response.json();
                
                log.innerHTML += `<br>✅ 获取成功！共找到 ${mysqlUsers.length} 个老用户。`;

                // 2. 循环写入 Firebase
                for (const user of mysqlUsers) {
                    // ⚠️ 关键逻辑：我们用用户的 Email 作为 ID 查查看
                    // 实际上，Firebase Auth 的 ID 是随机的。
                    // 但我们可以建一个临时集合叫 'legacy_users'，或者直接存入 'users' 并用 email 做索引。
                    
                    // 这里我们采用策略：把老数据存入 'users' 集合，使用 Email 作为文档 ID (或者让 App 登录时去匹配)
                    // 为了演示简单，我们直接把 Email 当作 Document ID 存进去。
                    // 这样当 App 登录时，如果发现 Google 邮箱和这个一样，就读取数据。
                    
                    // 注意：这里我们无法直接获得 Google 的 UID，所以我们先存个以 Email 命名的文档作为“占位符”
                    // 稍后在 App 端我们会修改逻辑去认领这个数据。
                    
                    // 或者更简单的：我们创建一个叫 'legacy_import' 的集合
                    
                    const userRef = doc(db, "users_legacy", user.Email); 
                    
                    await setDoc(userRef, {
                        originalId: user.User_ID,
                        name: user.First_Name + " " + user.Last_Name,
                        email: user.Email,
                        points: parseInt(user.Point),
                        teamId: user.Team_ID,
                        migrated: false // 标记未被认领
                    });

                    log.innerHTML += `<br>➡️ 已迁移: ${user.Email} (${user.Point}分)`;
                }

                log.innerHTML += "<br>🎉🎉🎉 全部迁移完成！";
                alert("迁移完成！");

            } catch (error) {
                log.innerHTML += `<br>❌ 错误: ${error.message}`;
                console.error(error);
            } finally {
                btn.disabled = false;
                btn.innerText = "再次迁移";
            }
        };
    </script>
</body>
</html>