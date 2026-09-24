<?php

namespace App\Filament\Pages;

use App\Services\SalesReportService;
use Filament\Actions\Action;
use Filament\Pages\Page;

class SaleReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function getNavigationLabel(): string
    {
        return 'Sale Report (รายงานยอดขาย)';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'รายงาน (Reports)';
    }

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
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('pos.reports.orders.pdf', $query)),
        ];
    }

    protected function getViewData(): array
    {
        [$period, $selectedDate] = app(SalesReportService::class)->normalizePeriod(
            request()->query('period'),
            request()->query('date', today()->toDateString()),
        );
        $report = app(SalesReportService::class)->make($period, $selectedDate);

        return [
            ...$report,
            'periods' => ['daily' => 'วันนี้', 'monthly' => 'รายเดือน', 'yearly' => 'รายปี'],
            'period' => $period,
            'selectedDate' => $selectedDate,
        ];
    }
}
