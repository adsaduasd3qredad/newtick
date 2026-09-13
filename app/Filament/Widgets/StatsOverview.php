<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\Showtime;

class StatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null; // Disable polling to save resources

    protected function getStats(): array
    {
        return [
            Stat::make('การจองทั้งหมด', Booking::count())
                ->description('รายการจองตั๋วในระบบ')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('ภาพยนตร์ในระบบ', Movie::count())
                ->description('จำนวนเรื่องทั้งหมด')
                ->descriptionIcon('heroicon-m-film')
                ->color('primary'),

            Stat::make('รอบฉายทั้งหมด', Showtime::count())
                ->description('รอบฉายที่เปิดให้บริการ')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}