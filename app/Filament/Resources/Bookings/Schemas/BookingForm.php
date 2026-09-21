<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('showtime_id')
                    ->label('รอบการแสดง')
                    ->relationship('showtime', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => ($record->movie ? $record->movie->title_th : 'Showtime #' . $record->id) . ' (' . \Carbon\Carbon::parse($record->show_date)->format('d/m/Y') . ' ' . \Carbon\Carbon::parse($record->show_time)->format('H:i') . ' น.)')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('booker_name')
                    ->label('ชื่อผู้จอง')
                    ->required(),

                TextInput::make('booker_email')
                    ->label('อีเมลผู้จอง')
                    ->email()
                    ->required(),

                TextInput::make('booker_phone')
                    ->label('เบอร์โทรศัพท์')
                    ->tel(),

                Select::make('visitor_type')
                    ->label('ประเภทผู้เข้าชม')
                    ->options([
                        'individual' => 'บุคคลทั่วไป (Individual)',
                        'school' => 'โรงเรียน (School)',
                        'government' => 'หน่วยงานรัฐ (Government)',
                        'company' => 'บริษัท (Company)',
                    ])
                    ->default('individual')
                    ->required(),

                TextInput::make('quantity')
                    ->label('จำนวนที่นั่ง')
                    ->required()
                    ->numeric(),

                TagsInput::make('seats')
                    ->label('หมายเลขที่นั่ง (เช่น A1, A2)')
                    ->placeholder('พิมพ์ที่นั่งแล้วกด Enter'),

                TextInput::make('total_amount')
                    ->label('ยอดเงินรวม (บาท)')
                    ->prefix('฿')
                    ->required()
                    ->numeric(),

                TextInput::make('amount_paid')
                    ->label('ยอดเก็บจริง (บาท)')
                    ->prefix('฿')
                    ->numeric(),

                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('หมายเหตุการขาย')
                    ->rows(3)
                    ->maxLength(1000),

                TextInput::make('discount_code')
                    ->label('โค้ดส่วนลด'),

                Select::make('status')
                    ->label('สถานะการจอง')
                    ->options([
                        'pending' => 'รอชำระ (Pending)',
                        'awaiting_payment' => 'รอชำระเงิน (Awaiting Payment)',
                        'paid' => 'ชำระแล้ว (Paid)',
                        'redeemed' => 'ตรวจตั๋วแล้ว (Redeemed)',
                        'expired' => 'หมดอายุ (Expired)',
                        'cancelled' => 'ยกเลิก (Cancelled)',
                    ])
                    ->default('pending')
                    ->required(),

                Select::make('payment_method')
                    ->label('วิธีการชำระเงิน')
                    ->options([
                        'qr_code' => 'PromptPay QR Code',
                        'counter' => 'ชำระที่เคาน์เตอร์ POS',
                    ]),

                DateTimePicker::make('expires_at')
                    ->label('หมดอายุชำระเงินเมื่อ'),

                TextInput::make('qr_payment_ref')
                    ->label('รหัสอ้างอิงการชำระเงิน'),

                TextInput::make('qr_ticket_ref')
                    ->label('รหัส QR Ticket'),

                
            ]);
    }
}
