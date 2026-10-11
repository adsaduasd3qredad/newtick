<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('user_name')
                    ->default(null),
                TextInput::make('user_role')
                    ->default(null),
                TextInput::make('action')
                    ->required(),
                TextInput::make('booking_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('order_reference')
                    ->default(null),
                TextInput::make('amount')
                    ->numeric()
                    ->default(null),
                TextInput::make('payment_method')
                    ->default(null),
                TextInput::make('description')
                    ->required(),
                Textarea::make('properties')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->default(null),
                Textarea::make('user_agent')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
