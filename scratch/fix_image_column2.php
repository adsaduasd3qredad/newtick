<?php
$file = 'app/Filament/Resources/Movies/Tables/MoviesTable.php';
$content = file_get_contents($file);

$search = "->square(),";
$replace = "->square()\n                    ->disk('public'),";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Added disk('public') using str_replace\n";

