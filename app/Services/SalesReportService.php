<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesReportService
{
    public function make(string $period, Carbon $date): array
    {
        [$start, $end] = $this->bounds($period, $date);
        return $this->makeRange($start, $end, $this->periodLabel($period, $date));
    }

    public function makeRange(Carbon $start, Carbon $end, ?string $periodLabel = null): array
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->endOfDay();

        $sales = Booking::query()
            ->with(['showtime.movie', 'payment'])
            ->whereIn('status', ['paid', 'redeemed'])
            ->whereHas('payment', fn ($query) => $query
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$start, $end]))
            ->get()
            ->sortByDesc(fn (Booking $booking) => $booking->payment?->paid_at)
            ->values();

        $byPaymentMethod = Payment::query()
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->whereIn('bookings.status', ['paid', 'redeemed'])
            ->whereNotNull('payments.paid_at')
            ->whereBetween('payments.paid_at', [$start, $end])
            ->select('bookings.payment_method')
            ->selectRaw('COUNT(bookings.id) as orders')
            ->selectRaw('SUM(bookings.quantity) as visitors')
            ->selectRaw('SUM(payments.ticket_amount + payments.transaction_fee) as collected')
            ->selectRaw('SUM(payments.transaction_fee) as fees')
            ->groupBy('bookings.payment_method')
            ->orderBy('bookings.payment_method')
            ->get();

        $dailySales = Payment::query()
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->whereIn('bookings.status', ['paid', 'redeemed'])
            ->whereNotNull('payments.paid_at')
            ->whereBetween('payments.paid_at', [$start, $end])
            ->selectRaw('DATE(payments.paid_at) as date')
            ->selectRaw('COUNT(bookings.id) as orders')
            ->selectRaw('SUM(bookings.quantity) as visitors')
            ->selectRaw('SUM(payments.ticket_amount + payments.transaction_fee) as total')
            ->selectRaw('SUM(payments.transaction_fee) as fees')
            ->groupBy(DB::raw('DATE(payments.paid_at)'))
            ->orderBy('date')
            ->get();

        $totalFees = (float) $sales->sum(fn (Booking $sale) => (float) ($sale->payment?->transaction_fee ?? 0));

        return [
            'period' => 'range',
            'date' => $start->copy(),
            'periodStart' => $start,
            'periodEnd' => $end,
            'periodLabel' => $periodLabel ?? ($start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')),
            'sales' => $sales,
            'paymentMethods' => $byPaymentMethod,
            'dailySales' => $dailySales,
            'totalOrders' => $sales->count(),
            'totalVisitors' => (int) $sales->sum('quantity'),
            'totalCollected' => (float) $sales->sum(fn (Booking $sale) => (float) ($sale->amount_paid ?? ($sale->total_amount + (float) ($sale->payment?->transaction_fee ?? 0)))),
            'totalFees' => $totalFees,
        ];
    }

    public function emptyRange(Carbon $start, Carbon $end, ?string $periodLabel = null): array
    {
        return [
            'period' => 'range',
            'date' => $start->copy(),
            'periodStart' => $start->copy()->startOfDay(),
            'periodEnd' => $end->copy()->endOfDay(),
            'periodLabel' => $periodLabel ?? ($start->format('d/m/Y') . ' - ' . $end->format('d/m/Y')),
            'sales' => collect(),
            'paymentMethods' => collect(),
            'dailySales' => collect(),
            'totalOrders' => 0,
            'totalVisitors' => 0,
            'totalCollected' => 0,
            'totalFees' => 0,
        ];
    }

    public function rangeDescriptor(string $period, string $from, string $to): array
    {
        $matchesFormat = match ($period) {
            'monthly' => fn (string $value): bool => (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value),
            'yearly' => fn (string $value): bool => (bool) preg_match('/^\d{4}$/', $value),
            'daily' => fn (string $value): bool => (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                && checkdate((int) substr($value, 5, 2), (int) substr($value, 8, 2), (int) substr($value, 0, 4)),
            default => fn (string $value): bool => false,
        };

        if (! $matchesFormat($from) || ! $matchesFormat($to)) {
            throw ValidationException::withMessages(['from' => 'Enter a valid start and end period.']);
        }
        if ($from > $to) {
            throw ValidationException::withMessages(['to' => 'The end period must be on or after the start period.']);
        }

        [$format, $labelFormat] = match ($period) {
            'monthly' => ['!Y-m', 'M Y'],
            'yearly' => ['!Y', 'Y'],
            default => ['!Y-m-d', 'd/m/Y'],
        };
        $start = Carbon::createFromFormat($format, $from);
        $end = Carbon::createFromFormat($format, $to);
        $start = $period === 'monthly' ? $start->startOfMonth() : ($period === 'yearly' ? $start->startOfYear() : $start->startOfDay());
        $end = $period === 'monthly' ? $end->endOfMonth() : ($period === 'yearly' ? $end->endOfYear() : $end->endOfDay());

        return [
            'period' => $period,
            'fromInput' => $from,
            'toInput' => $to,
            'start' => $start,
            'end' => $end,
            'label' => $start->format($labelFormat) . ' - ' . $end->format($labelFormat),
        ];
    }

    public function makeFromInputs(string $period, string $from, string $to): array
    {
        $range = $this->rangeDescriptor($period, $from, $to);

        return $this->makeRange($range['start'], $range['end'], $range['label']);
    }

    public function normalizePeriod(?string $period, ?string $date): array
    {
        $period = in_array($period, ['daily', 'monthly', 'yearly'], true) ? $period : 'daily';

        try {
            $parsed = match ($period) {
                'monthly' => preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $date)
                    ? Carbon::createFromFormat('!Y-m', (string) $date)
                    : Carbon::today(),
                'yearly' => preg_match('/^\d{4}$/', (string) $date)
                    ? Carbon::createFromFormat('!Y', (string) $date)
                    : Carbon::today(),
                default => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)
                    ? Carbon::createFromFormat('!Y-m-d', (string) $date)
                    : Carbon::today(),
            };
        } catch (\Throwable) {
            $parsed = Carbon::today();
        }

        return [$period, $parsed];
    }

    private function bounds(string $period, Carbon $date): array
    {
        return match ($period) {
            'monthly' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            'yearly' => [$date->copy()->startOfYear(), $date->copy()->endOfYear()],
            default => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
        };
    }

    private function periodLabel(string $period, Carbon $date): string
    {
        return match ($period) {
            'monthly' => $date->format('F Y'),
            'yearly' => $date->format('Y'),
            default => $date->format('d/m/Y'),
        };
    }
}
