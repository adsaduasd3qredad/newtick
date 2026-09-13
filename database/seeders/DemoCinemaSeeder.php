<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\WeeklySchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Carbon\CarbonPeriod;

class DemoCinemaSeeder extends Seeder
{
    public function run(): void
    {
        $movies = [
            ['title_th' => 'ดาวเหนือ คู่หูตะลุยอวกาศ', 'title_en' => 'Polaris', 'poster' => 'polaris.jpg', 'day' => 1],
            ['title_th' => 'ความลับของแรงโน้มถ่วง', 'title_en' => 'The Secrets of Gravity', 'poster' => 'gravity.jpg', 'day' => 2],
            ['title_th' => 'จากโลกสู่จักรวาล', 'title_en' => 'From Earth to the Universe', 'poster' => 'earthuniverse.jpg', 'day' => 3],
            ['title_th' => 'ผจญภัยนอกโลก ท่องสู่ระบบสุริยะ', 'title_en' => 'Life', 'poster' => 'life.jpg', 'day' => 4],
            ['title_th' => 'บรรยายดาว', 'title_en' => 'Star Lecture', 'poster' => 'star.png', 'day' => 5],
            ['title_th' => 'ท่องโลกเหนือดวงดาว', 'title_en' => 'To Worlds Beyond', 'poster' => 'worldbeyon.jpg', 'day' => 6],
            ['title_th' => 'เต้นรำท่ามกลางหมู่ดาว', 'title_en' => 'Dancing Among the Stars', 'poster' => 'dancing.jpg', 'day' => 7],
            ['title_th' => 'โคโคมง ผจญภัยในอวกาศ', 'title_en' => 'Cocomong Space Adventure', 'poster' => 'oddy.jpg', 'day' => 1],
            ['title_th' => 'คอสมอส โอดิสซีย์', 'title_en' => 'Cosmos Odyssey', 'poster' => 'cosmos.jpg', 'day' => 3],
            ['title_th' => 'ลูเซีย กับความลับของดาวตก', 'title_en' => 'Lucia, The Secret of Shooting Stars', 'poster' => 'lucia.jpg', 'day' => 5],
        ];

        $times = ['10:00:00', '13:00:00', '15:00:00'];
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);

        foreach ($movies as $data) {
            $source = public_path('images/' . $data['poster']);
            $posterPath = 'movies/posters/' . $data['poster'];

            if (is_file($source) && ! Storage::disk('public')->exists($posterPath)) {
                Storage::disk('public')->put($posterPath, file_get_contents($source));
            }

            $movie = Movie::updateOrCreate(
                ['title_en' => $data['title_en']],
                [
                    'title_th' => $data['title_th'],
                    'description' => 'ภาพยนตร์ดิจิทัลสำหรับการเรียนรู้เรื่องดาราศาสตร์และอวกาศ',
                    'poster_path' => $posterPath,
                    'duration_minutes' => 40,
                    'is_active' => true,
                    'rating' => 'G',
                    'language' => 'TH/TH',
                    'total_seats' => 160,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                ]
            );

            foreach ($times as $timeIndex => $time) {
                $schedule = WeeklySchedule::updateOrCreate(
                    [
                        'movie_id' => $movie->id,
                        'day_of_week' => $data['day'],
                        'show_time' => $time,
                    ],
                    ['total_seats' => 160]
                );

                foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                    if ($date->dayOfWeekIso !== $data['day']) {
                        continue;
                    }

                    Showtime::firstOrCreate(
                        [
                            'movie_id' => $movie->id,
                            'show_date' => $date->toDateString(),
                            'show_time' => $time,
                        ],
                        [
                            'weekly_schedule_id' => $schedule->id,
                            'total_seats' => 160,
                            'available_seats' => 160,
                        ]
                    );
                }
            }
        }
    }
}
