<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'ยกเลิกการจองที่หมดเวลา 30 นาทีแล้วยังไม่ชำระเงิน และคืนที่นั่ง';

    public function handle(): void
    {
        $expiredBookings = Booking::whereIn('status', ['pending', 'awaiting_payment'])
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                    ->orWhere('payment_method', 'counter');
            })
            ->get();

        foreach ($expiredBookings as $booking) {
            if ($booking->status === 'awaiting_payment' && $booking->payment_method === 'counter' && $booking->showtime) {
                $counterDeadline = $booking->showtime->startsAt()->addMinutes(30);
                if (! $booking->expires_at || ! $booking->expires_at->equalTo($counterDeadline)) {
                    $booking->update(['expires_at' => $counterDeadline]);
                }
            }

            if (! $booking->expires_at || $booking->expires_at->isFuture()) {
                continue;
            }

            if ($booking->expireAndReleaseSeats()) {
                $this->info("ยกเลิกการจอง #{$booking->id} คืนที่นั่ง {$booking->quantity} ที่");
            }
        }

        if ($expiredBookings->isEmpty()) {
            $this->info('ไม่มีการจองที่หมดเวลา');
        }
    }
}
