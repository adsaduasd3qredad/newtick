<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function getNavigationGroup(): ?string
    {
        return 'ระบบจัดการ (System)';
    }

    public static function getNavigationLabel(): string
    {
        return 'บันทึกประวัติการทำงาน (Audit Logs)';
    }

    public static function getModelLabel(): string
    {
        return 'บันทึกประวัติการทำงาน';
    }

    public static function getPluralModelLabel(): string
    {
        return 'บันทึกประวัติการทำงาน (Audit Logs)';
    }

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false; // ห้ามสร้าง log ด้วยมือผ่านหน้าเว็บ ป้องกันการปลอมแปลง
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
