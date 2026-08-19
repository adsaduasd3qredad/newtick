<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title_th', 'title_en', 'description', 'poster_path', 'duration_minutes',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}