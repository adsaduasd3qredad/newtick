<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'รายรับรายวัน';

    protected ?string $description = 'เลือกเดือนและปีเพื่อดูยอดรับจริงจากรายการที่ชำระแล้ว รวมค่าธรรมเนียม';

    protected ?string $maxHeight = '320px';

    protected function getFilters(): ?array
    {
        $filters = [];
        $month = Carbon::today()->startOfMonth();

        for ($offset = 0; $offset < 60; $offset++) {
            $date = $month->copy()->subMonthsNoOverflow($offset);
            $filters[$date->format('Y-m')] = $date->locale('th')->translatedFormat('F Y');
        }

        return $filters;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $filter = (string) ($this->filter ?? Carbon::today()->format('Y-m'));
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $filter)) {
            $filter = Carbon::today()->format('Y-m');
        }

        $start = Carbon::createFromFormat('!Y-m', $filter)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $dailyTotals = Payment::query()
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('DATE(paid_at) as sale_date, SUM(ticket_amount + transaction_fee) as total')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->pluck('total', 'sale_date');

        $labels = [];
        $values = [];

        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $date = $day->toDateString();
            $labels[] = $day->format('d/m');
            $values[] = (float) ($dailyTotals[$date] ?? 0);
        }

        return [
            'datasets' => [[
                'label' => 'รายรับ (บาท)',
                'data' => $values,
                'fill' => 'start',
                'borderColor' => 'rgb(217, 119, 6)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.14)',
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
