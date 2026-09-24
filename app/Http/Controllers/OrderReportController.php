<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\SalesReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderReportController extends Controller
{
    public function pdf(Request $request, SalesReportService $reports)
    {
        $data = $this->reportData($request, $reports);
        $suffix = $data['period'] === 'range'
            ? $data['periodStart']->format('Ymd') . '-to-' . $data['periodEnd']->format('Ymd')
            : $data['period'] . '-' . $data['date']->format('Ymd');

        $pdf = Pdf::loadView('reports.orders-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'DejaVu Sans']);

        return $request->boolean('print')
            ? $pdf->stream('sales-report-' . $suffix . '.pdf')
            : $pdf->download('sales-report-' . $suffix . '.pdf');
    }

    public function excel(Request $request, SalesReportService $reports): BinaryFileResponse
    {
        $data = $this->reportData($request, $reports);

        $directory = storage_path('app/reports');
        File::ensureDirectoryExists($directory);
        $path = tempnam($directory, 'sales-report-');
        $suffix = $data['period'] === 'range'
            ? $data['periodStart']->format('Ymd') . '-to-' . $data['periodEnd']->format('Ymd')
            : $data['period'] . '-' . $data['date']->format('Ymd');
        $fileName = 'sales-report-' . $suffix . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($path);
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Sales Report');
        $sheet->setColumnWidth(20, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14);
        $sheet->setColumnWidth(34, 3, 12);

        $titleStyle = (new Style())->setFontBold()->setFontSize(16)->setFontColor('FFFFFFFF');
        $sectionStyle = (new Style())->setFontBold()->setBackgroundColor('FFE2E8F0');
        $headerStyle = (new Style())->setFontBold()->setFontColor('FFFFFFFF')->setBackgroundColor('FF0F766E');
        $dateStyle = (new Style())->setFormat('yyyy-mm-dd');
        $moneyStyle = (new Style())->setFormat('#,##0.00');

        $writer->addRow(Row::fromValues(['Sales Report'], $titleStyle));
        $writer->addRow(Row::fromValues(['Period', $data['periodLabel']]));
        $writer->addRow(Row::fromValues(['Generated at', now()->format('Y-m-d H:i:s')]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Summary'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Metric', 'Value'], $headerStyle));
        $writer->addRow(Row::fromValues(['Orders', $data['totalOrders']]));
        $writer->addRow(Row::fromValues(['Visitors', $data['totalVisitors']]));
        $writer->addRow(Row::fromValuesWithStyles(['Collected (THB)', $data['totalCollected']], null, [1 => $moneyStyle]));
        $writer->addRow(Row::fromValuesWithStyles(['Fees (THB)', $data['totalFees']], null, [1 => $moneyStyle]));

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Revenue by Payment Method'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Method', 'Orders', 'Visitors', 'Collected (THB)', 'Fees (THB)'], $headerStyle));
        foreach ($data['paymentMethods'] as $method) {
            $writer->addRow(Row::fromValuesWithStyles([
                $this->methodLabel($method->payment_method),
                (int) $method->orders,
                (int) $method->visitors,
                (float) $method->collected,
                (float) $method->fees,
            ], null, [3 => $moneyStyle, 4 => $moneyStyle]));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Daily Sales'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Date', 'Orders', 'Visitors', 'Total Revenue (THB)', 'Fees (THB)'], $headerStyle));
        foreach ($data['dailySales'] as $day) {
            $writer->addRow(Row::fromValuesWithStyles([
                new DateTimeImmutable((string) $day->date),
                (int) $day->orders,
                (int) $day->visitors,
                (float) $day->total,
                (float) $day->fees,
            ], null, [0 => $dateStyle, 3 => $moneyStyle, 4 => $moneyStyle]));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Transaction Details'], $sectionStyle));
        $writer->addRow(Row::fromValues([
            'Paid Date', 'Show Date', 'Show Time', 'Movie', 'Booking', 'Visitors',
            'Ticket Amount (THB)', 'Fee (THB)', 'Discount (THB)', 'Collected (THB)',
            'Method', 'Paid Time', 'Status', 'Notes',
        ], $headerStyle));

        foreach ($data['sales'] as $sale) {
            $fee = (float) ($sale->payment?->transaction_fee ?? 0);
            $collected = (float) ($sale->amount_paid ?? ($sale->total_amount + $fee));
            $writer->addRow(Row::fromValuesWithStyles([
                $sale->payment?->paid_at ? new DateTimeImmutable($sale->payment->paid_at->format('Y-m-d')) : '',
                $sale->showtime?->show_date ? new DateTimeImmutable($sale->showtime->show_date->format('Y-m-d')) : '',
                $sale->showtime?->show_time ? substr((string) $sale->showtime->show_time, 0, 5) : '',
                $sale->showtime?->movie?->title_en ?: ('Movie #' . ($sale->showtime?->movie_id ?? '')),
                '#' . str_pad((string) $sale->id, 2, '0', STR_PAD_LEFT),
                (int) $sale->quantity,
                (float) $sale->total_amount,
                $fee,
                max(0, (float) $sale->total_amount - ($collected - $fee)),
                $collected,
                $this->methodLabel($sale->payment_method),
                $sale->payment?->paid_at?->format('H:i') ?? '',
                ucfirst($sale->status),
                (string) ($sale->notes ?? ''),
            ], null, [0 => $dateStyle, 1 => $dateStyle, 6 => $moneyStyle, 7 => $moneyStyle, 8 => $moneyStyle, 9 => $moneyStyle]));
        }

        $writer->close();

        return response()->download($path, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function methodLabel(?string $method): string
    {
        return match ($method) {
            'counter' => 'Counter / POS',
            'qr_code' => 'PromptPay QR',
            default => 'Unknown',
        };
    }

    private function reportData(Request $request, SalesReportService $reports): array
    {
        if ($request->filled('range_period')) {
            $validated = $request->validate([
                'range_period' => ['required', 'in:daily,monthly,yearly'],
                'from' => ['required', 'string'],
                'to' => ['required', 'string'],
            ]);
            $range = $reports->rangeDescriptor($validated['range_period'], $validated['from'], $validated['to']);

            return $reports->makeRange($range['start'], $range['end'], $range['label']);
        }

        [$period, $date] = $reports->normalizePeriod($request->query('period'), $request->query('date'));

        return $reports->make($period, $date);
    }
}
