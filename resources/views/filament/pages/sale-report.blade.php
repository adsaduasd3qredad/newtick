<x-filament-panels::page>
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sale Report</h2>
                <p class="mt-1 text-sm text-gray-500">ตารางรายการขายตามช่วงเวลาที่เลือก</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($periods as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['period' => $key, 'date' => $selectedDate->toDateString()]) }}"
                        class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ $period === $key ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/50">
            <input type="hidden" name="period" value="{{ $period }}">
            <div>
                <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">
                    {{ $period === 'weekly' ? 'วันที่สิ้นสุด 7 วัน' : ($period === 'monthly' ? 'เดือนที่รายงาน' : 'ปีที่รายงาน') }}
                </label>
                @if ($period === 'weekly')
                    <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                @elseif ($period === 'monthly')
                    <input type="month" name="date" value="{{ $selectedDate->format('Y-m') }}" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                @else
                    <input type="number" name="date" value="{{ $selectedDate->year }}" min="2000" max="2100" class="w-28 rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                @endif
            </div>
            <button class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">แสดงรายงาน</button>
            <div class="ml-auto text-right text-xs text-gray-500">
                <div>ช่วงข้อมูล</div>
                <strong class="text-gray-700 dark:text-gray-200">{{ $periodStart->format('d/m/Y') }} - {{ $periodEnd->format('d/m/Y') }}</strong>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1250px] text-left text-sm">
                <thead class="bg-gray-900 text-xs text-white dark:bg-gray-950">
                    <tr>
                        <th colspan="4" class="border-r border-gray-700 px-4 py-3">ข้อมูลการเข้าชม</th>
                        <th colspan="5" class="border-r border-gray-700 px-4 py-3">รายการเงิน</th>
                        <th colspan="3" class="px-4 py-3">การชำระเงินและหมายเหตุ</th>
                    </tr>
                    <tr class="bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        <th class="whitespace-nowrap px-4 py-3">โอนเงินวันที่</th>
                        <th class="whitespace-nowrap px-4 py-3">วันที่ชม</th>
                        <th class="whitespace-nowrap px-4 py-3">รอบ</th>
                        <th class="whitespace-nowrap px-4 py-3">เลขที่การจอง</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">จำนวนคน</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">จำนวนเงิน</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">ค่าธรรมเนียม</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">ส่วนลด</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right">เก็บจริง</th>
                        <th class="whitespace-nowrap px-4 py-3">วิธีชำระ</th>
                        <th class="whitespace-nowrap px-4 py-3">เวลาที่โอน</th>
                        <th class="min-w-[220px] px-4 py-3">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($sales as $sale)
                        @php
                            $total = (float) $sale->total_amount;
                            $collected = (float) ($sale->amount_paid ?? $sale->total_amount);
                            $fee = (float) ($sale->payment?->transaction_fee ?? 0);
                        @endphp
                        <tr class="hover:bg-primary-50/50 dark:hover:bg-gray-800">
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">{{ $sale->payment?->paid_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $sale->showtime?->show_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium">{{ $sale->showtime?->show_time ? substr((string) $sale->showtime->show_time, 0, 5) . ' น.' : '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-bold text-primary-700">#{{ str_pad((string) $sale->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($sale->quantity) }}</td>
                            <td class="px-4 py-3 text-right">฿ {{ number_format($total, 2) }}</td>
                            <td class="px-4 py-3 text-right text-amber-700">฿ {{ number_format($fee, 2) }}</td>
                            <td class="px-4 py-3 text-right text-rose-700">฿ {{ number_format(max(0, $total - $collected), 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-700">฿ {{ number_format($collected, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $sale->payment_method === 'counter' ? 'POS' : 'ออนไลน์/QR' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $sale->payment?->paid_at?->format('H:i') ?? '-' }}</td>
                            <td class="max-w-[280px] px-4 py-3 text-gray-600 dark:text-gray-300">{{ $sale->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="px-4 py-16 text-center text-gray-500">ไม่พบรายการขายในช่วงเวลานี้</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 bg-gray-50 px-5 py-3 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800/50">
            <span>แสดง {{ $sales->count() }} รายการในช่วงเวลาที่เลือก</span>
            <span>อัปเดตข้อมูลล่าสุด {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</x-filament-panels::page>
