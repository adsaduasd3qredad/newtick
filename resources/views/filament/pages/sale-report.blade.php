<x-filament-panels::page>
    <div class="mb-6 overflow-hidden rounded-2xl border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-cyan-50 shadow-sm">
        <div class="border-b border-primary-100 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-primary-600 p-2.5 text-white shadow-sm">
                    <x-heroicon-o-chart-bar-square class="h-6 w-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">รายงานยอดขาย</h2>
                    <p class="text-sm text-gray-500">สรุปยอดและรายละเอียดการชำระเงินตามช่วงเวลาที่เลือก</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-primary-800">ช่วงเวลารายงาน</p>
                <p class="text-xs text-gray-500 mt-1">เลือกช่วงเวลาเพื่อปรับยอดและรายการล่าสุด</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="period" value="{{ $period }}">
                @if ($period === 'daily')
                    <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" class="rounded-lg border-gray-300 text-sm">
                @elseif ($period === 'monthly')
                    <input type="month" name="date" value="{{ $selectedDate->format('Y-m') }}" class="rounded-lg border-gray-300 text-sm">
                @else
                    <input type="number" name="date" value="{{ $selectedDate->year }}" min="2000" max="2100" class="w-24 rounded-lg border-gray-300 text-sm">
                @endif
                <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white">ดูรายงาน</button>
            </form>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($periods as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['period' => $key, 'date' => $key === 'yearly' ? $selectedDate->year : ($key === 'monthly' ? $selectedDate->format('Y-m') : $selectedDate->format('Y-m-d'))]) }}"
                    class="rounded-full px-4 py-2 text-sm font-semibold {{ $period === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm">
            <p class="text-sm font-medium text-emerald-800">รายได้{{ $periods[$period] }}</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">฿ {{ number_format($totalRevenue, 2) }}</p>
            <p class="mt-1 text-xs text-emerald-700/70">{{ $selectedDate->format($period === 'yearly' ? 'Y' : ($period === 'monthly' ? 'm/Y' : 'd/m/Y')) }}</p>
        </div>
        <div class="rounded-2xl border border-primary-100 bg-primary-50/70 p-5 shadow-sm">
            <p class="text-sm font-medium text-primary-800">จำนวนผู้เข้าชม</p>
            <p class="mt-2 text-3xl font-bold text-primary-700">{{ number_format($totalTicketsSold) }}</p>
            <p class="mt-1 text-xs text-primary-700/70">คน</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-800">ค่าธรรมเนียมรวม</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">฿ {{ number_format($totalFees, 2) }}</p>
            <p class="mt-1 text-xs text-amber-700/70">จากรายการในช่วงเวลานี้</p>
        </div>
    </div>

    <x-filament::section heading="สรุปตามประเภทผู้เข้าชม">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-left border-collapse">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="py-3 px-4 font-semibold text-gray-900 dark:text-white">ประเภทผู้เข้าชม</th>
                        <th class="py-3 px-4 font-semibold text-gray-900 dark:text-white text-right">จำนวนตั๋ว (ใบ)</th>
                        <th class="py-3 px-4 font-semibold text-gray-900 dark:text-white text-right">รายได้รวม (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visitorStats as $stat)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                @if($stat->visitor_type == 'individual') บุคคลทั่วไป
                                @elseif($stat->visitor_type == 'school') โรงเรียน/สถานศึกษา
                                @elseif($stat->visitor_type == 'government') หน่วยงานรัฐบาล
                                @elseif($stat->visitor_type == 'company') องค์กร/บริษัท
                                @else {{ $stat->visitor_type }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300 text-right">{{ number_format($stat->tickets) }}</td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300 text-right font-medium">฿ {{ number_format($stat->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">รวมทั้งหมด</td>
                        <td class="py-3 px-4 font-bold text-gray-900 dark:text-white text-right">{{ number_format($totalTicketsSold) }}</td>
                        <td class="py-3 px-4 font-bold text-success-600 text-right">฿ {{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">รายละเอียด Sale Report · {{ $periods[$period] }}</x-slot>
        <x-slot name="description">รายการที่ชำระแล้วและตรวจตั๋วแล้วตามช่วงเวลาที่เลือก</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-xs text-gray-600">
                        <th class="whitespace-nowrap px-3 py-3">โอนเงินวันที่</th>
                        <th class="whitespace-nowrap px-3 py-3">วันที่ชม</th>
                        <th class="whitespace-nowrap px-3 py-3">รอบ</th>
                        <th class="whitespace-nowrap px-3 py-3">เลขที่การจอง</th>
                        <th class="whitespace-nowrap px-3 py-3 text-right">จำนวนคน</th>
                        <th class="whitespace-nowrap px-3 py-3 text-right">จำนวนเงิน</th>
                        <th class="whitespace-nowrap px-3 py-3 text-right">ค่าธรรมเนียม</th>
                        <th class="whitespace-nowrap px-3 py-3 text-right">ส่วนลด</th>
                        <th class="whitespace-nowrap px-3 py-3 text-right">เก็บจริง</th>
                        <th class="whitespace-nowrap px-3 py-3">วิธีชำระ</th>
                        <th class="whitespace-nowrap px-3 py-3">เวลาที่โอน</th>
                        <th class="whitespace-nowrap px-3 py-3">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($recentOrders as $order)
                        @php
                            $fee = (float) ($order->payment?->transaction_fee ?? 0);
                            $total = (float) $order->total_amount;
                            $collected = (float) ($order->amount_paid ?? $order->total_amount);
                            $discount = max(0, $total - $collected);
                        @endphp
                        <tr class="align-top hover:bg-primary-50/30">
                            <td class="whitespace-nowrap px-3 py-3 text-gray-600">{{ $order->payment?->paid_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3">{{ $order->showtime?->show_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 font-medium">{{ $order->showtime?->show_time ? substr((string) $order->showtime->show_time, 0, 5) . ' น.' : '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 font-bold text-primary-700">#{{ str_pad((string) $order->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-3 py-3 text-right">{{ number_format($order->quantity) }}</td>
                            <td class="px-3 py-3 text-right">฿ {{ number_format($total, 2) }}</td>
                            <td class="px-3 py-3 text-right text-amber-700">฿ {{ number_format($fee, 2) }}</td>
                            <td class="px-3 py-3 text-right text-rose-700">฿ {{ number_format($discount, 2) }}</td>
                            <td class="px-3 py-3 text-right font-bold text-emerald-700">฿ {{ number_format($collected, 2) }}</td>
                            <td class="whitespace-nowrap px-3 py-3">{{ $order->payment_method === 'counter' ? 'POS' : 'ออนไลน์/QR' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-gray-600">{{ $order->payment?->paid_at?->format('H:i') ?? '-' }}</td>
                            <td class="max-w-[180px] px-3 py-3 text-xs text-gray-600">{{ $order->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($recentOrders->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">ไม่พบรายการในช่วงเวลานี้</div>
            @endif
        </div>
    </x-filament::section>
</x-filament-panels::page>
