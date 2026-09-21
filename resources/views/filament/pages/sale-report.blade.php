<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="text-sm font-medium text-gray-500">รายได้วันนี้</div>
            <div class="text-3xl font-bold text-success-600 mt-2">฿ {{ number_format($todayRevenue, 2) }}</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm font-medium text-gray-500">รายได้เดือนนี้</div>
            <div class="text-3xl font-bold text-primary-600 mt-2">฿ {{ number_format($thisMonthRevenue, 2) }}</div>
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

    <x-filament::section heading="รายการออเดอร์ล่าสุด">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="py-3 px-4">เลขที่</th><th class="py-3 px-4">วันที่จอง</th>
                        <th class="py-3 px-4">ภาพยนตร์ / รอบ</th><th class="py-3 px-4 text-right">จำนวน</th>
                        <th class="py-3 px-4 text-right">ยอดเก็บจริง</th><th class="py-3 px-4">วิธีชำระ</th>
                        <th class="py-3 px-4">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-3 px-4 font-medium">#{{ $order->id }}</td>
                            <td class="py-3 px-4">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">{{ $order->showtime?->movie?->title_th ?? '-' }}<br><span class="text-xs text-gray-500">{{ $order->showtime?->show_date?->format('d/m/Y') }} {{ substr((string) ($order->showtime?->show_time ?? ''), 0, 5) }}</span></td>
                            <td class="py-3 px-4 text-right">{{ $order->quantity }}</td>
                            <td class="py-3 px-4 text-right font-medium">฿ {{ number_format((float) ($order->amount_paid ?? $order->total_amount), 2) }}</td>
                            <td class="py-3 px-4">{{ $order->payment_method === 'counter' ? 'เคาน์เตอร์ POS' : 'ออนไลน์/QR' }}</td>
                            <td class="py-3 px-4">{{ $order->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
