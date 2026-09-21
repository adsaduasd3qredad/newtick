<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Showtime extends Model
{
    private const BUSINESS_TIMEZONE = 'Asia/Bangkok';

    protected $fillable = [
        'weekly_schedule_id',
        'movie_id',
        'show_date',
        'show_time',
        'total_seats',
        'available_seats',
    ];

    protected $casts = [
        'show_date' => 'date',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function weeklySchedule()
    {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function startsAt(): Carbon
    {
        $showDate = $this->show_date instanceof Carbon
            ? $this->show_date->toDateString()
            : (string) $this->show_date;
        $showTime = substr((string) $this->show_time, 0, 8);

        return Carbon::parse($showDate . ' ' . $showTime, self::BUSINESS_TIMEZONE);
    }

    public function isBookable(): bool
    {
        return Carbon::now(self::BUSINESS_TIMEZONE)->lt($this->startsAt());
    }
}
