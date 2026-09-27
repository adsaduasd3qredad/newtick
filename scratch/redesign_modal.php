<?php
$file = 'resources/views/pos/index.blade.php';
$content = file_get_contents($file);

// 1. Remove emoji from showtime card button
$oldCardBtn = '<span>⚡ ขายด่วน (Walk-in)</span>';
$newCardBtn = '<span>ขายด่วน (Walk-in)</span>';
$content = str_replace($oldCardBtn, $newCardBtn, $content);

// 2. Replace the modal markup
$modalStart = '    <!-- Quick Sell Modal for Walk-in Customers -->';
$modalEnd = '    <!-- Modal Scripts -->';

$posStart = strpos($content, $modalStart);
$posEnd = strpos($content, $modalEnd);

if ($posStart === false || $posEnd === false) {
    echo "Could not find modal markers in pos/index.blade.php\n";
    exit(1);
}

$before = substr($content, 0, $posStart);
$after = substr($content, $posEnd);

$newModal = <<<'HTML'
    <!-- Quick Sell Modal for Walk-in Customers -->
    <div id="quickSellModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-xl text-slate-900 leading-tight">ขายตั๋วหน้าร้าน (Walk-in)</h3>
                        <p class="text-xs text-slate-500">ออกตั๋วและจัดสรรที่นั่งให้อัตโนมัติ</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickSellModal()" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center text-lg font-bold transition">
                    ✕
                </button>
            </div>

            <form action="{{ route('pos.quick-sell') }}" method="POST" class="mt-5 space-y-5">
                @csrf
                <input type="hidden" name="showtime_id" id="modal_showtime_id">

                <!-- Movie Info in modal -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <p class="font-bold text-slate-900 text-base font-display" id="modal_movie_title">-</p>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 mt-1.5">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>รอบเวลา: <strong class="text-cyan-800 font-bold" id="modal_show_time">-</strong> น.</span>
                        </span>
                        <span>•</span>
                        <span>ที่นั่งว่าง: <strong class="text-emerald-700 font-bold" id="modal_avail_seats">-</strong> ที่นั่ง</span>
                    </div>
                </div>

                <!-- Quantity Quick Selector -->
                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-2">จำนวนตั๋วทั้งหมด (ที่นั่ง)</label>
                    <div class="grid grid-cols-6 gap-2 mb-2.5">
                        @foreach([1, 2, 3, 4, 5, 10] as $q)
                            <button type="button" onclick="setQuantity(Number(this.textContent.trim()))"
                                class="h-11 text-sm font-bold rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-800 transition">
                                {{ $q }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="stepQuantity(-1)" class="w-12 h-12 rounded-xl border border-slate-300 hover:bg-slate-100 font-bold text-slate-800 flex items-center justify-center text-xl transition">-</button>
                        <input type="number" name="quantity" id="modal_quantity" min="1" max="160" value="1" required
                            oninput="updateModalTotal()"
                            class="flex-1 h-12 text-center bg-white border border-slate-300 rounded-xl px-4 text-xl text-slate-900 font-bold focus:ring-2 focus:ring-emerald-500">
                        <button type="button" onclick="stepQuantity(1)" class="w-12 h-12 rounded-xl border border-slate-300 hover:bg-slate-100 font-bold text-slate-800 flex items-center justify-center text-xl transition">+</button>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-semibold text-slate-800 mb-2">ช่องทางชำระเงิน</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border-2 border-emerald-400 bg-emerald-50/40 cursor-pointer hover:border-emerald-500 transition">
                            <input type="radio" name="payment_method" value="counter" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">เงินสด (Cash)</span>
                                <span class="text-xs text-slate-500">ไม่มีค่าธรรมเนียม</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border-2 border-slate-200 bg-white cursor-pointer hover:border-slate-300 transition">
                            <input type="radio" name="payment_method" value="qr_code" class="w-4 h-4 text-cyan-600 focus:ring-cyan-500">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">สแกน QR PromptPay</span>
                                <span class="text-xs text-slate-500">+{{ config('ticketing.qr_payment_fee', 10) }} บาท ค่าธรรมเนียม</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Promotion & Free Entries (Elderly / Children <= 100cm) -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-800">โปรโมชั่นเข้าชมฟรี</span>
                        <span class="text-xs text-slate-500">บันทึกลงรายงานอัตโนมัติ</span>
                    </div>

                    <div class="space-y-2">
                        <!-- Elderly Row -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-slate-800 block">ผู้สูงอายุ (เข้าชมฟรี)</span>
                                <span class="text-xs text-slate-400">สำหรับผู้สูงอายุ 60 ปีขึ้นไป</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="stepFree('elderly', -1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-sm font-bold text-slate-700 flex items-center justify-center transition">-</button>
                                <span id="free_elderly_display" class="w-8 text-center font-bold text-slate-900 text-sm">0</span>
                                <button type="button" onclick="stepFree('elderly', 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-sm font-bold text-slate-700 flex items-center justify-center transition">+</button>
                            </div>
                            <input type="hidden" name="free_elderly" id="free_elderly_input" value="0">
                        </div>

                        <!-- Children Row -->
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-slate-800 block">เด็กส่วนสูงไม่เกิน 100 ซม. (เข้าชมฟรี)</span>
                                <span class="text-xs text-slate-400">เด็กเล็กส่วนสูงไม่เกินเกณฑ์</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="stepFree('children', -1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-sm font-bold text-slate-700 flex items-center justify-center transition">-</button>
                                <span id="free_children_display" class="w-8 text-center font-bold text-slate-900 text-sm">0</span>
                                <button type="button" onclick="stepFree('children', 1)" class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-sm font-bold text-slate-700 flex items-center justify-center transition">+</button>
                            </div>
                            <input type="hidden" name="free_children" id="free_children_input" value="0">
                        </div>
                    </div>
                </div>

                <!-- Notes & Customer Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">ชื่อผู้จอง (เว้นว่างได้)</label>
                        <input type="text" name="booker_name" placeholder="Walk-in (รันเลขอัตโนมัติ)"
                            class="w-full h-11 bg-white border border-slate-300 rounded-xl px-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">หมายเหตุเพิ่มเติม</label>
                        <input type="text" name="notes" id="modal_notes" placeholder="เช่น หน่วยงาน, บัตรพิเศษ"
                            class="w-full h-11 bg-white border border-slate-300 rounded-xl px-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Total Amount Summary -->
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-emerald-800 font-semibold block">ยอดชำระสุทธิ</span>
                        <span class="text-xs text-emerald-600">ที่นั่งละ {{ number_format(config('ticketing.price_per_seat', 50), 2) }} ฿</span>
                    </div>
                    <div class="text-right">
                        <span class="font-display font-bold text-3xl text-emerald-700" id="modal_total_amount">{{ number_format(config('ticketing.price_per_seat', 50), 2) }} ฿</span>
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit"
                    class="w-full h-14 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 rounded-2xl shadow-md hover:shadow-lg transition duration-200 text-base font-display flex items-center justify-center gap-2.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>บันทึกการขายและพิมพ์ใบเสร็จ</span>
                </button>
            </form>
        </div>
    </div>

HTML;

$content = $before . $newModal . $after;
file_put_contents($file, $content);
echo "SUCCESS: Updated pos/index.blade.php with clean UI without emojis\n";

exec('php -l ' . escapeshellarg($file), $output, $returnCode);
echo implode("\n", $output) . "\n";
if ($returnCode !== 0) exit(1);
