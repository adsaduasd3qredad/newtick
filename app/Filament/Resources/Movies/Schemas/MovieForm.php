<?php

namespace App\Filament\Resources\Movies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload; 
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MovieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                FileUpload::make('poster_path')
                    ->label('รูปโปสเตอร์ภาพยนตร์ (600 x 730 px)')
                    ->image()
                    ->disk('public')
                    ->directory('movies/posters')
                    ->columnSpanFull(),

                TextInput::make('title_th')
                    ->label('ชื่อเรื่องภาพยนตร์ (ภาษาไทย)')
                    ->required(),
                
                TextInput::make('title_en')
                    ->label('ชื่อเรื่องภาพยนตร์ (ภาษาอังกฤษ)'),

                Toggle::make('is_active')
                    ->label('เปิดใช้งาน (Active)')
                    ->default(true),

                \Filament\Forms\Components\DatePicker::make('start_date')
                    ->label('วันที่เริ่มฉาย (Start Date)')
                    ->nullable(),
                
                \Filament\Forms\Components\DatePicker::make('end_date')
                    ->label('วันสิ้นสุดการฉาย (End Date)')
                    ->nullable(),

                \Filament\Forms\Components\Select::make('rating')
                    ->label('เรทติ้ง (Rating)')
                    ->options([
                        'G' => 'G (ทั่วไป)',
                        'PG' => 'PG (ผู้ปกครองควรแนะนำ)',
                        '13+' => '13+ (สำหรับผู้มีอายุ 13 ปีขึ้นไป)',
                        '15+' => '15+ (สำหรับผู้มีอายุ 15 ปีขึ้นไป)',
                        '18+' => '18+ (สำหรับผู้มีอายุ 18 ปีขึ้นไป)',
                        '20-' => '20- (สำหรับผู้มีอายุไม่เกิน 20 ปี)',
                    ])
                    ->nullable(),

                TextInput::make('language')
                    ->label('ภาษา (เสียง/ซับไตเติ้ล)')
                    ->placeholder('เช่น TH/TH, EN/TH')
                    ->nullable(),

                TextInput::make('trailer_url')
                    ->label('ลิงก์ตัวอย่างภาพยนตร์ (YouTube URL)')
                    ->url()
                    ->nullable(),

                TextInput::make('total_seats')
                    ->label('จำนวนที่นั่งรวม (Total Seats)')
                    ->numeric()
                    ->default(160)
                    ->required(),

                TextInput::make('duration_minutes')
                    ->label('ความยาวภาพยนตร์ (นาที)')
                    ->required()
                    ->numeric()
                    ->default(40),

                \Filament\Forms\Components\RichEditor::make('description')
                    ->label('เรื่องย่อ (Short Description)')
                    ->columnSpanFull(),
                
                \Filament\Schemas\Components\Section::make('รอบฉายประจำสัปดาห์ (Weekly Schedules)')
                    ->description('กำหนดว่าภาพยนตร์เรื่องนี้จะฉายในวันและเวลาใดเป็นประจำทุกสัปดาห์')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('weeklySchedules')
                            ->relationship()
                            ->label('')
                            ->schema([
                                \Filament\Forms\Components\Select::make('day_of_week')
                                    ->label('วันในสัปดาห์')
                                    ->options([
                                        1 => 'จันทร์ (Monday)',
                                        2 => 'อังคาร (Tuesday)',
                                        3 => 'พุธ (Wednesday)',
                                        4 => 'พฤหัสบดี (Thursday)',
                                        5 => 'ศุกร์ (Friday)',
                                        6 => 'เสาร์ (Saturday)',
                                        7 => 'อาทิตย์ (Sunday)',
                                    ])
                                    ->required(),
                                \Filament\Forms\Components\Select::make('show_time')
                                    ->label('เวลาฉาย')
                                    ->options([
                                        '10:00:00' => '10:00 น.',
                                        '11:00:00' => '11:00 น.',
                                        '13:00:00' => '13:00 น.',
                                        '14:00:00' => '14:00 น.',
                                        '15:00:00' => '15:00 น.',
                                    ])
                                    ->required(),
                                TextInput::make('total_seats')
                                    ->label('จำนวนที่นั่ง')
                                    ->numeric()
                                    ->default(160)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('เพิ่มรอบฉายประจำสัปดาห์')
                    ])
                    ->collapsible(),
            ]);
    }
}