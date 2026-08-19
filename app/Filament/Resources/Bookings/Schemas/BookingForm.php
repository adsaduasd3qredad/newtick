<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('showtime_id')
                    ->required()
                    ->numeric(),
                TextInput::make('booker_name')
                    ->required(),
                TextInput::make('booker_email')
                    ->email()
                    ->required(),
                TextInput::make('booker_phone')
                    ->tel(),
                Select::make('visitor_type')
                    ->options(['individual' => 'Individual', 'company' => 'Company', 'government' => 'Government'])
                    ->default('individual')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('discount_code'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'awaiting_payment' => 'Awaiting payment',
            'paid' => 'Paid',
            'redeemed' => 'Redeemed',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                Select::make('payment_method')
                    ->options(['qr_code' => 'Qr code', 'counter' => 'Counter']),
                DateTimePicker::make('expires_at'),
                TextInput::make('qr_payment_ref'),
                TextInput::make('qr_ticket_ref'),
                DateTimePicker::make('checked_in_at'),
            ]);
    }
}
