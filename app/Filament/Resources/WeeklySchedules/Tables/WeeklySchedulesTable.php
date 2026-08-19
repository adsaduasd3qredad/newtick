<?php

namespace App\Filament\Resources\WeeklySchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WeeklySchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 📌 แปลงตัวเลขวัน (0-6) ให้เป็นชื่อวันภาษาไทย
                TextColumn::make('day_of_week')
                    ->label('วันในสัปดาห์')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        
                        1 => 'วันจันทร์',
                        2 => 'วันอังคาร',
                        3 => 'วันพุธ',
                        4 => 'วันพฤหัสบดี',
                        5 => 'วันศุกร์',
                        6 => 'วันเสาร์',
                        7 => 'วันอาทิตย์',
                        default => '-',
                    })
                    ->sortable(),

                TextColumn::make('show_time')
                    ->label('เวลาฉาย')
                    ->time()
                    ->sortable(),

                // 📌 แสดงชื่อภาพยนตร์ภาษาไทยแทน Movie ID
                TextColumn::make('movie.title_th')
                    ->label('ภาพยนตร์')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_seats')
                    ->label('จำนวนที่นั่ง')
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}