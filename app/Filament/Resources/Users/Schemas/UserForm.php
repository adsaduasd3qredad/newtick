<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ชื่อ-นามสกุล')
                    ->required(),
                TextInput::make('email')
                    ->label('อีเมล')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label('รหัสผ่าน')
                    ->password()
                    ->rule(Password::min(8)->mixedCase()->numbers())
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                \Filament\Forms\Components\Select::make('role')
                    ->label('สิทธิ์การใช้งาน')
                    ->options([
                        'admin' => 'ผู้ดูแลระบบ (Admin)',
                        'staff' => 'พนักงาน (Staff)',
                        'customer' => 'ลูกค้า (Customer)',
                    ])
                    ->required()
                    ->default('staff'),
            ]);
    }
}
