<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'discount_code',
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

    protected static function booted()
    {
        static::deleting(function ($booking) {
            // เมื่อมีการลบ booking ให้บวกที่นั่งคืนในรอบฉายนั้นๆ
            if ($booking->showtime) {
                $booking->showtime->increment('available_seats', $booking->quantity);
            }
        });
    }
}
