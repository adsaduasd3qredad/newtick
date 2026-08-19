<?php

namespace App\Filament\Resources\WeeklySchedules\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WeeklyScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 📌 เปลี่ยนกลับมาเป็น Select เลือกวันเดี่ยว (ป้องกัน Error ข้อมูล Array)
                Select::make('day_of_week')
                    ->label('วันในสัปดาห์')
                    ->options([

                        1 => 'จันทร์',
                        2 => 'อังคาร',
                        3 => 'พุธ',
                        4 => 'พฤหัสบดี',
                        5 => 'ศุกร์',
                        6 => 'เสาร์',
                        7 => 'วันอาทิตย์',
                    ])
                    ->required()
                    ->columnSpanFull(),

                // ช่องเลือกเวลาฉายหลายรอบ (ยังคงความสะดวกไว้ตรงนี้)
                CheckboxList::make('show_times')
                    ->label('รอบเวลาฉาย (เลือกได้หลายรอบ)')
                    ->options([
                        '10:00:00' => '10:00 น.',
                        '11:00:00' => '11:00 น.',
                        '13:00:00' => '13:00 น.',
                        '14:00:00' => '14:00 น.',
                        '15:00:00' => '15:00 น.',
                    ])
                    ->columns(5)
                    ->required()
                    ->columnSpanFull(),

                Select::make('movie_id')
                    ->label('ภาพยนตร์')
                    ->relationship('movie', 'title_th')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('total_seats')
                    ->label('จำนวนที่นั่ง')
                    ->numeric()
                    ->default(160)
                    ->required(),
            ]);
    }
}
