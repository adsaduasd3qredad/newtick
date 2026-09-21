<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\Booking;

class SaleReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    public static function getNavigationLabel(): string { return 'Sale Report (รายงานยอดขาย)'; }
    public static function getNavigationGroup(): ?string { return 'รายงาน (Reports)'; }
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Sale Report (รายงานยอดขาย)';

    protected string $view = 'filament.pages.sale-report';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('ส่งออก PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('pos.reports.orders.pdf'))
                ->openUrlInNewTab(),
            Action::make('export_excel')
                ->label('ส่งออก Excel')
                ->icon('heroicon-o-table-cells')
                ->url(route('pos.reports.orders.excel')),
        ];
    }

    protected function getViewData(): array
    {
        $sales = Booking::with(['showtime.movie', 'payment'])->whereIn('status', ['paid', 'redeemed']);
        $todayRevenue = (clone $sales)->whereDate('created_at', today())->sum(\DB::raw('COALESCE(amount_paid, total_amount)'));
        $thisMonthRevenue = (clone $sales)->whereMonth('created_at', today()->month)->whereYear('created_at', today()->year)->sum(\DB::raw('COALESCE(amount_paid, total_amount)'));
        $totalRevenue = (clone $sales)->sum(\DB::raw('COALESCE(amount_paid, total_amount)'));
        $totalFees = (clone $sales)->whereHas('payment')->get()->sum(fn (Booking $booking) => (float) ($booking->payment?->transaction_fee ?? 0));

        $totalTicketsSold = (clone $sales)->sum('quantity');

        // Revenue by visitor type
        $visitorStats = Booking::selectRaw('visitor_type, sum(COALESCE(amount_paid, total_amount)) as revenue, sum(quantity) as tickets')
            ->whereIn('status', ['paid', 'redeemed'])
            ->groupBy('visitor_type')
            ->get();

        return [
            'todayRevenue' => $todayRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold,
            'totalFees' => $totalFees,
            'visitorStats' => $visitorStats,
            'recentOrders' => (clone $sales)->latest()->limit(20)->get(),
        ];
    }
}
