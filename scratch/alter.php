<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE bookings MODIFY COLUMN visitor_type ENUM('individual', 'company', 'government', 'school') NOT NULL DEFAULT 'individual'");

echo "OK\n";
