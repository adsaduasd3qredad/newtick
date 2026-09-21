<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class OrderReportController extends Controller
{
    public function pdf(): Response
    {
        return Pdf::loadView('reports.orders-pdf', [
            'bookings' => $this->bookings(),
            'title' => 'รายงานออเดอร์การจอง',
        ])->download('orders-report.pdf');
    }

    public function excel(): Response
    {
        $rows = [];
        $rows[] = ['เลขที่การจอง', 'วันที่จอง', 'ภาพยนตร์', 'วันที่ฉาย', 'รอบฉาย', 'จำนวนคน', 'ยอดตั๋ว', 'ค่าธรรมเนียม', 'ยอดเก็บจริง', 'วิธีชำระ', 'สถานะ', 'หมายเหตุ'];

        foreach ($this->bookings() as $booking) {
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
            ->header('Content-Disposition', 'attachment; filename="orders-report.csv"');
    }

    private function bookings()
    {
        return Booking::with(['showtime.movie', 'payment'])
            ->whereIn('status', ['paid', 'redeemed'])
            ->latest()
            ->get();
    }
}
