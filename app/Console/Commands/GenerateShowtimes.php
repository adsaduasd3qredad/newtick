<?php

namespace App\Console\Commands;

use App\Models\Showtime;
use App\Models\WeeklySchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class GenerateShowtimes extends Command
{
    protected $signature = 'showtimes:generate';
    protected $description = 'สร้างรอบฉายจริงเฉพาะภายในเดือนปัจจุบันเท่านั้น';

    public function handle(): void
    {
        $schedules = WeeklySchedule::all();
        $created = 0;

        $startDate = Carbon::today();
        $endOfMonth = Carbon::today()->addMonth()->endOfMonth(); // Create up to next month too so they can test

        $period = CarbonPeriod::create($startDate, $endOfMonth);

        foreach ($period as $date) {
            $dayOfWeek = $date->dayOfWeekIso; // 1 (Mon) - 7 (Sun)

            foreach ($schedules as $schedule) {
                if ((int) $schedule->day_of_week === $dayOfWeek) {
                    $exists = Showtime::where('weekly_schedule_id', $schedule->id)
                        ->whereDate('show_date', $date->toDateString())
                        ->exists();

                    if (! $exists) {
                        $exists = Showtime::whereDate('show_date', $date->toDateString())
                            ->whereTime('show_time', $schedule->show_time)
                            ->where('movie_id', $schedule->movie_id)
                            ->exists();
                    }

                    if (! $exists) {
                        Showtime::create([
                            'weekly_schedule_id' => $schedule->id,
                            'movie_id' => $schedule->movie_id,
                            'show_date' => $date->toDateString(),
                            'show_time' => $schedule->show_time,
                            'total_seats' => $schedule->total_seats,
                            'available_seats' => $schedule->total_seats,
                        ]);
                        $created++;
                    }
                }
            }
        }

        $this->info("สร้างรอบฉายใหม่ภายในเดือนนี้สำเร็จ: {$created} รอบ");
    }
}