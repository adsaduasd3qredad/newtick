<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'ยกเลิกการจองที่หมดเวลา 30 นาทีแล้วยังไม่ชำระเงิน และคืนที่นั่ง';

    public function handle(): void
    {
        $expiredBookings = Booking::whereIn('status', ['pending', 'awaiting_payment'])
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                // ล็อกป้องกันชนกับ booking ใหม่ที่กำลังตัดที่นั่งพร้อมกัน
                $showtime = $booking->showtime()->lockForUpdate()->first();
                $showtime->increment('available_seats', $booking->quantity);

                $booking->update(['status' => 'expired']);
            });

            $this->info("ยกเลิกการจอง #{$booking->id} คืนที่นั่ง {$booking->quantity} ที่");
        }

        if ($expiredBookings->isEmpty()) {
            $this->info('ไม่มีการจองที่หมดเวลา');
        }
    }
}