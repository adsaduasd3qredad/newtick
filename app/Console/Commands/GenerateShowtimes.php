<?php

namespace App\Console\Commands;

use App\Models\Showtime;
use App\Models\WeeklySchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateShowtimes extends Command
{
    protected $signature = 'showtimes:generate {--weeks=8}';
    protected $description = 'สร้างรอบฉายจริงล่วงหน้าตามตารางแม่แบบประจำสัปดาห์';

    public function handle(): void
    {
        $weeksAhead = (int) $this->option('weeks');
        $schedules = WeeklySchedule::all();
        $created = 0;

        for ($w = 0; $w < $weeksAhead; $w++) {
            $weekStart = now()->addWeeks($w)->startOfWeek(Carbon::SUNDAY);

            foreach ($schedules as $schedule) {
                $date = $weekStart->copy()->addDays($schedule->day_of_week);

                if ($date->isPast() && !$date->isToday()) {
                    continue;
                }

                $exists = Showtime::where('weekly_schedule_id', $schedule->id)
                    ->whereDate('show_date', $date->toDateString())
                    ->exists();

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

        $this->info("สร้างรอบฉายใหม่ {$created} รอบ");
    }
}