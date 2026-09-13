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
            <!-- Dark overlay for readability -->
            <div class="absolute inset-0 bg-black/60"></div>
            <!-- Bottom gradient -->
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col items-center justify-center min-h-[450px] md:min-h-[500px] relative z-10 px-4 text-center pb-12">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-[#00a8ff] drop-shadow-[0_0_15px_rgba(0,168,255,0.5)] mb-6 tracking-wide">
                ท้องฟ้าจำลองรังสิต
            </h1>
            
            <p class="text-white text-lg md:text-xl lg:text-2xl font-light leading-relaxed max-w-3xl drop-shadow-md mb-2">
                เรียนรู้ดาราศาสตร์ผ่านการรับชมภาพยนตร์เต็มโดม
            </p>
            
            <p class="text-white text-lg md:text-xl lg:text-2xl font-light leading-relaxed max-w-3xl drop-shadow-md mb-8">
                ความคมชัดระดับ 4K และฟังบรรยายจากนักวิชาการศึกษา
            </p>
            
            <div class="pt-2">
                <a href="#schedule" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-blue-500 to-cyan-400 hover:from-blue-600 hover:to-cyan-500 text-white font-medium text-lg rounded-md shadow-[0_0_20px_rgba(6,182,212,0.6)] hover:shadow-[0_0_25px_rgba(6,182,212,0.8)] transition-all transform hover:-translate-y-1 border border-cyan-300/50">
                    ตารางรอบการแสดงท้องฟ้าจำลอง
                </a>
            </div>
        </div>
    </section>
HTML;

$contentIndex = preg_replace($searchHero, $replaceHero, $contentIndex);
file_put_contents($fileIndex, $contentIndex);
echo "Updated Hero Section with new text and solar.jpg\n";

