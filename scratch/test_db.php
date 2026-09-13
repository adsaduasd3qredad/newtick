<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$showtimes = App\Models\Showtime::all();
echo "Total showtimes: " . $showtimes->count() . "\n";
foreach($showtimes as $st) {
    echo $st->show_date . " " . $st->show_time . "\n";
}

