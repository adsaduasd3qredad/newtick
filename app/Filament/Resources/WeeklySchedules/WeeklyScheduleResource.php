<?php

namespace App\Filament\Resources\WeeklySchedules;

use App\Filament\Resources\WeeklySchedules\Pages\CreateWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\EditWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\ListWeeklySchedules;
use App\Filament\Resources\WeeklySchedules\Pages\ViewWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Schemas\WeeklyScheduleForm;
use App\Filament\Resources\WeeklySchedules\Schemas\WeeklyScheduleInfolist;
use App\Filament\Resources\WeeklySchedules\Tables\WeeklySchedulesTable;
use App\Models\WeeklySchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class WeeklyScheduleResource extends Resource
{
    protected static ?string $model = WeeklySchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WeeklyScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WeeklyScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeeklySchedulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWeeklySchedules::route('/'),
            'create' => CreateWeeklySchedule::route('/create'),
            'view' => ViewWeeklySchedule::route('/{record}'),
            'edit' => EditWeeklySchedule::route('/{record}/edit'),
        ];
    }
}
