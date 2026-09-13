<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
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
}