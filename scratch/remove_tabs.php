<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

$searchPattern = '/<!-- Tabs Navigation \(ONGOING \/ COMING SOON\) -->.*?<\/section>/s';
$content = preg_replace($searchPattern, '', $content);

file_put_contents($file, $content);
echo "Removed Tabs Navigation\n";

