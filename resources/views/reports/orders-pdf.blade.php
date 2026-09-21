<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .summary { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; }
        th { background: #e2e8f0; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="summary">ออกรายงานเมื่อ {{ now()->format('d/m/Y H:i') }} | จำนวน {{ $bookings->count() }} ออเดอร์</div>
    <table>
        <thead>
            <tr>
                <th>เลขที่</th><th>วันที่จอง</th><th>ภาพยนตร์ / รอบ</th><th>จำนวน</th>
                <th class="right">ยอดตั๋ว</th><th class="right">ค่าธรรมเนียม</th><th class="right">เก็บจริง</th>
                <th>วิธีชำระ</th><th>หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td>#{{ $booking->id }}</td>
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
