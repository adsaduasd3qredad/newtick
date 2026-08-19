<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    protected $fillable = [
        'day_of_week',
        'show_time',
        'movie_id',
        'total_seats',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}