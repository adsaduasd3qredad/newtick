<?php
$file = 'app/Filament/Resources/Movies/Tables/MoviesTable.php';
$content = file_get_contents($file);

$searchPattern = '/ImageColumn::make\(\'poster_path\'\)\s*->label\(\'[^()]+\'\)\s*->square\(\)/s';
// We'll just replace it fully
$replacePattern = <<<PHP
ImageColumn::make('poster_path')
                    ->label('โปสเตอร์')
                    ->square()
                    ->disk('public')
PHP;

$content = preg_replace($searchPattern, $replacePattern, $content);
file_put_contents($file, $content);
echo "Added disk('public') to ImageColumn\n";

