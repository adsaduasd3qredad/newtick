<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        @page { size: A4 landscape; margin: 24px 28px 30px; }
        body { color: #172033; font-family: "DejaVu Sans", sans-serif; font-size: 8px; }
        h1 { margin: 0 0 3px; color: #0f766e; font-size: 20px; }
        h2 { margin: 16px 0 6px; color: #0f172a; font-size: 12px; }
        .meta { margin: 0 0 12px; color: #64748b; font-size: 8px; }
        .summary { width: 100%; margin: 0 0 12px; border-collapse: separate; border-spacing: 6px 0; }
        .summary td { width: 25%; padding: 8px; border: 1px solid #dbe4ea; background: #f8fafc; }
        .label { display: block; color: #64748b; font-size: 7px; text-transform: uppercase; }
        .value { display: block; margin-top: 3px; color: #0f172a; font-size: 13px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; margin: 0 0 10px; }
        table.data th { padding: 5px 4px; border: 1px solid #cbd5e1; background: #0f766e; color: white; text-align: left; }
        table.data td { padding: 4px; border: 1px solid #dbe4ea; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #f8fafc; }
        .num { text-align: right !important; white-space: nowrap; }
        .nowrap { white-space: nowrap; }
        .muted { color: #64748b; }
        .empty { padding: 12px !important; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    <p class="meta">Period: {{ $periodLabel }} ({{ $periodStart->format('Y-m-d') }} to {{ $periodEnd->format('Y-m-d') }}) &nbsp; | &nbsp; Generated: {{ now()->format('Y-m-d H:i:s') }}</p>

    <table class="summary">
        <tr>
            <td><span class="label">Orders</span><span class="value">{{ number_format($totalOrders) }}</span></td>
            <td><span class="label">Visitors</span><span class="value">{{ number_format($totalVisitors) }}</span></td>
            <td><span class="label">Total Collected (THB)</span><span class="value">{{ number_format($totalCollected, 2) }}</span></td>
            <td><span class="label">Fees (THB)</span><span class="value">{{ number_format($totalFees, 2) }}</span></td>
        </tr>
    </table>

    <h2>Revenue by Payment Method</h2>
    <table class="data">
        <thead><tr><th>Method</th><th class="num">Orders</th><th class="num">Visitors</th><th class="num">Total Collected (THB)</th><th class="num">Fees (THB)</th></tr></thead>
        <tbody>
        @forelse ($paymentMethods as $method)
            <tr><td>{{ $method->payment_method === 'counter' ? 'Counter / POS' : 'PromptPay QR' }}</td><td class="num">{{ number_format($method->orders) }}</td><td class="num">{{ number_format($method->visitors) }}</td><td class="num">{{ number_format($method->collected, 2) }}</td><td class="num">{{ number_format($method->fees, 2) }}</td></tr>
        @empty
            <tr><td class="empty" colspan="5">No payment records in this period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Daily Sales Summary</h2>
    <table class="data">
        <thead><tr><th>Date</th><th class="num">Orders</th><th class="num">Visitors</th><th class="num">Total Revenue (THB)</th><th class="num">Fees (THB)</th></tr></thead>
        <tbody>
        @forelse ($dailySales as $day)
            <tr><td>{{ $day->date }}</td><td class="num">{{ number_format($day->orders) }}</td><td class="num">{{ number_format($day->visitors) }}</td><td class="num">{{ number_format($day->total, 2) }}</td><td class="num">{{ number_format($day->fees, 2) }}</td></tr>
        @empty
            <tr><td class="empty" colspan="5">No sales in this period.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Transaction Details</h2>
    <table class="data">
        <thead><tr><th>Paid Date</th><th>Showtime</th><th>Movie</th><th>Booking</th><th class="num">Visitors</th><th class="num">Ticket Amount</th><th class="num">Fee</th><th class="num">Discount</th><th class="num">Collected</th><th>Method</th><th>Status</th><th>Notes</th></tr></thead>
        <tbody>
        @forelse ($sales as $sale)
            @php
                $fee = (float) ($sale->payment?->transaction_fee ?? 0);
                $collected = (float) ($sale->amount_paid ?? ($sale->total_amount + $fee));
                $discount = max(0, (float) $sale->total_amount - ($collected - $fee));
            @endphp
            <tr>
                <td class="nowrap">{{ $sale->payment?->paid_at?->format('Y-m-d') ?? '-' }}</td>
                <td class="nowrap">{{ $sale->showtime?->show_date?->format('Y-m-d') ?? '-' }} {{ $sale->showtime?->show_time ? substr((string) $sale->showtime->show_time, 0, 5) : '' }}</td>
                <td>{{ $sale->showtime?->movie?->title_en ?: ('Movie #' . ($sale->showtime?->movie_id ?? '')) }}</td>
                <td class="nowrap">#{{ str_pad((string) $sale->id, 2, '0', STR_PAD_LEFT) }}</td>
                <td class="num">{{ number_format($sale->quantity) }}</td>
                <td class="num">{{ number_format($sale->total_amount, 2) }}</td>
                <td class="num">{{ number_format($fee, 2) }}</td>
                <td class="num">{{ number_format($discount, 2) }}</td>
                <td class="num">{{ number_format($collected, 2) }}</td>
                <td>{{ $sale->payment_method === 'counter' ? 'Counter / POS' : 'PromptPay QR' }}</td>
                <td>{{ ucfirst($sale->status) }}</td>
                <td>{{ $sale->notes ?: '-' }}</td>
            </tr>
        @empty
            <tr><td class="empty" colspan="12">No sales in this period.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
