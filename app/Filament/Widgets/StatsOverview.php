<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Showtime;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null; // Disable polling to save resources

    protected function getStats(): array
    {
        return [
            Stat::make('รายการรอชำระ / ตรวจสอบ', Booking::whereIn('status', ['pending', 'awaiting_payment'])->count())
                ->description('คลิกเพื่อเปิดรายการที่ต้องดำเนินการ')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning')
                ->url('/admin/bookings?order_status=pending'),

            Stat::make('ยอดรับเงินจริงวันนี้', number_format(
                Payment::whereNotNull('paid_at')
                    ->whereDate('paid_at', Carbon::today())
                    ->sum(\Illuminate\Support\Facades\DB::raw('ticket_amount + transaction_fee')),
                2
            ) . ' บาท')
                ->description('รวมค่าธรรมเนียมจากรายการที่ชำระแล้ว')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('รอบฉายวันนี้', Showtime::whereDate('show_date', Carbon::today())->count())
                ->description('รอบฉายของวันที่ ' . Carbon::today()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url('/admin/showtimes'),

            Stat::make('ที่นั่งรับชำระวันนี้', Booking::whereIn('status', ['paid', 'redeemed'])
                ->whereHas('payment', fn ($query) => $query->whereDate('paid_at', Carbon::today()))
                ->sum('quantity'))
                ->description('จาก ' . Booking::whereIn('status', ['paid', 'redeemed'])
                    ->whereHas('payment', fn ($query) => $query->whereDate('paid_at', Carbon::today()))
                    ->count() . ' ออเดอร์ที่รับชำระแล้ว')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }
}
