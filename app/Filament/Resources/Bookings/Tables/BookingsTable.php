<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(function (Builder $query): Builder {
                $status = request()->input('order_status') ?: request()->input('tableFilters.status.value');

                if ($status === 'pending') {
                    return $query->whereIn('status', ['pending', 'awaiting_payment']);
                }

                return filled($status) ? $query->where('status', $status) : $query;
            })
            ->columns([
                TextColumn::make('id')
                    ->label('Order')
                    ->formatStateUsing(fn ($state) => '#' . str_pad((string) $state, 2, '0', STR_PAD_LEFT))
                    ->url(fn (Booking $record): string => route('filament.admin.resources.bookings.edit', ['record' => $record]))
                    ->color('primary')
                    ->description(fn (Booking $record): string => $record->payment_method === 'counter'
                        ? 'POS'
                        : ($record->booker_name ?: 'ออนไลน์'))
                    ->width('80px')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('วันที่สั่งซื้อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn (string $state, Booking $record): string => match ($state) {
                        'pending' => 'รอเลือก/ชำระ',
                        'awaiting_payment' => $record->payment_method === 'counter' ? 'รอชำระหน้าเคาน์เตอร์' : 'รอตรวจสอบ PromptPay',
                        'paid' => 'ชำระแล้ว / รอตรวจตั๋ว',
                        'redeemed' => 'ตรวจตั๋วแล้ว',
                        'expired' => 'หมดอายุ',
                        'cancelled' => 'ยกเลิก',
                        default => $state,
                    })
                    ->colors([
                        'warning' => fn ($state) => in_array($state, ['pending', 'awaiting_payment']),
                        'success' => 'paid',
                        'info' => 'redeemed',
                        'danger' => fn ($state) => in_array($state, ['expired', 'cancelled']),
                    ]),

                TextColumn::make('amount_paid')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('กรองตามสถานะ')
                    ->options([
                        'pending' => 'รอชำระ',
                        'awaiting_payment' => 'รอชำระ / ตรวจสอบ',
                        'paid' => 'ชำระแล้ว',
                        'redeemed' => 'ตรวจตั๋วแล้ว',
                        'expired' => 'หมดอายุ',
                        'cancelled' => 'ยกเลิก',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => ($data['value'] ?? null) === 'pending'
                        ? $query->whereIn('status', ['pending', 'awaiting_payment'])
                        : (filled($data['value'] ?? null)
                            ? $query->where('status', $data['value'])
                            : $query)),

                SelectFilter::make('created_month')
                    ->label('เดือนที่สั่งซื้อ')
                    ->options(collect(range(1, 12))->mapWithKeys(fn (int $month) => [
                        str_pad((string) $month, 2, '0', STR_PAD_LEFT) => Carbon::create()->month($month)->translatedFormat('F'),
                    ])->all())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereMonth('created_at', (int) $data['value'])
                        : $query),
            ])
            ->headerActions([
                Action::make('all_orders')
                    ->label('ทั้งหมด')
                    ->color('gray')
                    ->url('/admin/bookings'),
                Action::make('pending_orders')
                    ->label('รอชำระ')
                    ->color('warning')
                    ->url('/admin/bookings?order_status=pending'),
                Action::make('paid_orders')
                    ->label('ชำระแล้ว')
                    ->color('success')
                    ->url('/admin/bookings?order_status=paid'),
                Action::make('redeemed_orders')
                    ->label('ตรวจตั๋วแล้ว')
                    ->color('info')
                    ->url('/admin/bookings?order_status=redeemed'),
                Action::make('cancelled_orders')
                    ->label('ยกเลิก')
                    ->color('danger')
                    ->url('/admin/bookings?order_status=cancelled'),
            ])
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
                ActionGroup::make([
                    Action::make('view_ticket')
                        ->label('ดูตั๋ว')
                        ->icon('heroicon-m-ticket')
                        ->color('info')
                        ->url(fn (Booking $record): string => route('bookings.confirmed', $record->qr_ticket_ref))
                        ->openUrlInNewTab(),

                    Action::make('view_slip')
                        ->label('ดูสลิป')
                        ->icon('heroicon-m-photo')
                        ->color('warning')
                        ->visible(fn (Booking $record): bool => filled($record->payment?->slip_path))
                        ->url(fn (Booking $record): string => asset('storage/' . $record->payment->slip_path))
                        ->openUrlInNewTab(),

                    Action::make('mark_paid')
                        ->label('ยืนยันรับเงิน')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'awaiting_payment'], true))
                        ->requiresConfirmation()
                        ->action(function (Booking $record) {
                            DB::transaction(function () use ($record) {
                                $booking = Booking::whereKey($record->id)->lockForUpdate()->firstOrFail();
                                if ($booking->status !== 'awaiting_payment' || $booking->payment_method !== 'qr_code') {
                                    return;
                                }

                                $fee = (int) config('ticketing.qr_payment_fee');
                                $paidAt = now();
                                $booking->update([
                                    'status' => 'paid',
                                    'amount_paid' => (float) $booking->total_amount + $fee,
                                ]);
                                $booking->payment()->updateOrCreate(
                                    ['booking_id' => $booking->id],
                                    [
                                        'ticket_amount' => $booking->total_amount,
                                        'transaction_fee' => $fee,
                                        'method' => 'qr_code',
                                        'paid_at' => $paidAt,
                                    ],
                                );
                            });
                        }),

                    Action::make('cancel_booking')
                        ->label('ยกเลิกการจอง')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'awaiting_payment'], true))
                        ->requiresConfirmation()
                        ->action(function (Booking $record) {
                            DB::transaction(function () use ($record) {
                                $booking = Booking::whereKey($record->id)->lockForUpdate()->firstOrFail();
                                if (! in_array($booking->status, ['pending', 'awaiting_payment'], true)) {
                                    return;
                                }

                                $showtime = $booking->showtime()->lockForUpdate()->first();
                                $booking->update(['status' => 'cancelled']);
                                $showtime?->increment('available_seats', $booking->quantity);
                            });
                        }),

                    EditAction::make()
                        ->label('แก้ไขรายการ'),
                ])->label('จัดการ')->icon('heroicon-m-ellipsis-vertical'),
            ]);
    }
}
