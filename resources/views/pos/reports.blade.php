@extends('pos.layout')
@section('title', 'Sales Report')
@section('content')
@php
    $dateType = $rangePeriod === 'monthly' ? 'month' : ($rangePeriod === 'yearly' ? 'number' : 'date');
    $exportQuery = ['range_period' => $rangePeriod, 'from' => $fromInput, 'to' => $toInput];
@endphp
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Sales Report</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $periodLabel }} · {{ $periodStart->format('d/m/Y') }} – {{ $periodEnd->format('d/m/Y') }}</p>
        </div>
        @if ($searched)
            <div class="flex gap-2">
                <a target="_blank" href="{{ route('pos.reports.pdf', [...$exportQuery, 'print' => 1]) }}" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">Print / PDF</a>
                <a href="{{ route('pos.reports.orders.excel', $exportQuery) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export Excel (.xlsx)</a>
            </div>
        @endif
    </div>

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <input type="hidden" name="search" value="1">
        <label class="text-sm font-medium text-slate-600">ช่วงเวลา
            <select name="range_period" class="mt-1 block rounded-lg border-slate-300 text-sm" onchange="const type = this.value === 'monthly' ? 'month' : (this.value === 'yearly' ? 'number' : 'date'); ['from','to'].forEach(name => { const field = this.form.elements.namedItem(name); field.type = type; field.value = ''; });">
                @foreach ($periods as $key => $label)
                    <option value="{{ $key }}" @selected($rangePeriod === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm font-medium text-slate-600">ตั้งแต่
            <input class="mt-1 block rounded-lg border-slate-300 text-sm" type="{{ $dateType }}" name="from" value="{{ $fromInput }}" required @if($rangePeriod === 'yearly') min="2000" max="2100" @endif>
        </label>
        <label class="text-sm font-medium text-slate-600">ถึง
            <input class="mt-1 block rounded-lg border-slate-300 text-sm" type="{{ $dateType }}" name="to" value="{{ $toInput }}" required @if($rangePeriod === 'yearly') min="2000" max="2100" @endif>
        </label>
        <button class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">ค้นหา</button>
    </form>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    @if (! $searched)
        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center text-slate-500">เลือกช่วงวันที่หรือเดือน แล้วกดค้นหาเพื่อแสดงรายงาน</div>
    @else
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Sales / Orders</div><div class="mt-1 text-2xl font-bold">{{ number_format($totalOrders) }}</div></div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Visitors</div><div class="mt-1 text-2xl font-bold">{{ number_format($totalVisitors) }}</div></div>
        <div class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Total Collected</div><div class="mt-1 text-2xl font-bold text-emerald-700">฿{{ number_format($totalCollected, 2) }}</div></div>
        <div class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Fees</div><div class="mt-1 text-2xl font-bold text-amber-700">฿{{ number_format($totalFees, 2) }}</div></div>
    </div>

    <section class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h3 class="font-bold text-slate-800">Revenue by Payment Method</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-left text-slate-600"><tr><th class="px-5 py-3">Method</th><th class="px-5 py-3 text-right">Orders</th><th class="px-5 py-3 text-right">Visitors</th><th class="px-5 py-3 text-right">Collected (THB)</th><th class="px-5 py-3 text-right">Fees (THB)</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($paymentMethods as $method)
                        <tr><td class="px-5 py-3">{{ $method->payment_method === 'counter' ? 'Counter / POS' : 'PromptPay QR' }}</td><td class="px-5 py-3 text-right">{{ number_format($method->orders) }}</td><td class="px-5 py-3 text-right">{{ number_format($method->visitors) }}</td><td class="px-5 py-3 text-right font-semibold">{{ number_format($method->collected, 2) }}</td><td class="px-5 py-3 text-right">{{ number_format($method->fees, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No sales for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h3 class="font-bold text-slate-800">Daily Sales Summary</h3><p class="text-xs text-slate-500">A daily breakdown for the selected period</p></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-left text-slate-600"><tr><th class="px-5 py-3">Date</th><th class="px-5 py-3 text-right">Orders</th><th class="px-5 py-3 text-right">Visitors</th><th class="px-5 py-3 text-right">Total Revenue (THB)</th><th class="px-5 py-3 text-right">Fees (THB)</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dailySales as $day)
                        <tr><td class="px-5 py-3">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td><td class="px-5 py-3 text-right">{{ number_format($day->orders) }}</td><td class="px-5 py-3 text-right">{{ number_format($day->visitors) }}</td><td class="px-5 py-3 text-right font-semibold text-emerald-700">{{ number_format($day->total, 2) }}</td><td class="px-5 py-3 text-right">{{ number_format($day->fees, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">No sales for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h3 class="font-bold text-slate-800">Transaction Details</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] text-sm">
                <thead class="bg-slate-100 text-left text-slate-600"><tr><th class="px-3 py-3">Paid Date</th><th class="px-3 py-3">Show Date</th><th class="px-3 py-3">Time</th><th class="px-3 py-3">Movie</th><th class="px-3 py-3">Booking</th><th class="px-3 py-3 text-right">Visitors</th><th class="px-3 py-3 text-right">Ticket Amount</th><th class="px-3 py-3 text-right">Fee</th><th class="px-3 py-3 text-right">Discount</th><th class="px-3 py-3 text-right">Collected</th><th class="px-3 py-3">Method</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Notes</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sales as $sale)
                        @php
                            $fee = (float) ($sale->payment?->transaction_fee ?? 0);
                            $collected = (float) ($sale->amount_paid ?? ($sale->total_amount + $fee));
                            $discount = max(0, (float) $sale->total_amount - ($collected - $fee));
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap px-3 py-3">{{ $sale->payment?->paid_at?->format('d/m/Y') ?? '-' }}</td><td class="whitespace-nowrap px-3 py-3">{{ $sale->showtime?->show_date?->format('d/m/Y') ?? '-' }}</td><td class="whitespace-nowrap px-3 py-3">{{ $sale->showtime?->show_time ? substr((string) $sale->showtime->show_time, 0, 5) : '-' }}</td><td class="px-3 py-3">{{ $sale->showtime?->movie?->title_th ?? '-' }}</td><td class="px-3 py-3 font-semibold">#{{ str_pad((string) $sale->id, 2, '0', STR_PAD_LEFT) }}</td><td class="px-3 py-3 text-right">{{ number_format($sale->quantity) }}</td><td class="px-3 py-3 text-right">{{ number_format($sale->total_amount, 2) }}</td><td class="px-3 py-3 text-right">{{ number_format($fee, 2) }}</td><td class="px-3 py-3 text-right">{{ number_format($discount, 2) }}</td><td class="px-3 py-3 text-right font-semibold text-emerald-700">{{ number_format($collected, 2) }}</td><td class="px-3 py-3">{{ $sale->payment_method === 'counter' ? 'Counter / POS' : 'PromptPay QR' }}</td><td class="px-3 py-3">{{ ucfirst($sale->status) }}</td><td class="max-w-xs truncate px-3 py-3">{{ $sale->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="px-5 py-8 text-center text-slate-500">No sales for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection
