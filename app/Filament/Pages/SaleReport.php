<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
        $query = request()->only(['period', 'date']);

        return [
            Action::make('export_pdf')
                ->label('ส่งออก PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('pos.reports.orders.pdf', $query))
                ->openUrlInNewTab(),
            Action::make('export_excel')
                ->label('ส่งออก Excel')
                ->icon('heroicon-o-table-cells')
                ->url(route('pos.reports.orders.excel', $query)),
        ];
    }

    protected function getViewData(): array
    {
        $periods = ['daily' => 'รายวัน', 'monthly' => 'รายเดือน', 'yearly' => 'รายปี'];
        $period = request()->query('period', 'daily');
        $period = array_key_exists($period, $periods) ? $period : 'daily';
        $selectedDate = Carbon::parse(request()->query('date', today()->toDateString()));
        $sales = Booking::with(['showtime.movie', 'payment'])->whereIn('status', ['paid', 'redeemed']);
        $periodSales = $this->applyPeriod($sales, $period, $selectedDate);
        $totalRevenue = (clone $periodSales)->sum(\DB::raw('COALESCE(amount_paid, total_amount)'));
        $totalFees = (clone $periodSales)->whereHas('payment')->get()->sum(fn (Booking $booking) => (float) ($booking->payment?->transaction_fee ?? 0));

        $totalTicketsSold = (clone $periodSales)->sum('quantity');

        // Revenue by visitor type
        $visitorStats = $this->applyPeriod(Booking::selectRaw('visitor_type, sum(COALESCE(amount_paid, total_amount)) as revenue, sum(quantity) as tickets')
            ->whereIn('status', ['paid', 'redeemed']), $period, $selectedDate)
            ->groupBy('visitor_type')
            ->get();

        return [
            'totalRevenue' => $totalRevenue,
            'totalTicketsSold' => $totalTicketsSold,
            'totalFees' => $totalFees,
            'visitorStats' => $visitorStats,
            'recentOrders' => (clone $periodSales)->latest()->limit(20)->get(),
            'periods' => $periods,
            'period' => $period,
            'selectedDate' => $selectedDate,
        ];
    }

    private function applyPeriod(Builder $query, string $period, Carbon $date): Builder
    {
        return match ($period) {
            'monthly' => $query->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month),
            'yearly' => $query->whereYear('created_at', $date->year),
            default => $query->whereDate('created_at', $date->toDateString()),
        };
    }
}
