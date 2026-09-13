<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

// Replace the Navigation Links
$navSearch = <<<HTML
            <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-sm uppercase tracking-wider font-medium">
                <a href="/" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Home</a>
                
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">About Us</a>
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Contact Us</a>
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Cinema Location</a>
            </nav>
HTML;

$navReplace = <<<HTML
            <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-sm uppercase tracking-wider font-medium">
                <a href="/" class="hover:text-gray-300 transition border-b border-gray-800 pb-2" onclick="closeMenu()">Home</a>
                <a href="/#about" class="hover:text-gray-300 transition border-b border-gray-800 pb-2" onclick="closeMenu()">About Us</a>
                <a href="/#contact" class="hover:text-gray-300 transition border-b border-gray-800 pb-2" onclick="closeMenu()">Contact Us</a>
                <a href="/#contact" class="hover:text-gray-300 transition border-b border-gray-800 pb-2" onclick="closeMenu()">Cinema Location</a>
            </nav>
HTML;

$content = str_replace($navSearch, $navReplace, $content);

// Replace the simple footer with the complex footer
$footerSearch = <<<HTML
    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-500 uppercase tracking-widest">
            <p>&copy; {{ date('Y') }} Science Centre for Education. All rights reserved.</p>
        </div>
    </footer>
HTML;

$footerReplace = <<<HTML
    <!-- Contact & Footer Section -->
    <div id="contact" class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-16 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left flex-1">
                <h2 class="text-3xl text-cyan-500 mb-6" style="font-family: 'Prompt', sans-serif;">ติดต่อเรา</h2>
                <p class="text-gray-800 font-medium mb-1">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                <p class="text-gray-600 text-sm mb-1">ที่อยู่ 5 หมู่ 2 ต.รังสิต อ.ธัญบุรี จ.ปทุมธานี 12110</p>
                <p class="text-gray-600 text-sm mb-6">โทร 02 577 5456 - 9 ต่อ 304</p>
                
                <div class="flex items-center justify-center md:justify-start gap-2 mb-6">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 008.44-9.9c0-5.53-4.5-10.02-10-10.02z"></path></svg>
                    <span class="text-sm font-medium text-gray-700">ท้องฟ้าจำลองรังสิต</span>
                </div>
                
                <a href="https://maps.app.goo.gl/wY2DDEt9k1B33G6v8" target="_blank" class="inline-block bg-cyan-500 hover:bg-cyan-600 text-white font-medium py-2 px-12 rounded shadow transition">
                    แผนที่การเดินทาง
                </a>
            </div>
            
            <div class="flex-1 w-full max-w-lg rounded-xl overflow-hidden shadow-sm border border-gray-200 h-64 bg-gray-100 flex items-center justify-center">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1m2!1s0x30e20606b0d91243%3A0xf64f3319be1e4347!2z4Lio4Li54LiZ4Lii4LmM4Lin4Li04LiX4Lii4Liy4Lio4Liy4Liq4LiV4Lij4LmM4LmA4Lie4Li34LmI4Lit4LiB4Liy4Lij4Lio4Li24LiB4Lip4Liy4Lij4Lix4LiH4Liq4Li04LiV!5e0!3m2!1sth!2sth!4v1700000000000!5m2!1sth!2sth" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <!-- Black Footer -->
    <footer class="bg-[#141414] text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Footer Logo & Copyright -->
                <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10 rounded">
                    <div>
                        <p class="text-white font-medium text-sm">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                        <p class="text-[10px] uppercase tracking-wider">RANGSIT SCIENCE CENTRE FOR EDUCATION</p>
                        <p class="text-[11px] mt-2">Copyright &copy; 2021 ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved</p>
                    </div>
                </div>
                
                <!-- Footer Links -->
                <div class="text-[12px] flex flex-wrap justify-center gap-4 text-gray-400">
                    <a href="/" class="hover:text-white transition">หน้าแรก</a>
                    <span>|</span>
                    <a href="/#movies" class="hover:text-white transition">โปรแกรมภาพยนตร์</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition">โปรโมชั่น</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition">ข่าวสารและกิจกรรม</a>
                    <span class="text-white ml-2">โทร 02 577 5456 - 9 ต่อ 304</span>
                </div>
            </div>
        </div>
    </footer>
HTML;

$content = str_replace($footerSearch, $footerReplace, $content);
file_put_contents($file, $content);
echo "Updated client.blade.php with footer\n";

