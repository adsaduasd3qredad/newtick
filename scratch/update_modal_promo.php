<?php
// 1. Update resources/views/pos/index.blade.php
$fileIndex = 'resources/views/pos/index.blade.php';
$contentIndex = file_get_contents($fileIndex);

$searchTarget = '<!-- Total Amount Summary -->';
$promoHtml = <<<'HTML'
<!-- Promotion & Free Entries (Elderly / Children <= 100cm) -->
                <div class="bg-amber-50/70 p-3.5 rounded-2xl border border-amber-200 text-xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-amber-900 flex items-center gap-1.5">
                            <span>🎁</span>
                            <span>โปรโมชั่นเข้าชมฟรี & หมายเหตุ</span>
                        </span>
                        <span class="text-[10px] text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-md font-semibold">บันทึกลง Report</span>
                    </div>

                    <!-- Free attendees buttons -->
                    <div class="grid grid-cols-2 gap-2 mb-2.5">
                        <div class="bg-white p-2 rounded-xl border border-amber-200 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-700">👴 ผู้สูงอายุ (ฟรี)</span>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="stepFree('elderly', -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 flex items-center justify-center">-</button>
                                <span id="free_elderly_display" class="w-5 text-center font-bold text-slate-800 text-xs">0</span>
                                <button type="button" onclick="stepFree('elderly', 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 flex items-center justify-center">+</button>
                            </div>
                            <input type="hidden" name="free_elderly" id="free_elderly_input" value="0">
                        </div>

                        <div class="bg-white p-2 rounded-xl border border-amber-200 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-700">👶 เด็ก ≤100 ซม. (ฟรี)</span>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="stepFree('children', -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 flex items-center justify-center">-</button>
                                <span id="free_children_display" class="w-5 text-center font-bold text-slate-800 text-xs">0</span>
                                <button type="button" onclick="stepFree('children', 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 flex items-center justify-center">+</button>
                            </div>
                            <input type="hidden" name="free_children" id="free_children_input" value="0">
                        </div>
                    </div>

                    <!-- Custom Notes input -->
                    <div>
                        <input type="text" name="notes" id="modal_notes" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี เช่น หน่วยงาน, บัตรพิเศษ)"
                            class="w-full bg-white border border-amber-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                    </div>
                </div>

                <!-- Total Amount Summary -->
HTML;

if (strpos($contentIndex, '<!-- Promotion & Free Entries') === false) {
    $contentIndex = str_replace($searchTarget, $promoHtml, $contentIndex);
}

// Update JS functions in pos/index.blade.php
$oldUpdateModalTotal = <<<'JS'
        function updateModalTotal() {
            const qty = parseInt(quantityInput.value) || 0;
            const total = qty * pricePerSeat;
            totalAmountEl.textContent = total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ฿';
        }
JS;

$newUpdateModalTotal = <<<'JS'
        function updateModalTotal() {
            const qty = parseInt(quantityInput.value) || 0;
            const elderly = parseInt(document.getElementById('free_elderly_input')?.value) || 0;
            const children = parseInt(document.getElementById('free_children_input')?.value) || 0;
            const totalFree = Math.min(qty, elderly + children);
            const payingQty = Math.max(0, qty - totalFree);

            const total = payingQty * pricePerSeat;
            let summaryText = total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ฿';
            if (totalFree > 0) {
                summaryText += ` <span class="text-xs font-normal text-emerald-600 block">(ชำระ ${payingQty}, ฟรี ${totalFree})</span>`;
            }
            totalAmountEl.innerHTML = summaryText;
        }

        function stepFree(type, delta) {
            const elderlyInput = document.getElementById('free_elderly_input');
            const elderlyDisplay = document.getElementById('free_elderly_display');
            const childrenInput = document.getElementById('free_children_input');
            const childrenDisplay = document.getElementById('free_children_display');

            let elderly = parseInt(elderlyInput.value) || 0;
            let children = parseInt(childrenInput.value) || 0;
            let qty = parseInt(quantityInput.value) || 1;

            if (type === 'elderly') {
                elderly = Math.max(0, elderly + delta);
            } else if (type === 'children') {
                children = Math.max(0, children + delta);
            }

            if (elderly + children > qty) {
                qty = elderly + children;
                if (qty <= currentMaxSeats) {
                    quantityInput.value = qty;
                } else {
                    if (type === 'elderly') elderly = Math.max(0, currentMaxSeats - children);
                    if (type === 'children') children = Math.max(0, currentMaxSeats - elderly);
                    qty = currentMaxSeats;
                    quantityInput.value = qty;
                }
            }

            elderlyInput.value = elderly;
            elderlyDisplay.textContent = elderly;
            childrenInput.value = children;
            childrenDisplay.textContent = children;

            updateModalTotal();
        }
JS;

if (strpos($contentIndex, $oldUpdateModalTotal) !== false) {
    $contentIndex = str_replace($oldUpdateModalTotal, $newUpdateModalTotal, $contentIndex);
}

// Reset promo inputs in openQuickSellModal
$oldReset = "quantityInput.value = 1;";
$newReset = <<<'JS'
quantityInput.value = 1;
            if (document.getElementById('free_elderly_input')) {
                document.getElementById('free_elderly_input').value = '0';
                document.getElementById('free_elderly_display').textContent = '0';
            }
            if (document.getElementById('free_children_input')) {
                document.getElementById('free_children_input').value = '0';
                document.getElementById('free_children_display').textContent = '0';
            }
            if (document.getElementById('modal_notes')) {
                document.getElementById('modal_notes').value = '';
            }
JS;

if (strpos($contentIndex, "free_elderly_input')") === false) {
    $contentIndex = str_replace($oldReset, $newReset, $contentIndex);
}

file_put_contents($fileIndex, $contentIndex);
echo "SUCCESS: Updated pos/index.blade.php\n";

// 2. Update pos/receipt.blade.php to show discount and notes neatly
$fileReceipt = 'resources/views/pos/receipt.blade.php';
$contentReceipt = file_get_contents($fileReceipt);

$oldBreakdown = <<<'BLADE'
            @if ($booking->payment?->transaction_fee)
                <div class="flex justify-between">
                    <span>ค่าธรรมเนียม:</span>
                    <span>฿ {{ number_format($booking->payment->transaction_fee, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-sm pt-1 border-t border-slate-200">
                <span>ยอดชำระสุทธิ:</span>
                <span class="font-display">฿ {{ number_format($booking->amount_paid ?? $booking->total_amount, 2) }}</span>
            </div>
            @if ($booking->notes)
                <div class="pt-1 text-[10px] text-slate-600">
                    หมายเหตุ: {{ $booking->notes }}
                </div>
            @endif
BLADE;

$newBreakdown = <<<'BLADE'
            @if ($booking->payment?->transaction_fee)
                <div class="flex justify-between">
                    <span>ค่าธรรมเนียม:</span>
                    <span>฿ {{ number_format($booking->payment->transaction_fee, 2) }}</span>
                </div>
            @endif
            @if ($booking->total_amount > (($booking->amount_paid ?? 0) - ($booking->payment?->transaction_fee ?? 0)))
                @php($discount = max(0, $booking->total_amount - (($booking->amount_paid ?? 0) - ($booking->payment?->transaction_fee ?? 0))))
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>ส่วนลดโปรโมชั่น (เข้าชมฟรี):</span>
                    <span>- ฿ {{ number_format($discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-sm pt-1 border-t border-slate-200">
                <span>ยอดชำระสุทธิ:</span>
                <span class="font-display">฿ {{ number_format($booking->amount_paid ?? $booking->total_amount, 2) }}</span>
            </div>
            @if ($booking->notes)
                <div class="pt-1.5 text-[10px] text-slate-700 bg-amber-50 p-1.5 rounded border border-amber-200 font-medium">
                    <strong>หมายเหตุ:</strong> {{ $booking->notes }}
                </div>
            @endif
BLADE;

if (strpos($contentReceipt, $oldBreakdown) !== false) {
    $contentReceipt = str_replace($oldBreakdown, $newBreakdown, $contentReceipt);
    file_put_contents($fileReceipt, $contentReceipt);
    echo "SUCCESS: Updated pos/receipt.blade.php\n";
}

exec('php -l ' . escapeshellarg($fileIndex), $o1, $r1);
echo "pos/index: " . implode(" ", $o1) . "\n";
exec('php -l ' . escapeshellarg($fileReceipt), $o2, $r2);
echo "pos/receipt: " . implode(" ", $o2) . "\n";
