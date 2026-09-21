@extends('pos.layout')
@section('title', 'รายการจอง (Orders)')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'ออเดอร์ที่ชำระแล้ว', 'value' => number_format($summary['orders']) . ' รายการ', 'class' => 'text-cyan-700'],
            ['label' => 'จำนวนผู้เข้าชม', 'value' => number_format($summary['tickets']) . ' คน', 'class' => 'text-blue-700'],
            ['label' => 'ยอดเก็บจริง', 'value' => number_format($summary['collected'], 2) . ' บาท', 'class' => 'text-emerald-700'],
            ['label' => 'ค่าธรรมเนียมรวม', 'value' => number_format($summary['fees'], 2) . ' บาท', 'class' => 'text-amber-700'],
        ] as $card)
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <p class="text-xs text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-lg font-bold {{ $card['class'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-lg">รายการออเดอร์</h2>
                <p class="text-xs text-slate-500 mt-1">ข้อมูลการจอง การชำระเงิน และหมายเหตุสำคัญ</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pos.reports.orders.pdf') }}" target="_blank" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">ส่งออก PDF</a>
                <a href="{{ route('pos.reports.orders.excel') }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">ส่งออก Excel</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-slate-50 uppercase border-b border-slate-200">
                    <tr class="bg-slate-100 text-slate-600">
                        <th colspan="4" class="px-6 py-2">ข้อมูลการจอง</th>
                        <th colspan="3" class="px-6 py-2">การชำระเงิน</th>
                        <th colspan="4" class="px-6 py-2">ผลการขายและการติดตาม</th>
                    </tr>
                    <tr>
                        <th class="px-6 py-3">เลขหน้าเคาน์เตอร์</th>
                        <th class="px-6 py-3">วันที่จอง</th>
                        <th class="px-6 py-3">โอนเงินวันที่</th>
                        <th class="px-6 py-3">ภาพยนตร์ / รอบ</th>
                        <th class="px-6 py-3">จำนวนคน</th>
                        <th class="px-6 py-3">หมายเหตุ</th>
                        <th class="px-6 py-3">ช่องทางชำระ</th>
                        <th class="px-6 py-3 text-right">ค่าธรรมเนียม</th>
                        <th class="px-6 py-3 text-right">ยอดเก็บจริง</th>
                        <th class="px-6 py-3 text-center">สถานะ</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $b)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-semibold">#{{ $b->id }}</td>
                        <td class="px-6 py-4">{{ $b->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">{{ $b->payment?->paid_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $b->showtime->movie->title_th ?? '-' }}</div>
                            <div class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($b->showtime->show_date)->format('d/m/Y') }}
                                {{ \Carbon\Carbon::parse($b->showtime->show_time)->format('H:i') }} น.
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $b->quantity }} คน</td>
                        <td class="px-6 py-4 max-w-xs text-xs text-slate-600">{{ $b->notes ?: '-' }}</td>
                        <td class="px-6 py-4">
                            @if($b->payment_method == 'counter')
                                <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs">เงินสดหน้าเคาน์เตอร์</span>
                            @else
                                <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded text-xs">ออนไลน์ (QR)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">{{ number_format($b->payment?->transaction_fee ?? 0, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold">{{ number_format($b->amount_paid ?? $b->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                {{ $b->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($b->status === 'redeemed' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $b->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('pos.receipt', $b->id) }}" target="_blank" class="text-cyan-600 hover:underline text-xs">พิมพ์ตั๋ว</a>
                            @if($b->payment?->slip_path)
                                <a href="{{ asset('storage/' . $b->payment->slip_path) }}" target="_blank" rel="noopener"
                                    class="ml-2 text-amber-600 hover:underline text-xs">ดูสลิป</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center py-8 text-slate-500">ไม่มีข้อมูลการจอง</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection