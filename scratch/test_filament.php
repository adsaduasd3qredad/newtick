<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$panel = filament()->getPanel('admin');
echo method_exists($panel, 'logoutRedirectUrl') ? 'logoutRedirectUrl exists' : 'no logoutRedirectUrl';
echo "\n";
echo method_exists($panel, 'loginUrl') ? 'loginUrl exists' : 'no loginUrl';
echo "\n";

