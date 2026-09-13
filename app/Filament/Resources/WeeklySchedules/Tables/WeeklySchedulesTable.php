<?php

namespace App\Filament\Resources\WeeklySchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeeklySchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week', 'asc')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('วันในสัปดาห์')
                    ->badge()
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
                    ->colors([
                        'warning' => 1,
                        'danger' => fn ($state) => in_array($state, [2, 7]),
                        'success' => 3,
                        'primary' => fn ($state) => in_array($state, [4, 5]),
                        'info' => 6,
                    ])
                    ->sortable(),

                TextColumn::make('show_time')
                    ->label('เวลาฉาย')
                    ->time('H:i น.')
                    ->sortable(),

                TextColumn::make('movie.title_th')
                    ->label('ภาพยนตร์')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_seats')
                    ->label('จำนวนที่นั่ง')
                    ->formatStateUsing(fn ($state) => $state . ' ที่นั่ง')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('กรองตามวัน')
                    ->options([
                        1 => 'วันจันทร์',
                        2 => 'วันอังคาร',
                        3 => 'วันพุธ',
                        4 => 'วันพฤหัสบดี',
                        5 => 'วันศุกร์',
                        6 => 'วันเสาร์',
                        7 => 'วันอาทิตย์',
                    ]),

                SelectFilter::make('movie_id')
                    ->label('กรองตามภาพยนตร์')
                    ->relationship('movie', 'title_th'),
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