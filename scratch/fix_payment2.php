<?php
$file = 'resources/views/bookings/payment.blade.php';
$content = file_get_contents($file);

// Find the start of the QR code section
$qrStartPos = strpos($content, '<!--');
// Actually, it's easier to use a targeted preg_replace.

$pattern = '/<!-- .*? QR Code -->.*?<div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">.*?<\/form>\s*<\/div>/s';
// But the Thai comments will break the regex if I don't use the correct encoding.
// Let's just find `<div class="flex justify-center mb-10">` which wraps the QR code.
// And the end of the forms.

$pattern = '/<div class="flex justify-center mb-10">.*?<div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">.*?<\/form>\s*<\/div>/s';

$newForms = <<<HTML
            <div class="bg-blue-50 border border-blue-200 text-blue-800 p-6 rounded-2xl mb-8 max-w-md mx-auto text-center shadow-sm">
                <svg class="w-10 h-10 mx-auto text-blue-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <h3 class="font-bold text-lg mb-2">ขั้นตอนต่อไป</h3>
                <p class="text-sm">
                    กรุณากดยืนยันเพื่อรับ <strong>รหัสตั๋ว (E-Ticket)</strong><br>
                    นำรหัสนี้ไปแสดงที่จุดจำหน่ายตั๋วเพื่อชำระเงิน<br>หรือยืนยันการโอนเงิน (แสดงสลิป) กับเจ้าหน้าที่
                </p>
            </div>

            <div class="flex justify-center max-w-md mx-auto">
                <form method="POST" action="{{ route('bookings.confirm', \$booking->id) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="payment_method" value="counter">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex items-center justify-center gap-2 text-lg">
                        <span>ยืนยันรับรหัสตั๋ว (E-Ticket)</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
            </div>
HTML;

$content = preg_replace($pattern, $newForms, $content);
file_put_contents($file, $content);
echo "Updated payment view successfully.\n";

