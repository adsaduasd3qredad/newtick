<?php

namespace App\Filament\Resources\Showtimes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; // 📌 เพิ่ม use ตัวนี้
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ShowtimeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('movie_id')
                    ->label('ภาพยนตร์')
                    ->relationship('movie', 'title_th') // ดึงชื่อฟิลด์ title_th จากตาราง movies มาแสดงผล
                    ->required()
                    ->preload(),
                DatePicker::make('show_date')
                    ->label('วันที่ฉาย')
                    ->required(),
                TimePicker::make('show_time')
                    ->label('เวลาฉาย')
                    ->seconds(false)
                    ->required(),
                TextInput::make('total_seats')
                    ->label('จำนวนที่นั่งทั้งหมด')
                    ->numeric()
                    ->required()
                    ->default(160),
                TextInput::make('available_seats')
                    ->label('ที่นั่งว่าง')
                    ->numeric()
                    ->required()
                    ->default(160),
            ]);
    }
}
