<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$panel = filament()->getPanel('admin');
$methods = get_class_methods($panel);
foreach ($methods as $method) {
    if (stripos($method, 'logout') !== false) {
        echo $method . "\n";
    }
}

