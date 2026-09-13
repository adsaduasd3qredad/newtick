<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$showtimes = App\Models\Showtime::all();
$date = \Carbon\Carbon::parse('2026-09-15');
$time = '11:00';

$cellShowtime = $showtimes->first(function($st) use ($date, $time) {
    return $st->show_date->toDateString() === $date->toDateString() && $st->show_time === $time.':00';
});

if ($cellShowtime) {
    echo "Found!\n";
} else {
    echo "Not found!\n";
}

