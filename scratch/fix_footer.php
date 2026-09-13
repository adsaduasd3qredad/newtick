<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

$searchPattern = '/<footer class="bg-\[#141414\] text-gray-400 py-10">/s';
$replacePattern = '<footer class="bg-gradient-to-r from-[#d94a11] to-[#f47e20] text-white/90 py-10">';

$content = preg_replace($searchPattern, $replacePattern, $content);

// Also change the links hover color
$content = str_replace('text-gray-400', 'text-white/80', $content);
$content = str_replace('hover:text-white', 'hover:text-yellow-200', $content);

file_put_contents($file, $content);
echo "Updated footer color\n";
