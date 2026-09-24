<?php

namespace App\Filament\Resources\Showtimes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select; // 📌 เพิ่ม use ตัวนี้
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use App\Models\Showtime;

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
                    ->rules(fn (Get $get, ?Showtime $record): array => [
                        Rule::unique('showtimes', 'movie_id')
                            ->where(fn (Builder $query) => $query
                                ->whereDate('show_date', $get('show_date'))
                                ->where('show_time', $get('show_time')))
                            ->ignore($record?->getKey()),
                    ])
                    ->validationMessages([
                        'unique' => 'ภาพยนตร์เรื่องนี้มีรอบฉายในวันและเวลานี้แล้ว',
                    ])
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
