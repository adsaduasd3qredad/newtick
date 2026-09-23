<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A4 landscape; margin: 24px; }
        body { font-family: freeserif, DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }
        h1 { color: #0e7490; font-size: 18px; margin: 0 0 4px; }
        .summary { color: #64748b; margin-bottom: 14px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; }
        th, td { border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 6px; }
        th { background: #e0f2fe; color: #155e75; text-align: left; }
        tr:last-child td { border-bottom: 0; }
        th:last-child, td:last-child { border-right: 0; }
        .right { text-align: right; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="summary">ออกรายงานเมื่อ {{ now()->format('d/m/Y H:i') }} | จำนวน {{ $bookings->count() }} ออเดอร์</div>
    <table>
        <thead>
            <tr>
                <th>รหัสจอง</th><th>วันที่จอง</th><th>ภาพยนตร์ / รอบ</th><th>จำนวนคน</th>
                <th class="right">ยอดตั๋ว</th><th class="right">ค่าธรรมเนียม</th><th class="right">เก็บจริง</th>
                <th>วิธีชำระ</th><th>หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td class="status">#{{ $booking->id }}</td>
                <td>{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                <td>{{ $booking->showtime?->movie?->title_th ?? '-' }}<br>{{ $booking->showtime?->show_date?->format('d/m/Y') }} {{ substr((string) ($booking->showtime?->show_time ?? ''), 0, 5) }}</td>
                <td>{{ $booking->quantity }}</td>
                <td class="right">{{ number_format((float) $booking->total_amount, 2) }}</td>
                <td class="right">{{ number_format((float) ($booking->payment?->transaction_fee ?? 0), 2) }}</td>
                <td class="right">{{ number_format((float) ($booking->amount_paid ?? $booking->total_amount), 2) }}</td>
                <td>{{ $booking->payment_method === 'counter' ? 'เคาน์เตอร์ POS' : 'ออนไลน์/QR' }}</td>
                <td>{{ $booking->notes ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
