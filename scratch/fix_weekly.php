<?php
$file = 'app/Filament/Resources/WeeklySchedules/Pages/ListWeeklySchedules.php';
$content = file_get_contents($file);
$content = preg_replace("/^\xEF\xBB\xBF/", '', $content);
$content = str_replace("\$movie->status !== 'publish'", "!\$movie->is_active", $content);
file_put_contents($file, $content);
echo "Fixed $file\n";

