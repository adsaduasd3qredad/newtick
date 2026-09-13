<?php

namespace App\Filament\Resources\WeeklySchedules\Pages;

use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use App\Models\WeeklySchedule;
use App\Models\Showtime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ListWeeklySchedules extends ListRecords
{
    protected static string $resource = WeeklyScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateShowtimes')
                ->label('สร้างรอบฉายอัตโนมัติ')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->modalHeading('สร้างรอบฉายอัตโนมัติจากแม่แบบ (Weekly Schedules)')
                ->modalDescription('ระบบจะดึงโครงสร้างเวลาที่เซ็ตไว้ในแม่แบบประจำสัปดาห์ มาสร้างเป็นรอบฉายจริง ในช่วงเวลาที่คุณเลือก (รองรับการข้ามวันจันทร์อัตโนมัติ)')
                ->modalSubmitActionLabel('เริ่มสร้างรอบฉาย')
                ->form([
                    Select::make('range_type')
                        ->label('เลือกระยะเวลาที่จะสร้างล่วงหน้า')
                        ->options([
                            '7_days' => '7 วันข้างหน้า (Next 7 Days)',
                            '14_days' => '14 วันข้างหน้า (Next 14 Days)',
                            'end_of_month' => 'จนถึงสิ้นเดือนนี้ (End of this month)',
                            'next_month' => 'ตลอดเดือนหน้า (Full Next Month)',
                            'custom' => 'กำหนดช่วงเวลาเอง (Custom Range)',
                        ])
                        ->default('14_days')
                        ->reactive()
                        ->required(),

                    DatePicker::make('custom_start')
                        ->label('วันที่เริ่มต้น')
                        ->default(now()->toDateString())
                        ->visible(fn ($get) => $get('range_type') === 'custom')
                        ->required(fn ($get) => $get('range_type') === 'custom'),

                    DatePicker::make('custom_end')
                        ->label('วันที่สิ้นสุด')
                        ->default(now()->addDays(30)->toDateString())
                        ->visible(fn ($get) => $get('range_type') === 'custom')
                        ->required(fn ($get) => $get('range_type') === 'custom'),
                ])
                ->action(function (array $data) {
                    $startDate = Carbon::today();

                    $endDate = match ($data['range_type']) {
                        '7_days' => Carbon::today()->addDays(7),
                        '14_days' => Carbon::today()->addDays(14),
                        'end_of_month' => Carbon::today()->endOfMonth(),
                        'next_month' => Carbon::today()->addMonthNoOverflow()->endOfMonth(),
                        'custom' => Carbon::parse($data['custom_end']),
                        default => Carbon::today()->addDays(14),
                    };

                    if ($data['range_type'] === 'custom' && !empty($data['custom_start'])) {
                        $startDate = Carbon::parse($data['custom_start']);
                    }

                    $schedules = WeeklySchedule::with('movie')->get();
                    $createdCount = 0;
                    $period = CarbonPeriod::create($startDate, $endDate);

                    foreach ($period as $date) {
                        $dayOfWeekIso = $date->dayOfWeekIso; // 1=Mon ... 7=Sun

                        $daySchedules = $schedules->where('day_of_week', $dayOfWeekIso);

                        foreach ($daySchedules as $schedule) {
                            $movie = $schedule->movie;
                            
                            // Check if movie is active and the date falls within the movie's start/end dates
                            if (!$movie || !$movie->is_active) {
                                continue;
                            }
                            
                            if ($movie->start_date && $date->lt(Carbon::parse($movie->start_date)->startOfDay())) {
                                continue;
                            }
                            
                            if ($movie->end_date && $date->gt(Carbon::parse($movie->end_date)->endOfDay())) {
                                continue;
                            }

                            $timeString = Carbon::parse($schedule->show_time)->format('H:i:s');

                            $exists = Showtime::whereDate('show_date', $date->toDateString())
                                ->where('show_time', $timeString)
                                ->where('movie_id', $schedule->movie_id)
                                ->exists();

                            if (! $exists) {
                                Showtime::create([
                                    'weekly_schedule_id' => $schedule->id,
                                    'movie_id' => $schedule->movie_id,
                                    'show_date' => $date->toDateString(),
                                    'show_time' => $timeString,
                                    'total_seats' => $schedule->total_seats,
                                    'available_seats' => $schedule->total_seats,
                                ]);
                                $createdCount++;
                            }
                        }
                    }

                    Notification::make()
                        ->title('สร้างรอบฉายสำเร็จ!')
                        ->body("สร้างรอบฉายใหม่ทั้งหมด {$createdCount} รอบ (ช่วงวันที่ {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')})")
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
