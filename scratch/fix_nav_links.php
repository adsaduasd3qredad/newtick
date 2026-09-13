<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

$pattern = '/<!-- Navigation Links -->.*?<\/nav>/s';

$replacement = <<<HTML
            <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-sm uppercase tracking-wider font-medium">
                <a href="/" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3" onclick="closeMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Home
                </a>
                <a href="/#about" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3" onclick="closeMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    About Us
                </a>
                <a href="/#contact" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3" onclick="closeMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Contact Us
                </a>
                <a href="/#contact" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3" onclick="closeMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Cinema Location
                </a>
            </nav>
HTML;

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);
echo "Updated navigation links with onclick and hashes\n";

