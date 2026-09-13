<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

// Remove the Center Badge (Showtimes)
$searchBadge = '/<!-- Center Badge \(Showtimes\) -->.*?<\/div>/s';
$content = preg_replace($searchBadge, '', $content);

file_put_contents($file, $content);
echo "Removed Showtimes badge from client.blade.php\n";

