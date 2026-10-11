<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'ticket_amount', 'transaction_fee', 'method', 'slip_path', 'paid_at',
        'slip_verify_result',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'slip_verify_result' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}