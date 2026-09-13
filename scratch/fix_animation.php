<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);
$content = str_replace('animate-spin-slow', 'animate-pulse', $content);
file_put_contents($file, $content);
echo "Fixed animation\n";
