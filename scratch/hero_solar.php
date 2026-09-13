<?php
$fileIndex = 'resources/views/showtimes/index.blade.php';
$contentIndex = file_get_contents($fileIndex);

$searchHero = '/<!-- Hero Section \(Promotional Banner\) -->.*?<\/section>/s';
$replaceHero = <<<HTML
<!-- Hero Section (Promotional Banner) -->
    <section class="relative bg-black overflow-hidden border-b border-gray-900">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/solar.jpg') }}" alt="Solar System" class="w-full h-full object-cover opacity-90">
            <!-- Overlay to ensure text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent"></div>
            <!-- Bottom gradient for smooth transition to the next section -->
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between min-h-[500px] relative z-10 pb-10">
            <!-- Left: Text -->
            <div class="w-full md:w-3/5 p-8 md:p-12">
                <div class="space-y-6 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight drop-shadow-2xl">
                        เปิดประสบการณ์การเรียนรู้<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 font-extrabold drop-shadow-md">
                            ผ่านโดมท้องฟ้าจำลอง
                        </span>
                    </h1>
                    <p class="text-gray-100 text-lg md:text-xl mt-4 max-w-xl drop-shadow-lg font-medium leading-relaxed">
                        ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต <br>
                        <span class="font-light text-gray-300">แหล่งเรียนรู้ดาราศาสตร์และอวกาศสำหรับทุกคน</span>
                    </p>
                    
                    <div class="pt-8">
                        <a href="#schedule" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold text-lg rounded-full shadow-[0_4px_14px_0_rgba(242,101,34,0.39)] hover:shadow-[0_6px_20px_rgba(242,101,34,0.23)] hover:shadow-orange-500/50 transition-all transform hover:-translate-y-1">
                            ดูรอบฉายวันนี้
                            <svg class="w-5 h-5 ml-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right: Empty space to let the planets show clearly -->
            <div class="w-full md:w-2/5 hidden md:block">
            </div>
        </div>
    </section>
HTML;

$contentIndex = preg_replace($searchHero, $replaceHero, $contentIndex);
file_put_contents($fileIndex, $contentIndex);
echo "Updated Hero Section with solar.jpg\n";

