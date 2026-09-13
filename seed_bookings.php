<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Support\Str;
use Carbon\Carbon;

$showtimes = Showtime::whereDate('show_date', '>=', Carbon::today()->subDays(2))->take(10)->get();

foreach ($showtimes as $s) {
    Booking::create([
        'showtime_id' => $s->id,
        'booker_name' => 'Demo Customer ' . rand(1, 100),
        'booker_email' => 'customer'.rand(1,100).'@demo.com',
        'visitor_type' => 'individual',
        'quantity' => rand(1, 4),
        'total_amount' => 110 * rand(1, 4),
        'status' => 'paid',
        'payment_method' => rand(0,1) ? 'counter' : 'qr_code',
        'qr_ticket_ref' => (string) Str::uuid(),
        'qr_payment_ref' => (string) Str::uuid(),
        'created_at' => Carbon::now()->subHours(rand(1, 48)),
        'updated_at' => Carbon::now()
    ]);
}

echo "Seeded " . $showtimes->count() . " bookings.\n";
