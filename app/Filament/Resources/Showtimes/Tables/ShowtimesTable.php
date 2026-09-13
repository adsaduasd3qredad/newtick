<?php

namespace App\Filament\Resources\Showtimes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Showtime;

class ShowtimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movie.title_th')
                    ->label('ภาพยนตร์')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('show_date')
                    ->label('วันที่ฉาย')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('show_time')
                    ->label('เวลาฉาย')
                    ->time('H:i น.')
                    ->sortable(),
                TextColumn::make('total_seats')
                    ->label('ที่นั่งทั้งหมด')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_seats')
                    ->label('ที่นั่งว่าง')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state . ' ที่')
                    ->colors([
                        'danger' => fn ($state) => $state <= 0,
                        'warning' => fn ($state) => $state > 0 && $state <= 20,
                        'success' => fn ($state) => $state > 20,
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('show_date', 'asc')
            ->filters([
                SelectFilter::make('movie_id')
                    ->label('กรองตามภาพยนตร์')
                    ->relationship('movie', 'title_th'),

                Filter::make('today')
                    ->label('เฉพาะรอบวันนี้')
                    ->query(fn (Builder $query): Builder => $query->whereDate('show_date', today())),

                Filter::make('future')
                    ->label('รอบตั้งแต่วันนี้เป็นต้นไป')
                    ->query(fn (Builder $query): Builder => $query->whereDate('show_date', '>=', today())),
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

