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
                <h2 class="font-bold text-lg text-slate-800">รายการออเดอร์</h2>
                <p class="text-xs text-slate-500 mt-0.5">ข้อมูลการจอง การชำระเงิน และหมายเหตุสำคัญ</p>
            </div>
        </div>

        <div class="flex gap-2 overflow-x-auto border-b border-slate-200 bg-white px-4 py-3">
            @foreach($statuses as $key => $label)
                <a href="{{ route('pos.orders', $key === 'all' ? [] : ['status' => $key]) }}"
                    class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $selectedStatus === $key ? 'bg-cyan-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                    <span class="ml-1 opacity-80 font-mono">({{ $key === 'all' ? $statusCounts->sum() : ($statusCounts[$key] ?? 0) }})</span>
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-4 py-3.5 font-semibold whitespace-nowrap">รหัสจอง</th>
                        <th class="px-4 py-3.5 font-semibold whitespace-nowrap">วันที่จอง</th>
                        <th class="px-4 py-3.5 font-semibold">ภาพยนตร์ / รอบ</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">จำนวน</th>
                        <th class="px-4 py-3.5 font-semibold whitespace-nowrap">ช่องทางชำระ</th>
                        <th class="px-4 py-3.5 font-semibold text-right whitespace-nowrap">ยอดเก็บจริง</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">สถานะ</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">การจัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $b)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        {{-- 1. รหัสจอง --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="font-bold text-slate-900 font-mono text-sm">#{{ $b->id }}</span>
                        </td>

                        {{-- 2. วันที่จอง --}}
                        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-600">
                            <div>{{ $b->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $b->created_at->format('H:i') }} น.</div>
                        </td>

                        {{-- 3. ภาพยนตร์ / รอบ --}}
                        <td class="px-4 py-3.5 min-w-[200px]">
                            <div class="font-semibold text-slate-800 text-sm leading-snug">{{ $b->showtime->movie->title_th ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                รอบ {{ \Carbon\Carbon::parse($b->showtime->show_date)->format('d/m/Y') }} &bull; {{ \Carbon\Carbon::parse($b->showtime->show_time)->format('H:i') }} น.
                            </div>
                            @if($b->notes)
                                <div class="text-[11px] text-amber-700 bg-amber-50 rounded px-1.5 py-0.5 mt-1 inline-block border border-amber-200/60 max-w-xs truncate" title="{{ $b->notes }}">
                                    {{ $b->notes }}
                                </div>
                            @endif
                        </td>

                        {{-- 4. จำนวน --}}
                        <td class="px-4 py-3.5 text-center whitespace-nowrap font-medium text-slate-800">
                            {{ $b->quantity }} คน
                        </td>

                        {{-- 5. ช่องทางชำระ --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($b->payment_method === 'counter')
                                <span class="inline-flex items-center gap-1 text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md text-xs font-medium">
                                    เงินสด
                                </span>
                            @elseif($b->payment_method === 'qr_code')
                                <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md text-xs font-medium">
                                    โอนเงิน (QR)
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- 6. ยอดเก็บจริง --}}
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            @if(in_array($b->status, ['paid', 'redeemed'], true))
                                <span class="font-bold text-slate-900 font-mono text-sm">
                                    {{ number_format($b->amount_paid ?? $b->total_amount, 2) }}
                                </span>
                                <span class="text-xs text-slate-500">บ.</span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- 7. สถานะ --}}
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @php
                                $statusClass = match ($b->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'redeemed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'expired', 'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                                };
                                $statusLabel = match ($b->status) {
                                    'pending' => 'รอชำระ',
                                    'awaiting_payment' => $b->payment_method === 'counter' ? 'รอชำระหน้าเคาน์เตอร์' : 'รอตรวจสลิป',
                                    'paid' => 'ชำระแล้ว',
                                    'redeemed' => 'ตรวจตั๋วแล้ว',
                                    'expired' => 'หมดอายุ',
                                    'cancelled' => 'ยกเลิก',
                                    default => $b->status,
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- 8. การจัดการ (Action) --}}
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('pos.receipt', $b->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-cyan-50 text-slate-700 hover:text-cyan-700 border border-slate-200 hover:border-cyan-300 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>ดูใบเสร็จ / ตั๋ว</span>
                                </a>

                                @if($b->payment?->slip_path)
                                    <a href="{{ asset('storage/' . $b->payment->slip_path) }}" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>สลิป</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <p class="text-sm font-medium">ไม่พบข้อมูลออเดอร์ในหมวดหมู่นี้</p>
                        </td>
                    </tr>
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
