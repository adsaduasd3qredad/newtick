<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Booking;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('รหัสจอง')
                    ->formatStateUsing(fn ($state) => '#' . $state)
                    ->width('80px')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('showtime.movie.title_th')
                    ->label('ภาพยนตร์')
                    ->description(fn (Booking $record) => $record->showtime ? \Carbon\Carbon::parse($record->showtime->show_date)->format('d/m/Y') . ' รอบ ' . \Carbon\Carbon::parse($record->showtime->show_time)->format('H:i') . ' น.' : '-')
                    ->limit(28)
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('booker_name')
                    ->label('ผู้จอง')
                    ->description(fn (Booking $record) => $record->booker_phone)
                    ->limit(20)
                    ->wrap()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('visitor_type')
                    ->label('ประเภท')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'individual' => 'บุคคลทั่วไป',
                        'school' => 'โรงเรียน',
                        'government' => 'หน่วยงานรัฐ',
                        'company' => 'บริษัท',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'individual',
                        'warning' => 'school',
                        'info' => 'government',
                        'success' => 'company',
                    ]),

                TextColumn::make('quantity')
                    ->label('ที่นั่ง')
                    ->formatStateUsing(fn (Booking $record) => $record->quantity . ' ที่ ' . (!empty($record->seats) ? '(' . implode(',', $record->seats) . ')' : ''))
                    ->wrap()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'รอชำระ',
                        'awaiting_payment' => 'รอชำระเงิน',
                        'paid' => 'ชำระแล้ว',
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

                TextColumn::make('payment_method')
                    ->label('วิธีจ่าย')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'qr_code' => 'PromptPay QR',
                        'counter' => 'เคาน์เตอร์ POS',
                        default => $state ?? 'QR',
                    }),

                TextColumn::make('payment.slip_path')
                    ->label('หลักฐาน')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'มีสลิป' : 'ไม่มีสลิป')
                    ->badge()
                    ->color(fn (?string $state): string => filled($state) ? 'success' : 'gray')
                    ->tooltip(fn (?string $state): string => filled($state) ? 'เปิดดูสลิปได้จากเมนูการทำงาน' : 'ลูกค้ายังไม่ได้แนบสลิป'),

                TextColumn::make('checked_in_at')
                    ->label('เวลาตรวจตั๋ว')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('กรองตามสถานะ')
                    ->options([
                        'pending' => 'รอชำระ',
                        'paid' => 'ชำระแล้ว',
                        'redeemed' => 'ตรวจตั๋วแล้ว',
                        'expired' => 'หมดอายุ',
                    ]),

                SelectFilter::make('visitor_type')
                    ->label('กรองตามประเภท')
                    ->options([
                        'individual' => 'บุคคลทั่วไป',
                        'school' => 'โรงเรียน',
                        'government' => 'หน่วยงานรัฐ',
                        'company' => 'บริษัท',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('วิธีชำระ')
                    ->options([
                        'qr_code' => 'PromptPay QR',
                        'counter' => 'เคาน์เตอร์',
                    ]),
            ])
            ->recordActions([
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
                        ->visible(fn (Booking $record) => in_array($record->status, ['pending', 'awaiting_payment']))
                        ->requiresConfirmation()
                        ->action(function (Booking $record) {
                            $record->update(['status' => 'paid']);
                            $record->payment?->update(['paid_at' => now()]);
                        }),

                    EditAction::make()
                        ->label('แก้ไขรายการ'),
                ])->label('จัดการ')->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
