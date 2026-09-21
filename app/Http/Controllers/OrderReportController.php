<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderReportController extends Controller
{
    public function pdf(Request $request): Response
    {
        [$period, $date, $label] = $this->period($request);
        return Pdf::loadView('reports.orders-pdf', [
            'bookings' => $this->bookings($period, $date),
            'title' => 'รายงานออเดอร์การจอง ' . $label,
        ])->download('orders-report-' . $period . '.pdf');
    }

    public function excel(Request $request): Response
    {
        [$period, $date] = $this->period($request);
        $rows = [];
        $rows[] = ['เลขที่การจอง', 'วันที่จอง', 'ภาพยนตร์', 'วันที่ฉาย', 'รอบฉาย', 'จำนวนคน', 'ยอดตั๋ว', 'ค่าธรรมเนียม', 'ยอดเก็บจริง', 'วิธีชำระ', 'สถานะ', 'หมายเหตุ'];

        foreach ($this->bookings($period, $date) as $booking) {
            $rows[] = [
                '#' . $booking->id,
                $booking->created_at?->format('d/m/Y H:i'),
                $booking->showtime?->movie?->title_th ?? '-',
                $booking->showtime?->show_date?->format('d/m/Y'),
                $booking->showtime?->show_time ? substr((string) $booking->showtime->show_time, 0, 5) : '-',
                $booking->quantity,
                number_format((float) $booking->total_amount, 2, '.', ''),
                number_format((float) ($booking->payment?->transaction_fee ?? 0), 2, '.', ''),
                number_format((float) ($booking->amount_paid ?? $booking->total_amount), 2, '.', ''),
                $booking->payment_method === 'counter' ? 'เคาน์เตอร์ POS' : 'ออนไลน์/QR',
                $booking->status,
                $booking->notes ?? '',
            ];
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="orders-report-' . $period . '.csv"');
    }

    private function bookings(string $period, Carbon $date)
    {
        $query = Booking::with(['showtime.movie', 'payment'])
            ->whereIn('status', ['paid', 'redeemed'])
            ->latest();

        return $this->applyPeriod($query, $period, $date)->get();
    }

    private function period(Request $request): array
    {
        $periods = ['weekly' => '7 วันล่าสุด', 'monthly' => 'รายเดือน', 'yearly' => 'รายปี'];
        $period = array_key_exists($request->query('period'), $periods) ? $request->query('period') : 'weekly';
        $date = Carbon::parse($request->query('date', today()->toDateString()));

        return [$period, $date, $periods[$period]];
    }

    private function applyPeriod(Builder $query, string $period, Carbon $date): Builder
    {
        return match ($period) {
            'weekly' => $query->whereBetween('created_at', [$date->copy()->subDays(6)->startOfDay(), $date->copy()->endOfDay()]),
            'monthly' => $query->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month),
            'yearly' => $query->whereYear('created_at', $date->year),
            default => $query->whereDate('created_at', $date->toDateString()),
        };
    }
}
