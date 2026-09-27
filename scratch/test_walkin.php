<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

function getNextWalkinName(): string {
    $maxNum = Booking::where('booker_name', 'like', 'Walk-in %')
        ->get()
        ->map(function ($b) {
            if (preg_match('/^Walk-in\s+(\d+)$/i', $b->booker_name, $m)) {
                return (int) $m[1];
            }
            return 0;
        })
        ->max() ?? 0;

    return 'Walk-in ' . ($maxNum + 1);
}

echo "Next name: " . getNextWalkinName() . "\n";
