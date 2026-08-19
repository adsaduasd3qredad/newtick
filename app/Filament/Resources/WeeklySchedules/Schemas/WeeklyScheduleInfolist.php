<?php

namespace App\Filament\Resources\WeeklySchedules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WeeklyScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 📌 แปลงเลขวัน (0-6) ให้เป็นชื่อวันภาษาไทย
                TextEntry::make('day_of_week')
                    ->label('วันในสัปดาห์')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        7 => 'วันอาทิตย์',
                        1 => 'วันจันทร์',
                        2 => 'วันอังคาร',
                        3 => 'วันพุธ',
                        4 => 'วันพฤหัสบดี',
                        5 => 'วันศุกร์',
                        6 => 'วันเสาร์',
                        default => '-',
                    }),

                // 📌 ดึงชื่อภาพยนตร์ภาษาไทยมาแสดงแทน Movie ID
                TextEntry::make('movie.title_th')
                    ->label('ภาพยนตร์'),

                TextEntry::make('show_time')
                    ->label('เวลาฉาย'),

                TextEntry::make('total_seats')
                    ->label('จำนวนที่นั่งรวม'),
            ]);
    }
}