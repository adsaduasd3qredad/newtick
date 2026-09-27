<?php
$file = 'app/Http/Controllers/PosController.php';
$content = file_get_contents($file);

$searchStart = 'public function quickSell(Request $request)';
$searchEnd = 'public function scan()';

$pos1 = strpos($content, $searchStart);
$pos2 = strpos($content, $searchEnd);

if ($pos1 === false || $pos2 === false) {
    echo "Could not find quickSell boundaries in PosController.php\n";
    exit(1);
}

$before = substr($content, 0, $pos1);
$after = substr($content, $pos2);

$newQuickSell = 'public function quickSell(Request $request)
    {
        $validated = $request->validate([
            \'showtime_id\' => \'required|exists:showtimes,id\',
            \'quantity\' => \'required|integer|min:1|max:160\',
            \'payment_method\' => \'required|in:counter,qr_code\',
            \'booker_name\' => \'nullable|string|max:255\',
            \'booker_phone\' => \'nullable|string|max:20\',
            \'visitor_type\' => \'nullable|in:individual,school,government,company\',
            \'free_elderly\' => \'nullable|integer|min:0\',
            \'free_children\' => \'nullable|integer|min:0\',
            \'notes\' => \'nullable|string|max:1000\',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $showtime = Showtime::where(\'id\', $validated[\'showtime_id\'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $showtime->isBookable()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    \'showtime\' => \'รอบฉายเริ่มแล้ว ไม่สามารถขายตั๋วได้\',
                ]);
            }

            $qty = (int) $validated[\'quantity\'];

            if ($showtime->available_seats < $qty) {
                return back()->withErrors([\'quantity\' => "ที่นั่งไม่พอ เหลือ {$showtime->available_seats} ที่นั่ง"]);
            }

            // คำนวณจำนวนตั๋วฟรีและยอดเงิน
            $freeElderly = max(0, (int) ($request->input(\'free_elderly\') ?? 0));
            $freeChildren = max(0, (int) ($request->input(\'free_children\') ?? 0));
            $totalFree = min($qty, $freeElderly + $freeChildren);
            $payingQty = max(0, $qty - $totalFree);

            // รวมข้อความโปรโมชั่นและหมายเหตุ
            $noteParts = [];
            if ($freeElderly > 0) {
                $noteParts[] = "ผู้สูงอายุเข้าชมฟรี {$freeElderly} คน";
            }
            if ($freeChildren > 0) {
                $noteParts[] = "เด็กสูงไม่เกิน 100 ซม. เข้าชมฟรี {$freeChildren} คน";
            }
            $customNote = !empty($validated[\'notes\']) ? trim($validated[\'notes\']) : \'\';

            if (!empty($noteParts)) {
                $promoNote = implode(\', \', $noteParts);
                $finalNotes = $customNote ? ($promoNote . \' (\' . $customNote . \')\') : $promoNote;
            } else {
                $finalNotes = $customNote ?: null;
            }

            // หาที่นั่งที่ถูกจองไปแล้ว
            $bookedSeats = Booking::where(\'showtime_id\', $showtime->id)
                ->whereIn(\'status\', [\'pending\', \'awaiting_payment\', \'paid\', \'redeemed\'])
                ->pluck(\'seats\')
                ->filter()
                ->flatten()
                ->all();

            // รายการที่นั่งทั้งหมด 160 ที่นั่ง (แถว A - I)
            $allSeats = [];
            $rows = [
                \'A\' => 16, \'B\' => 16, \'C\' => 18, \'D\' => 18,
                \'E\' => 18, \'F\' => 18, \'G\' => 18, \'H\' => 20, \'I\' => 20
            ];
            foreach ($rows as $rowLetter => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $allSeats[] = $rowLetter . $i;
                }
            }

            // เลือกที่นั่งว่างลำดับแรกๆ
            $availableSeatsList = array_values(array_diff($allSeats, $bookedSeats));
            $assignedSeats = array_slice($availableSeatsList, 0, $qty);

            // ตัดจำนวนที่นั่งว่าง
            $showtime->decrement(\'available_seats\', $qty);

            $fee = $payingQty > 0 ? $this->paymentFee($validated[\'payment_method\']) : 0;
            $totalAmount = $this->pricePerSeat() * $qty;
            $amountPaid = ($this->pricePerSeat() * $payingQty) + $fee;

            $bookerName = !empty($validated[\'booker_name\']) ? trim($validated[\'booker_name\']) : $this->getNextWalkinName();
            $visitorType = $validated[\'visitor_type\'] ?? \'individual\';

            $booking = Booking::create([
                \'showtime_id\' => $showtime->id,
                \'booker_name\' => $bookerName,
                \'booker_email\' => \'pos@sci-rangsit.local\',
                \'booker_phone\' => $validated[\'booker_phone\'] ?? null,
                \'visitor_type\' => $visitorType,
                \'quantity\' => $qty,
                \'seats\' => $assignedSeats,
                \'total_amount\' => $totalAmount,
                \'amount_paid\' => $amountPaid,
                \'notes\' => $finalNotes,
                \'status\' => \'paid\',
                \'payment_method\' => $validated[\'payment_method\'],
                \'qr_ticket_ref\' => (string) Str::uuid(),
            ]);

            $booking->payment()->create([
                \'ticket_amount\' => $booking->total_amount,
                \'transaction_fee\' => $fee,
                \'method\' => $validated[\'payment_method\'],
                \'paid_at\' => now(),
            ]);

            return redirect()->route(\'pos.receipt\', $booking->id);
        });
    }

    ';

$content = $before . $newQuickSell . $after;
file_put_contents($file, $content);
echo "SUCCESS: Updated PosController.php\n";

exec('php -l ' . escapeshellarg($file), $out, $ret);
echo implode("\n", $out) . "\n";
if ($ret !== 0) exit(1);
