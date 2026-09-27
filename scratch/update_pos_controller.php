<?php
$file = 'app/Http/Controllers/PosController.php';
$content = file_get_contents($file);

// Replace default name logic in quickSell
$oldNameLogic = "\$bookerName = !empty(\$validated['booker_name']) ? trim(\$validated['booker_name']) : 'ลูกค้าหน้าร้าน (Walk-in)';";
$newNameLogic = "\$bookerName = !empty(\$validated['booker_name']) ? trim(\$validated['booker_name']) : \$this->getNextWalkinName();";

if (strpos($content, $oldNameLogic) !== false) {
    $content = str_replace($oldNameLogic, $newNameLogic, $content);
}

// Add getNextWalkinName method if not already present
if (strpos($content, 'function getNextWalkinName()') === false) {
    $method = <<<'PHP'

    private function getNextWalkinName(): string
    {
        $maxNum = Booking::where('booker_name', 'like', 'Walk-in %')
            ->pluck('booker_name')
            ->map(function ($name) {
                if (preg_match('/^Walk-in\s+(\d+)$/i', $name, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->max() ?? 0;

        return 'Walk-in ' . ($maxNum + 1);
    }
PHP;

    // Insert right before the last closing brace
    $pos = strrpos($content, '}');
    if ($pos !== false) {
        $content = substr($content, 0, $pos) . $method . "\n}\n";
    }
}

file_put_contents($file, $content);
echo "SUCCESS: Updated PosController.php\n";

// Syntax check
exec('php -l ' . escapeshellarg($file), $output, $returnCode);
echo implode("\n", $output) . "\n";
if ($returnCode !== 0) {
    exit(1);
}
