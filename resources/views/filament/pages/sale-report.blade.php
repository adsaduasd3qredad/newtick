<x-filament-panels::page>
    <div class="mb-6 rounded-2xl border border-primary-100 bg-primary-50/60 p-4">
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-filament::section>
            <div class="text-sm font-medium text-gray-500">รายได้{{ $periods[$period] }}</div>
            <div class="text-3xl font-bold text-success-600 mt-2">฿ {{ number_format($totalRevenue, 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $selectedDate->format($period === 'yearly' ? 'Y' : ($period === 'monthly' ? 'm/Y' : 'd/m/Y')) }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm font-medium text-gray-500">รายได้ทั้งหมด</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">฿ {{ number_format($totalRevenue, 2) }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-sm font-medium text-gray-500">ค่าธรรมเนียมรวม</div>
            <div class="text-3xl font-bold text-warning-600 mt-2">฿ {{ number_format($totalFees, 2) }}</div>
        </x-filament::section>
    </div>

    <x-filament::section heading="สถิติตามประเภทผู้เข้าชม">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
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

    <x-filament::section>
        <x-slot name="heading">รายการล่าสุด · {{ $periods[$period] }}</x-slot>
        <x-slot name="description">แสดงรายการที่ชำระแล้วและตรวจตั๋วแล้วตามช่วงเวลาที่เลือก</x-slot>
        <div class="space-y-3">
                    @foreach ($recentOrders as $order)
                        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="rounded-lg bg-primary-100 px-3 py-2 text-sm font-bold text-primary-700">#{{ $order->id }}</div>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ $order->showtime?->movie?->title_th ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at?->format('d/m/Y H:i') }} · {{ $order->quantity }} คน</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="hidden text-gray-500 sm:inline">{{ $order->payment_method === 'counter' ? 'เคาน์เตอร์ POS' : 'ออนไลน์/QR' }}</span>
                                <span class="font-bold text-success-700">฿ {{ number_format((float) ($order->amount_paid ?? $order->total_amount), 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    @if ($recentOrders->isEmpty())
                        <div class="rounded-xl border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">ไม่พบรายการในช่วงเวลานี้</div>
                    @endif
        </div>
    </x-filament::section>
</x-filament-panels::page>
