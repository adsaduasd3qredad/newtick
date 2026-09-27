<?php
$file = 'app/Filament/Resources/Bookings/Tables/BookingsTable.php';
$content = file_get_contents($file);

// Check if imports exist
if (strpos($content, 'use Filament\Forms\Components\Select;') === false) {
    $content = str_replace(
        "use Filament\Tables\Table;",
        "use Filament\Tables\Table;\nuse Filament\Forms\Components\Select;\nuse Filament\Notifications\Notification;",
        $content
    );
}

// Add the change_status action right at the start of recordActions
$search = '->recordActions([';
$actionCode = <<<'PHP'
->recordActions([
                Action::make('change_status')
                    ->label('เปลี่ยนสถานะ')
                    ->icon('heroicon-m-arrows-right-left')
                    ->color('warning')
                    ->button()
                    ->modalHeading('เปลี่ยนสถานะการจอง')
                    ->modalDescription(fn (Booking $record): string => 'ออเดอร์ #' . str_pad((string) $record->id, 2, '0', STR_PAD_LEFT) . ' - ' . ($record->booker_name ?: 'ไม่ระบุชื่อ'))
                    ->modalSubmitActionLabel('บันทึกการเปลี่ยนสถานะ')
                    ->form([
                        Select::make('new_status')
                            ->label('เลือกสถานะใหม่')
                            ->options([
                                'awaiting_payment' => 'รอชำระเงิน (Awaiting Payment)',
                                'paid' => 'ชำระแล้ว / รอตรวจตั๋ว (Paid)',
                                'redeemed' => 'ตรวจตั๋วแล้ว (Redeemed)',
                                'cancelled' => 'ยกเลิกการจอง (Cancelled)',
                                'expired' => 'หมดอายุ (Expired)',
                                'pending' => 'รอเลือก/ชำระ (Pending)',
                            ])
                            ->default(fn (Booking $record): string => $record->status)
                            ->required(),
                    ])
                    ->action(function (Booking $record, array $data): void {
                        $oldStatus = $record->status;
                        $newStatus = $data['new_status'];

                        if ($oldStatus === $newStatus) {
                            return;
                        }

                        DB::transaction(function () use ($record, $oldStatus, $newStatus) {
                            $booking = Booking::whereKey($record->id)->lockForUpdate()->firstOrFail();
                            $showtime = $booking->showtime()->lockForUpdate()->first();

                            $activeStatuses = ['pending', 'awaiting_payment', 'paid', 'redeemed'];
                            $inactiveStatuses = ['cancelled', 'expired'];

                            $wasActive = in_array($oldStatus, $activeStatuses, true);
                            $nowActive = in_array($newStatus, $activeStatuses, true);

                            if ($wasActive && ! $nowActive) {
                                $showtime?->increment('available_seats', $booking->quantity);
                            } elseif (! $wasActive && $nowActive) {
                                $showtime?->decrement('available_seats', $booking->quantity);
                            }

                            $updateData = ['status' => $newStatus];

                            if ($newStatus === 'paid') {
                                $fee = (int) config('ticketing.qr_payment_fee');
                                $actualFee = $booking->payment_method === 'qr_code' ? $fee : 0;
                                $updateData['amount_paid'] = $booking->amount_paid ?: ((float) $booking->total_amount + $actualFee);

                                $booking->payment()->updateOrCreate(
                                    ['booking_id' => $booking->id],
                                    [
                                        'ticket_amount' => $booking->total_amount,
                                        'transaction_fee' => $actualFee,
                                        'method' => $booking->payment_method ?: 'counter',
                                        'paid_at' => $booking->payment?->paid_at ?: now(),
                                    ]
                                );
                            } elseif ($newStatus === 'redeemed') {
                                $fee = (int) config('ticketing.qr_payment_fee');
                                $actualFee = $booking->payment_method === 'qr_code' ? $fee : 0;
                                $updateData['amount_paid'] = $booking->amount_paid ?: ((float) $booking->total_amount + $actualFee);
                                $updateData['checked_in_at'] = $booking->checked_in_at ?: now();

                                $booking->payment()->updateOrCreate(
                                    ['booking_id' => $booking->id],
                                    [
                                        'ticket_amount' => $booking->total_amount,
                                        'transaction_fee' => $actualFee,
                                        'method' => $booking->payment_method ?: 'counter',
                                        'paid_at' => $booking->payment?->paid_at ?: now(),
                                    ]
                                );
                            }

                            $booking->update($updateData);
                        });

                        Notification::make()
                            ->title('เปลี่ยนสถานะเป็น ' . match ($newStatus) {
                                'pending' => 'รอเลือก/ชำระ',
                                'awaiting_payment' => 'รอชำระเงิน',
                                'paid' => 'ชำระแล้ว / รอตรวจตั๋ว',
                                'redeemed' => 'ตรวจตั๋วแล้ว',
                                'cancelled' => 'ยกเลิก',
                                'expired' => 'หมดอายุ',
                                default => $newStatus,
                            } . ' เรียบร้อยแล้ว')
                            ->success()
                            ->send();
                    }),
PHP;

$content = str_replace($search, $actionCode, $content);

// Also update mark_paid visibility so counter payment can also be marked paid easily
$oldMarkPaid = "->visible(fn (Booking \$record) => \$record->status === 'awaiting_payment'\n                            && \$record->payment_method === 'qr_code'\n                            && filled(\$record->payment?->slip_path))";
$newMarkPaid = "->visible(fn (Booking \$record) => in_array(\$record->status, ['pending', 'awaiting_payment'], true))";
$content = str_replace($oldMarkPaid, $newMarkPaid, $content);

file_put_contents($file, $content);
echo "SUCCESS: Updated BookingsTable.php!\n";

// Syntax check
exec('php -l ' . escapeshellarg($file), $output, $returnCode);
echo implode("\n", $output) . "\n";
if ($returnCode !== 0) {
    exit(1);
}
