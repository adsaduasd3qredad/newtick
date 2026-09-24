<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Showtime;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'showtime_id',
        'booker_name',
        'booker_email',
        'booker_phone',
        'visitor_type',
        'visitor_details',
        'quantity',
        'seats',
        'total_amount',
        'amount_paid',
        'discount_code',
        'notes',
        'status',
        'payment_method',
        'expires_at',
        'qr_payment_ref',
        'qr_ticket_ref',
        'checked_in_at',
    ];

    protected $casts = [
        'seats' => 'array',
        'visitor_details' => 'array',
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /** Expire an unpaid booking and return its seats exactly once. */
    public function expireAndReleaseSeats(): bool
    {
        return DB::transaction(function (): bool {
            $showtimeId = self::whereKey($this->getKey())->value('showtime_id');
            if (! $showtimeId) {
                return false;
            }

            // Match the showtime -> booking lock order used when reserving seats.
            $showtime = Showtime::whereKey($showtimeId)->lockForUpdate()->first();
            $booking = self::whereKey($this->getKey())->lockForUpdate()->first();

            if (! $showtime || ! $booking
                || ! in_array($booking->status, ['pending', 'awaiting_payment'], true)
                || ! $booking->expires_at
                || $booking->expires_at->isFuture()) {
                return false;
            }

            $booking->update(['status' => 'expired']);
            $showtime->increment('available_seats', $booking->quantity);

            return true;
        });
    }

    protected static function booted()
    {
        static::deleting(function ($booking) {
            // Only bookings that still reserve seats may return capacity.
            if ($booking->showtime && in_array($booking->status, ['pending', 'awaiting_payment'], true)) {
                $booking->showtime->increment('available_seats', $booking->quantity);
            }
        });
    }
}
