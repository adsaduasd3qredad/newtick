<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Booking;

class SaleReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    public static function getNavigationLabel(): string { return 'Sale Report (รายงานยอดขาย)'; }
    public static function getNavigationGroup(): ?string { return 'รายงาน (Reports)'; }
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Sale Report (รายงานยอดขาย)';

    protected string $view = 'filament.pages.sale-report';

    protected function getViewData(): array
    {
        $todayRevenue = Booking::whereDate('created_at', today())->whereIn('status', ['paid', 'redeemed'])->sum('total_amount');
        $thisMonthRevenue = Booking::whereMonth('created_at', today()->month)->whereYear('created_at', today()->year)->whereIn('status', ['paid', 'redeemed'])->sum('total_amount');
        $totalRevenue = Booking::whereIn('status', ['paid', 'redeemed'])->sum('total_amount');

        $totalTicketsSold = Booking::whereIn('status', ['paid', 'redeemed'])->sum('quantity');

        // Revenue by visitor type
        $visitorStats = Booking::selectRaw('visitor_type, sum(total_amount) as revenue, sum(quantity) as tickets')
            ->whereIn('status', ['paid', 'redeemed'])
            ->groupBy('visitor_type')
            ->get();

        return [
            'todayRevenue' => $todayRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold,
            'visitorStats' => $visitorStats,
        ];
    }
}
