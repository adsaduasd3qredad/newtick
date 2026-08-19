<?php

namespace App\Filament\Resources\Movies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload; 
use Filament\Forms\Components\Toggle; // 🟢 1. นำเข้าคอมเพนเนนต์ Toggle สำหรับสร้างสวิตช์
use Filament\Schemas\Schema;

class MovieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                FileUpload::make('poster_path')
                    ->label('โปสเตอร์ภาพยนตร์')
                    ->image()
                    ->directory('movies/posters')
                    ->columnSpanFull(),
                    

                TextInput::make('title_th')
                    ->required(),
                TextInput::make('title_en'),

                // 🟢 2. เพิ่มฟิลด์ Toggle สำหรับเปิด-ปิดการแสดงผลภาพยนตร์
                Toggle::make('is_active')
                    ->label('เปิดการแสดงผลภาพยนตร์')
                    ->default(true) // ตั้งค่าเริ่มต้นให้เปิดใช้งานเสมอเมื่อเพิ่มหนังใหม่
                    ->required(),

                Textarea::make('description')
                    ->columnSpanFull(),

                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(40),
            ]);
    }
}