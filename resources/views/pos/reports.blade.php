@extends('pos.layout')
@section('title', 'รายงาน (Reports)')
@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">รายงานยอดขายหน้าเคาน์เตอร์</h2>
        <div class="flex gap-2">
            <a href="{{ route('pos.reports.pdf') }}" target="_blank" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold border border-red-200">Export PDF</a>
            <a href="{{ route('pos.reports.csv') }}" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 px-4 py-2 rounded-lg text-sm font-semibold border border-emerald-200">Export CSV (Excel)</a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-bold mb-4 text-slate-700">รายได้แยกตามช่องทาง</h3>
            <ul class="space-y-3">
                @foreach($paymentMethods as $pm)
                <li class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-sm">{{ $pm->payment_method == 'counter' ? 'เงินสด (Counter)' : 'ออนไลน์ (QR)' }}</span>
                    <span class="font-bold text-emerald-600">{{ number_format($pm->total, 2) }} ฿</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-700">รายงานรายวัน (Daily Report - 30 วันล่าสุด)</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 bg-slate-50 uppercase border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3">วันที่</th>
                    <th class="px-6 py-3 text-right">จำนวนตั๋ว</th>
                    <th class="px-6 py-3 text-right">ยอดรวม (฿)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($dailySales as $ds)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse($ds->date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 text-right">{{ $ds->tickets }}</td>
                    <td class="px-6 py-3 text-right font-bold text-emerald-600">{{ number_format($ds->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection