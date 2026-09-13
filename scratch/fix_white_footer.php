<?php
// 1. Revert Hero Section in showtimes/index.blade.php
$fileIndex = 'resources/views/showtimes/index.blade.php';
$contentIndex = file_get_contents($fileIndex);

$searchHero = '/<!-- Hero Section \(Promotional Banner\) -->.*?<\/section>/s';
$replaceHero = <<<HTML
<!-- Hero Section (Promotional Banner) -->
    <section class="bg-[#f0f0f0] border-b border-gray-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between min-h-[400px]">
            <!-- Left: Graphic / Cards illustration -->
            <div class="w-full md:w-1/2 p-8 md:p-12 relative overflow-hidden flex justify-center items-center">
                <!-- Using a simple elegant text or graphic to match the reference -->
                <div class="space-y-4 text-center md:text-left z-10">
                    <h1 class="text-4xl md:text-5xl font-bold text-[#1c1c1c] leading-tight">
                        เปิดประสบการณ์การเรียนรู้<br>
                        <span class="text-blue-600 font-light">ผ่านโดมท้องฟ้าจำลอง</span>
                    </h1>
                    <p class="text-gray-600 text-lg mt-4">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต แหล่งเรียนรู้ดาราศาสตร์และอวกาศสำหรับทุกคน</p>
                </div>
                <!-- Subtle background decoration -->
                <div class="absolute -right-20 top-0 w-96 h-96 bg-gray-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
                <div class="absolute -left-20 bottom-0 w-72 h-72 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
            </div>
            
            <!-- Right: Image -->
            <div class="w-full md:w-1/2 flex justify-end">
                <!-- In a real scenario, this would be a high-quality promo image -->
            </div>
        </div>
    </section>
HTML;

$contentIndex = preg_replace($searchHero, $replaceHero, $contentIndex);
file_put_contents($fileIndex, $contentIndex);
echo "Reverted Hero Section\n";

// 2. Make the Footer White in layouts/client.blade.php
$fileClient = 'resources/views/layouts/client.blade.php';
$contentClient = file_get_contents($fileClient);

$searchFooter = '/<!-- Black Footer -->\s*<footer class="bg-gradient-to-r from-\[#d94a11\] to-\[#f47e20\] text-white\/90 py-10">/s';
$replaceFooter = <<<HTML
<!-- White Footer -->
    <footer class="bg-white border-t border-gray-200 text-gray-600 py-10">
HTML;
$contentClient = preg_replace($searchFooter, $replaceFooter, $contentClient);

// Change text colors in footer
$contentClient = str_replace('text-white font-medium', 'text-gray-900 font-medium', $contentClient);
$contentClient = str_replace('text-white/80', 'text-gray-500', $contentClient);
$contentClient = str_replace('hover:text-yellow-200', 'hover:text-cyan-500', $contentClient);
$contentClient = preg_replace('/<span class="text-white ml-2">/', '<span class="text-gray-600 ml-2">', $contentClient);

file_put_contents($fileClient, $contentClient);
echo "Updated Footer to White\n";
