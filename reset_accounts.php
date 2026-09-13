<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::firstOrCreate(
    ['email' => 'admin@admin.com'],
    ['name' => 'Admin User', 'password' => Hash::make('password'), 'role' => 'admin']
);
$admin->password = Hash::make('password');
$admin->role = 'admin';
$admin->save();

$staff = User::firstOrCreate(
    ['email' => 'staff@staff.com'],
    ['name' => 'Staff User', 'password' => Hash::make('password'), 'role' => 'staff']
);
$staff->password = Hash::make('password');
$staff->role = 'staff';
$staff->save();

// Update existing Jeng if needed
$jeng = User::where('email', '080@gmail.com')->first();
if ($jeng) {
    $jeng->password = Hash::make('password');
    $jeng->save();
}

echo "Accounts ready!\n";
