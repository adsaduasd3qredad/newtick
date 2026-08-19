<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\WeeklySchedule; // 📌 1. เพิ่มการเรียกใช้งาน Model WeeklySchedule
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ShowtimeController extends Controller
{
    public function index(Request $request)
    {
        // 1. เช็คว่ามีการเลือกวันที่จากปฏิทินส่งมาไหม ถ้ามีให้นับจากวันนั้นเป็นหลัก
        if ($request->has('date') && !empty($request->query('date'))) {
            $selectedDate = \Carbon\Carbon::parse($request->query('date'));
            // คำนวณหา weekOffset เทียบกับสัปดาห์ปัจจุบัน
            $startOfWeek = $selectedDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $currentWeekStart = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $weekOffset = (int) $currentWeekStart->diffInWeeks($selectedDate, false);
        } else {
            // ถ้าไม่มีการเลือก ให้ใช้ weekOffset ตามปุ่มสัปดาห์ก่อนหน้า/ถัดไปปกติ
            $weekOffset = (int) $request->query('week', 0);
            $startOfWeek = now()->addWeeks($weekOffset)->startOfWeek(\Carbon\Carbon::SUNDAY);
        }

        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $weekDates = collect(range(0, 6))->map(fn($i) => $startOfWeek->copy()->addDays($i));

        // 2. ดึงรอบพิเศษ (Showtimes) ในสัปดาห์นี้
        $showtimes = Showtime::with('movie')
            ->whereBetween('show_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get();

        // 3. 📌 ดึงแม่แบบประจำสัปดาห์ (Weekly Schedules) มาเป็นพื้นหลังหลัก
        $weeklySchedules = WeeklySchedule::with('movie')->get();

        // ช่องเวลาตายตัว ตามที่ทางศูนย์ล็อกเวลาไว้
        $timeSlots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];

        return view('showtimes.index', compact('showtimes', 'weeklySchedules', 'timeSlots', 'weekDates', 'weekOffset'));
    }
}
