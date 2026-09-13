<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

$searchPattern = '/<!-- Hero Section \(Promotional Banner\) -->.*?<\/section>/s';

$replacePattern = <<<HTML
<!-- Hero Section (Promotional Banner) -->
    <section class="relative bg-black overflow-hidden border-b border-gray-800">
        <!-- Astronomy Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/images/cosmos.jpg" alt="Space Background" class="w-full h-full object-cover opacity-60 mix-blend-screen">
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between min-h-[400px] relative z-10">
            <!-- Left: Graphic / Cards illustration -->
            <div class="w-full md:w-2/3 p-8 md:p-12">
                <div class="space-y-6 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight drop-shadow-lg">
                        เปิดประสบการณ์การเรียนรู้<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 font-extrabold">ผ่านโดมท้องฟ้าจำลอง</span>
                    </h1>
                    <p class="text-gray-300 text-lg md:text-xl mt-4 max-w-2xl drop-shadow">
                        ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต แหล่งเรียนรู้ดาราศาสตร์และอวกาศสำหรับทุกคน
                    </p>
                    
                    <div class="pt-4">
                        <a href="#schedule" class="inline-block px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold rounded-full shadow-lg hover:shadow-cyan-500/30 transition transform hover:-translate-y-1">
                            ดูรอบฉายวันนี้
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right: Image Decoration -->
            <div class="w-full md:w-1/3 flex justify-center md:justify-end p-8 hidden md:flex">
                <div class="relative w-64 h-64 animate-pulse">
                    <!-- Just a decorative element, maybe the star.png if it looks good, or just rely on background -->
                    <img src="/images/star.png" alt="Star" class="w-full h-full object-contain opacity-80 drop-shadow-[0_0_15px_rgba(34,211,238,0.5)]">
                </div>
            </div>
        </div>
    </section>
HTML;

$content = preg_replace($searchPattern, $replacePattern, $content);
file_put_contents($file, $content);
echo "Updated Hero Section\n";
