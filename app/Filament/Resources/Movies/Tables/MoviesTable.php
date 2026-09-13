<?php

namespace App\Filament\Resources\Movies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn; // 🖼️ 1. นำเข้า ImageColumn สำหรับแสดงรูปโปสเตอร์
use Filament\Tables\Columns\ToggleColumn; // ⚡ 2. นำเข้า ToggleColumn สำหรับสร้างสวิตช์เปิด-ปิด
use Filament\Tables\Table;

class MoviesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 🖼️ 3. แสดงรูปโปสเตอร์ภาพยนตร์จริง
                ImageColumn::make('poster_path')
                    ->label('โปสเตอร์')
                    ->square()
                    ->disk('public'),

                TextColumn::make('title_th')
                    ->label('ชื่อไทย')
                    ->label('ชื่อเรื่อง (TH)')
                    ->searchable(),

                TextColumn::make('title_en')
                    ->label('ชื่ออังกฤษ')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('เรทติ้ง')
                    ->badge(),

                // ⚡ 4. เพิ่มสวิตช์เปิด-ปิดสถานะฉายตรงหน้าตาราง
                ToggleColumn::make('is_active')
                    ->label('Is Active'),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('เริ่มฉาย'),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('สิ้นสุดฉาย'),

                TextColumn::make('duration_minutes')
                    ->label('ความยาว (นาที)')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('total_seats')
                    ->label('ที่นั่งรวม')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}