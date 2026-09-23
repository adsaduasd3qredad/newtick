<x-filament-panels::page>
    <style>
        .sales-report { color: #172033; }
        .sales-report .report-shell { overflow: hidden; border: 1px solid #e5e7eb; border-radius: 18px; background: #fff; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
        .sales-report .report-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 24px 28px; background: linear-gradient(135deg, #0f766e, #155e75); color: #fff; }
        .sales-report .report-title { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -.02em; }
        .sales-report .report-subtitle { margin-top: 5px; color: #ccfbf1; font-size: 13px; }
        .sales-report .report-icon { display: grid; width: 48px; height: 48px; place-items: center; border-radius: 14px; background: rgba(255,255,255,.16); }
        .sales-report .export-links { display: flex; flex-wrap: wrap; gap: 8px; }
        .sales-report .export-link { display: inline-flex; align-items: center; gap: 7px; border-radius: 9px; padding: 9px 13px; background: #f59e0b; color: #422006; font-size: 12px; font-weight: 700; text-decoration: none; }
        .sales-report .export-link:hover { background: #fbbf24; }
        .sales-report .filter-bar { display: flex; flex-wrap: wrap; align-items: end; justify-content: space-between; gap: 16px; padding: 18px 28px; border-bottom: 1px solid #e5e7eb; background: #f8fafc; }
        .sales-report .filter-left { display: flex; flex-wrap: wrap; align-items: end; gap: 10px; }
        .sales-report .filter-label { display: block; margin-bottom: 5px; color: #475569; font-size: 11px; font-weight: 700; }
        .sales-report .filter-input { min-height: 38px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; padding: 7px 10px; color: #1e293b; font-size: 13px; }
        .sales-report .filter-submit { min-height: 38px; border: 0; border-radius: 8px; padding: 0 16px; background: #0f766e; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; }
        .sales-report .filter-submit:hover { background: #115e59; }
        .sales-report .period-tabs { display: flex; flex-wrap: wrap; gap: 6px; }
        .sales-report .period-tab { border-radius: 999px; padding: 8px 13px; color: #475569; background: #e2e8f0; font-size: 12px; font-weight: 700; text-decoration: none; }
        .sales-report .period-tab.active { background: #0f766e; color: #fff; }
        .sales-report .range-note { color: #64748b; font-size: 12px; line-height: 1.5; text-align: right; }
        .sales-report .range-note strong { display: block; color: #0f172a; font-size: 14px; }
        .sales-report .summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; padding: 18px 28px; background: #fff; }
        .sales-report .summary-card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 13px 15px; background: #f8fafc; }
        .sales-report .summary-label { color: #64748b; font-size: 11px; font-weight: 700; }
        .sales-report .summary-value { margin-top: 4px; color: #0f172a; font-size: 20px; font-weight: 800; }
        .sales-report .summary-value.green { color: #047857; }
        .sales-report .summary-value.amber { color: #b45309; }
        .sales-report .table-wrap { overflow-x: auto; border-top: 1px solid #e5e7eb; }
        .sales-report table { width: 100%; min-width: 1260px; border-collapse: separate; border-spacing: 0; font-size: 12px; }
        .sales-report th, .sales-report td { border-right: 1px solid #eef2f7; border-bottom: 1px solid #e5e7eb; padding: 11px 12px; vertical-align: middle; }
        .sales-report th:last-child, .sales-report td:last-child { border-right: 0; }
        .sales-report thead tr:first-child th { border-bottom: 0; background: #0f172a; color: #e2e8f0; font-size: 11px; letter-spacing: .02em; text-align: left; }
        .sales-report thead tr:nth-child(2) th { background: #f1f5f9; color: #334155; font-size: 11px; white-space: nowrap; }
        .sales-report tbody tr:nth-child(even) { background: #fcfdff; }
        .sales-report tbody tr:hover { background: #f0fdfa; }
        .sales-report .num { text-align: right; white-space: nowrap; }
        .sales-report .nowrap { white-space: nowrap; }
        .sales-report .booking-code { color: #0f766e; font-weight: 800; }
        .sales-report .money { font-variant-numeric: tabular-nums; }
        .sales-report .collected { color: #047857; font-weight: 800; }
        .sales-report .fee { color: #b45309; }
        .sales-report .discount { color: #be123c; }
        .sales-report .method { display: inline-block; border-radius: 999px; padding: 4px 8px; background: #e0f2fe; color: #075985; font-size: 11px; font-weight: 700; }
        .sales-report .note { max-width: 240px; color: #64748b; }
        .sales-report .empty { padding: 56px 20px; color: #64748b; text-align: center; }
        .sales-report .report-footer { display: flex; justify-content: space-between; gap: 12px; padding: 13px 28px; background: #f8fafc; color: #64748b; font-size: 12px; }
        @media (max-width: 1100px) {
            .sales-report .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .sales-report .report-header { align-items: flex-start; flex-direction: column; padding: 20px; }
            .sales-report .filter-bar, .sales-report .summary-grid { padding-right: 20px; padding-left: 20px; }
            .sales-report .summary-grid { grid-template-columns: 1fr; }
            .sales-report .range-note { text-align: left; }
            .sales-report .report-footer { flex-direction: column; padding-right: 20px; padding-left: 20px; }
        }
    </style>

    @php
        $totalSales = $sales->count();
        $totalCollected = $sales->sum(fn ($sale) => (float) ($sale->amount_paid ?? $sale->total_amount));
        $totalFees = $sales->sum(fn ($sale) => (float) ($sale->payment?->transaction_fee ?? 0));
        $totalPeople = $sales->sum('quantity');
    @endphp

    <div class="sales-report">
        <div class="report-shell">
            

            <div class="filter-bar">
                <div class="filter-left">
                    <div class="period-tabs">
                        @foreach ($periods as $key => $label)
                            <a class="period-tab {{ $period === $key ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['period' => $key, 'date' => $selectedDate->toDateString()]) }}">{{ $label }}</a>
                        @endforeach
                    </div>
                    <form method="GET" style="display:flex;align-items:end;gap:8px;">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <div>
                            <label class="filter-label">{{ $period === 'daily' ? 'วันที่' : ($period === 'monthly' ? 'เดือน' : 'ปี') }}</label>
                            @if ($period === 'daily')
                                <input class="filter-input" type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                            @elseif ($period === 'monthly')
                                <input class="filter-input" type="month" name="date" value="{{ $selectedDate->format('Y-m') }}">
                            @else
                                <input class="filter-input" style="width:88px;" type="number" name="date" value="{{ $selectedDate->year }}" min="2000" max="2100">
                            @endif
                        </div>
                        <button class="filter-submit" type="submit">แสดงข้อมูล</button>
                    </form>
                </div>
                <div class="range-note">ช่วงข้อมูล<strong>{{ $period === 'daily' ? $periodStart->format('d/m/Y') : $periodStart->format('d/m/Y') . ' - ' . $periodEnd->format('d/m/Y') }}</strong></div>
            </div>

            <div class="summary-grid">
                <div class="summary-card"><div class="summary-label">รายการขาย</div><div class="summary-value">{{ number_format($totalSales) }} รายการ</div></div>
                <div class="summary-card"><div class="summary-label">จำนวนผู้เข้าชม</div><div class="summary-value">{{ number_format($totalPeople) }} คน</div></div>
                <div class="summary-card"><div class="summary-label">ยอดเก็บจริงรวม</div><div class="summary-value green">฿ {{ number_format($totalCollected, 2) }}</div></div>
                <div class="summary-card"><div class="summary-label">ค่าธรรมเนียมรวม</div><div class="summary-value amber">฿ {{ number_format($totalFees, 2) }}</div></div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th colspan="5">ข้อมูลการเข้าชม</th>
                            <th colspan="4">รายการเงิน</th>
                            <th colspan="3">การชำระเงินและหมายเหตุ</th>
                        </tr>
                        <tr>
                            <th>โอนเงินวันที่</th><th>วันที่ชม</th><th>รอบ</th><th>เลขที่การจอง</th><th class="num">จำนวนคน</th>
                            <th class="num">จำนวนเงิน</th><th class="num">ค่าธรรมเนียม</th><th class="num">ส่วนลด</th><th class="num">เก็บจริง</th>
                            <th>วิธีชำระ</th><th>เวลาที่โอน</th><th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            @php
                                $total = (float) $sale->total_amount;
                                $collected = (float) ($sale->amount_paid ?? $sale->total_amount);
                                $fee = (float) ($sale->payment?->transaction_fee ?? 0);
                            @endphp
                            <tr>
                                <td class="nowrap">{{ $sale->payment?->paid_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="nowrap">{{ $sale->showtime?->show_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="nowrap">{{ $sale->showtime?->show_time ? substr((string) $sale->showtime->show_time, 0, 5) . ' น.' : '-' }}</td>
                                <td class="booking-code nowrap">#{{ str_pad((string) $sale->id, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="num">{{ number_format($sale->quantity) }}</td>
                                <td class="num money">฿ {{ number_format($total, 2) }}</td>
                                <td class="num money fee">฿ {{ number_format($fee, 2) }}</td>
                                <td class="num money discount">฿ {{ number_format(max(0, $total - $collected), 2) }}</td>
                                <td class="num money collected">฿ {{ number_format($collected, 2) }}</td>
                                <td><span class="method">{{ $sale->payment_method === 'counter' ? 'POS' : 'ออนไลน์ / QR' }}</span></td>
                                <td class="nowrap">{{ $sale->payment?->paid_at?->format('H:i') ?? '-' }}</td>
                                <td class="note">{{ $sale->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td class="empty" colspan="12">ไม่พบรายการขายในช่วงเวลานี้</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="report-footer">
                <span>แสดง {{ number_format($totalSales) }} รายการ · {{ number_format($totalPeople) }} คน</span>
                <span>อัปเดตล่าสุด {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</x-filament-panels::page>
