<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\AuditLog;
use Filament\Actions\Action;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('วัน-เวลาที่ทำรายการ')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->width('160px'),

                TextColumn::make('user_name')
                    ->label('ผู้ดำเนินการ')
                    ->description(fn (AuditLog $record): string => 'สิทธิ์: ' . strtoupper($record->user_role ?: 'GUEST'))
                    ->searchable()
                    ->sortable()
                    ->width('180px'),

                TextColumn::make('action')
                    ->label('ประเภทกิจกรรม')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pos_quick_sell' => 'POS ขายตั๋วด่วน',
                        'pos_collect_payment' => 'POS รับชำระเงิน',
                        'pos_checkin_redeem' => 'สแกนตรวจตั๋ว',
                        'admin_confirm_payment' => 'แอดมินยืนยันสลิป',
                        'admin_change_status' => 'แอดมินเปลี่ยนสถานะ',
                        'admin_cancel_booking' => 'แอดมินยกเลิกตั๋ว',
                        default => $state,
                    })
                    ->colors([
                        'success' => fn ($state) => in_array($state, ['pos_quick_sell', 'pos_collect_payment', 'admin_confirm_payment']),
                        'info' => 'pos_checkin_redeem',
                        'warning' => 'admin_change_status',
                        'danger' => 'admin_cancel_booking',
                    ])
                    ->width('160px'),

                TextColumn::make('order_reference')
                    ->label('ออเดอร์')
                    ->searchable()
                    ->placeholder('-')
                    ->width('90px'),

                TextColumn::make('amount')
                    ->label('ยอดเงิน')
                    ->money('THB')
                    ->sortable()
                    ->placeholder('-')
                    ->width('110px'),

                TextColumn::make('payment_method')
                    ->label('ช่องทางชำระ')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'counter' => 'เงินสดเคาน์เตอร์',
                        'qr_code' => 'PromptPay / QR',
                        default => $state ?: '-',
                    })
                    ->colors([
                        'gray' => fn ($state) => $state === 'counter',
                        'info' => fn ($state) => $state === 'qr_code',
                    ])
                    ->width('130px'),

                TextColumn::make('description')
                    ->label('รายละเอียดการกระทำ (Description)')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('กรองตามกิจกรรม')
                    ->options([
                        'pos_quick_sell' => 'POS ขายตั๋วด่วน (Walk-in)',
                        'pos_collect_payment' => 'POS รับชำระเงิน',
                        'pos_checkin_redeem' => 'สแกนตรวจบัตรเข้าชม',
                        'admin_confirm_payment' => 'แอดมินยืนยันรับเงินโอน',
                        'admin_change_status' => 'แอดมินเปลี่ยนสถานะ',
                        'admin_cancel_booking' => 'แอดมินยกเลิกการจอง',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('กรองตามช่องทางชำระ')
                    ->options([
                        'counter' => 'เงินสดหน้าเคาน์เตอร์',
                        'qr_code' => 'PromptPay / QR Code',
                    ]),
            ])
            ->recordActions([
                Action::make('view_details')
                    ->label('ดูข้อมูลเชิงลึก')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalHeading(fn (AuditLog $record): string => 'รายละเอียด Log #' . $record->id)
                    ->modalDescription(fn (AuditLog $record): string => $record->description)
                    ->form([
                        \Filament\Forms\Components\Textarea::make('properties_json')
                            ->label('ข้อมูลประกอบเพิ่มเติม (Metadata JSON)')
                            ->default(fn (AuditLog $record): string => json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: 'ไม่มีข้อมูลเพิ่มเติม')
                            ->rows(6)
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address ของผู้ทำรายการ')
                            ->default(fn (AuditLog $record): string => $record->ip_address ?: '-')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('user_agent')
                            ->label('เบราว์เซอร์ / อุปกรณ์')
                            ->default(fn (AuditLog $record): string => $record->user_agent ?: '-')
                            ->disabled(),
                    ])
                    ->modalSubmitAction(false),
            ]);
    }
}
