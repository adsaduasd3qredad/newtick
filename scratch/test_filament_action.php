<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\Booking;

try {
    $action = Action::make('change_status')
        ->label('เปลี่ยนสถานะ')
        ->icon('heroicon-m-arrows-right-left')
        ->color('primary')
        ->form([
            Select::make('new_status')
                ->label('สถานะใหม่')
                ->options([
                    'awaiting_payment' => 'รอชำระเงิน',
                    'paid' => 'ชำระแล้ว',
                ])
                ->required()
        ]);
    
    echo "Action created successfully!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
