<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

$searchPattern = '/<div class="mt-6 flex flex-wrap items-center justify-end gap-6 text-xs text-gray-500 uppercase tracking-wide">.*?<\/div>/s';
$content = preg_replace($searchPattern, '', $content);
file_put_contents($file, $content);
echo "Removed legend\n";

