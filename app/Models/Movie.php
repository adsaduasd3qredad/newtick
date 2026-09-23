<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title_th', 'title_en', 'description', 'poster_path', 'duration_minutes', 
        'is_active', 'rating', 'language', 'trailer_url', 'total_seats', 
        'start_date', 'end_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function weeklySchedules()
    {
        return $this->hasMany(WeeklySchedule::class);
    }

    public function scopeActive($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        return $query->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $date);
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $date);
            });
    }

    public function isActiveOn($date = null): bool
    {
        $date = $date ? \Illuminate\Support\Carbon::parse($date)->toDateString() : now()->toDateString();

        return $this->is_active
            && (! $this->start_date || $this->start_date->toDateString() <= $date)
            && (! $this->end_date || $this->end_date->toDateString() >= $date);
    }
}