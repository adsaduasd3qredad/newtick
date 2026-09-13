<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null; // Disable polling to save resources

    protected function getStats(): array
    {
        return [
            Stat::make('รอตรวจสอบ/รับเงิน', Booking::whereIn('status', ['pending', 'awaiting_payment'])->count())
                ->description('รายการที่ต้องดำเนินการ')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('danger')
                ->url('/admin/bookings?tableFilters[status][value]=awaiting_payment'),

            Stat::make('รายได้วันนี้', number_format(
                Booking::whereIn('status', ['paid', 'redeemed'])
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount'),
                2
            ) . ' บาท')
                ->description('เฉพาะรายการที่ชำระแล้ว')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('รอบฉายวันนี้', Showtime::whereDate('show_date', Carbon::today())->count())
                ->description('รอบฉายของวันที่ ' . Carbon::today()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url('/admin/showtimes'),

            Stat::make('การจองทั้งหมด', Booking::count())
                ->description('รายการจองสะสมในระบบ')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}