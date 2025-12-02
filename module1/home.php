<?php
session_start();
require 'database.php'; 

// 安全检查
if (!isset($_SESSION['Firstname'])) {
    header("Location: index.php");
    exit();
}

// 设置页面参数
$page_title = "ecoTrip - Home";
$extra_css = '<link rel="stylesheet" href="/../login.css">';

// 引入通用头部
include '../header.php';
?>

    <main class="relative w-full min-h-screen flex items-center justify-center">
        
        <div class="absolute inset-0 w-full h-full">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                 alt="Background" 
                 class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-4 px-6 py-12 md:px-12 md:py-16 text-center text-white bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl shadow-2xl">
            
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight mb-6 drop-shadow-lg">
                Welcome!
                
            </h1>

            <div class="w-24 h-1 bg-green-500 mx-auto mb-8 rounded-full shadow-lg"></div>

            <div class="space-y-6 text-lg md:text-xl font-light text-gray-100 leading-relaxed">
                
                <p>
                    <span class="font-semibold text-white">Welcome to EcoTrip.</span> 
                    We believe that exploring the world shouldn't cost the Earth. 
                    Every journey you take leaves a mark—let's make sure it's a green one.
                </p>

                <p>
                    Track your carbon footprint, discover sustainable travel options, and join a global community committed to reducing emissions.
                </p>

                <p>
                    Ready to challenge yourself? Complete daily eco-missions and earn rewards while protecting the nature we love.
                </p>

            </div>

            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="../module2/view_challenge.php" class="px-8 py-3 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-full transition-all shadow-lg transform hover:scale-105">
                    Start Challenge
                </a>
                <a href="#learn-more" class="px-8 py-3 bg-transparent border border-white hover:bg-white hover:text-green-900 text-white font-semibold rounded-full transition-all">
                    Learn More
                </a>
            </div>

        </div>

    </main>
    
    <footer class="absolute bottom-0 w-full py-4 text-center text-white/60 text-xs">
        &copy; 2025 ecoTrip Inc. Designed for a greener tomorrow.
    </footer>

</body>
</html>