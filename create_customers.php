<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::firstOrCreate(
    ['email' => 'customer1@demo.com'],
    ['name' => 'Demo Customer 1', 'password' => Hash::make('password'), 'role' => 'customer']
);

User::firstOrCreate(
    ['email' => 'customer2@demo.com'],
    ['name' => 'Demo Customer 2', 'password' => Hash::make('password'), 'role' => 'customer']
);

echo "Demo customers created.\n";
