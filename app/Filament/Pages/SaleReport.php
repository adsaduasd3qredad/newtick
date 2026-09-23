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
            Action::make('export_excel')
                ->label('ส่งออก Excel')
                ->icon('heroicon-o-table-cells')
                ->url(route('pos.reports.orders.excel', $query)),
        ];
    }

    protected function getViewData(): array
    {
        $periods = ['daily' => 'วันนี้', 'monthly' => 'รายเดือน', 'yearly' => 'รายปี'];
        $period = request()->query('period', 'daily');
        $period = array_key_exists($period, $periods) ? $period : 'daily';
        $selectedDate = Carbon::parse(request()->query('date', today()->toDateString()));
        $sales = Booking::with(['showtime.movie', 'payment'])->whereIn('status', ['paid', 'redeemed']);
        $periodSales = $this->applyPeriod($sales, $period, $selectedDate);
        return [
            'sales' => (clone $periodSales)->latest()->get(),
            'periods' => $periods,
            'period' => $period,
            'selectedDate' => $selectedDate,
            'periodStart' => $period === 'daily' ? $selectedDate->copy()->startOfDay() : ($period === 'monthly' ? $selectedDate->copy()->startOfMonth() : $selectedDate->copy()->startOfYear()),
            'periodEnd' => $period === 'daily' ? $selectedDate->copy()->endOfDay() : ($period === 'monthly' ? $selectedDate->copy()->endOfMonth() : $selectedDate->copy()->endOfYear()),
        ];
    }

    private function applyPeriod(Builder $query, string $period, Carbon $date): Builder
    {
        return match ($period) {
            'daily' => $query->whereDate('created_at', $date->toDateString()),
            'monthly' => $query->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month),
            'yearly' => $query->whereYear('created_at', $date->year),
            default => $query->whereDate('created_at', $date->toDateString()),
        };
    }
}
