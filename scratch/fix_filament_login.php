<?php
$file = 'app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($file);

$content = str_replace("->login()\n", "", $content);

file_put_contents($file, $content);
echo "Removed ->login() from AdminPanelProvider\n";

