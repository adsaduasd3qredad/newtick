<?php

namespace App\Filament\Resources\WeeklySchedules\Pages;

use App\Filament\Resources\WeeklySchedules\WeeklyScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWeeklySchedule extends CreateRecord 
{
    protected static string $resource = WeeklyScheduleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $day = $data['day_of_week'];     // เป็นค่าเดี่ยว เช่น วันเสาร์ (6)
        $times = $data['show_times'];   // เป็น Array ของเวลา เช่น ['10:00:00']
        $lastRecord = null;

        // 📌 วนลูปสร้างเฉพาะเวลาที่เลือกในวันนั้นๆ วันอื่นจะไม่เกี่ยวกัน
        foreach ($times as $time) {
            $lastRecord = static::getModel()::create([
                'movie_id' => $data['movie_id'],
                'show_time' => $time,
                'day_of_week' => $day,
                'total_seats' => $data['total_seats'],
            ]);
        }

        return $lastRecord; 
    }
}