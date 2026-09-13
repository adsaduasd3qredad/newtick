<?php
$file = 'app/Http/Controllers/PosController.php';
$content = file_get_contents($file);

// Fix verifyBooking
$newVerify = <<<PHP
    public function verifyBooking(Request \$request)
    {
        \$ref = \$request->input('ref');
        \$cleanId = ltrim(\$ref, '#');
        \$booking = Booking::with('showtime.movie')
            ->where('qr_payment_ref', \$ref)
            ->orWhere('qr_ticket_ref', \$ref)
            ->orWhere('id', is_numeric(\$cleanId) ? (int)\$cleanId : 0)
            ->first();

        if (!\$booking) {
            return redirect()->route('pos.scan')->withErrors(['ref' => 'ไม่พบข้อมูลการจองรหัสนี้']);
        }

        \$qrPayload = \App\Services\PromptPayQr::generatePayload(
            env('PROMPTPAY_TARGET', '0800000000'),
            (float) \$booking->total_amount
        );

        return view('pos.verify', compact('booking', 'qrPayload'));
    }
PHP;
$content = preg_replace('/public function verifyBooking\s*\([^\{]+\{.*?(?=public function confirmCheckin)/s', $newVerify . "\n\n    ", $content);

// Fix confirmCheckin
$newConfirm = <<<PHP
    public function confirmCheckin(Request \$request, Booking \$booking)
    {
        if (in_array(\$booking->status, ['pending', 'awaiting_payment'])) {
            \$paymentMethod = \$request->input('payment_method', 'cash');
            \$booking->update([
                'payment_method' => \$paymentMethod,
                'status' => 'redeemed',
                'checked_in_at' => now(),
            ]);
            return redirect()->route('pos.receipt', \$booking->id)->with('success', 'ชำระเงินและออกตั๋วเรียบร้อยแล้ว');
        }

        if (\$booking->status === 'paid' || \$booking->status === 'redeemed') {
            \$booking->update([
                'status' => 'redeemed',
                'checked_in_at' => now()
            ]);
            return redirect()->route('pos.receipt', \$booking->id)->with('success', 'ออกตั๋วเรียบร้อยแล้ว');
        }
        
        return back()->withErrors(['error' => 'สถานะไม่ถูกต้อง ไม่สามารถดำเนินการได้']);
    }
PHP;
$content = preg_replace('/public function confirmCheckin\s*\([^\{]+\{.*?(?=public function orders)/s', $newConfirm . "\n\n    ", $content);

file_put_contents($file, $content);
echo "Updated PosController.\n";

