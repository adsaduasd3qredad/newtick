<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('bookings', 'visitor_details')) {
    Schema::table('bookings', function (Blueprint $table) {
        $table->json('visitor_details')->nullable()->after('visitor_type');
    });
}
echo "OK\n";

