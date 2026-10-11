<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'booking_id',
        'order_reference',
        'amount',
        'payment_method',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Helper สำหรับบันทึก Log การทำงานอย่างสะดวกรวดเร็ว
     */
    public static function log(
        string $action,
        string $description,
        ?Booking $booking = null,
        ?float $amount = null,
        ?string $paymentMethod = null,
        ?array $properties = null
    ): self {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'ระบบอัตโนมัติ / ลูกค้า',
            'user_role' => $user?->role ?? 'guest',
            'action' => $action,
            'booking_id' => $booking?->id,
            'order_reference' => $booking ? ('#' . str_pad((string) $booking->id, 2, '0', STR_PAD_LEFT)) : null,
            'amount' => $amount ?? ($booking ? (float) ($booking->amount_paid ?: $booking->total_amount) : null),
            'payment_method' => $paymentMethod ?? $booking?->payment_method,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);
    }
}

