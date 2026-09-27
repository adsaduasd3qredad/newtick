<?php
$file = 'resources/views/pos/index.blade.php';
$content = file_get_contents($file);

$oldLine = '<div class="bg-cyan-500 h-1.5 rounded-full transition-all duration-300" style="<?php echo \'width: \' . $percentBooked . \'%\'; ?>"></div>';
$newLine = '<div class="bg-cyan-500 h-1.5 rounded-full transition-all duration-300" style="--percent: {{ $percentBooked }}%; width: var(--percent);"></div>';

if (strpos($content, $oldLine) !== false) {
    $content = str_replace($oldLine, $newLine, $content);
    file_put_contents($file, $content);
    echo "SUCCESS: Replaced line 132\n";
} else {
    echo "NOT FOUND\n";
}

exec('php -l ' . escapeshellarg($file), $output, $returnCode);
echo implode("\n", $output) . "\n";
