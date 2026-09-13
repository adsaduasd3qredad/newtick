<?php
$file = 'resources/views/bookings/confirmed.blade.php';
$content = file_get_contents($file);

$search = "<span>ยอดชำระ: <strong class=\"text-emerald-600 text-sm font-bold\">{{ number_format(\$booking->total_amount, 2) }} บาท</strong></span>";
// Wait, the currency might be `฿` or `บาท` based on my previous cat output. Let me check the exact string.
// It was `<span>ยอดชำระ: <strong class="text-emerald-600 text-sm font-bold">{{ number_format($booking->total_amount, 2) }} บาท</strong></span>` - wait, looking at my cat output:
// `<span>,,-,",, ,: <strong class="text-emerald-600 text-sm font-bold">{{ number_format($booking->total_amount, 2) }} ,</strong></span>`
// It seems it was `บาท` but Thai characters got mangled in PowerShell out.

$pattern = '/<span>.*?<strong class="text-emerald-600 text-sm font-bold">{{ number_format\(\$booking->total_amount, 2\) }}.*?<\/strong><\/span>/';

$replace = <<<HTML
                            <div class="flex items-center gap-2">
                                <span>ยอดชำระ: <strong class="text-emerald-600 text-sm font-bold">{{ number_format(\$booking->total_amount, 2) }} บาท</strong></span>
                                @if(in_array(\$booking->status, ['pending', 'awaiting_payment']))
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[10px] font-bold">รอชำระเงิน</span>
                                @elseif(\$booking->status == 'paid')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">ชำระแล้ว</span>
                                @endif
                            </div>
HTML;

$content = preg_replace($pattern, $replace, $content);
file_put_contents($file, $content);
echo "Added status badge to ticket.\n";

