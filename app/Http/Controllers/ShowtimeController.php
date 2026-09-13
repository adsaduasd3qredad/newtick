<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ShowtimeController extends Controller
{
    public function index(Request $request)
    {
        // 1. à¹€à¸Šà¹‡à¸„à¸§à¹ˆà¸²à¸¡à¸µà¸à¸²à¸£à¹€à¸¥à¸·à¸­à¸à¸§à¸±à¸™à¸—à¸µà¹ˆà¸ˆà¸²à¸à¸›à¸à¸´à¸—à¸´à¸™à¸ªà¹ˆà¸‡à¸¡à¸²à¹„à¸«à¸¡ à¸–à¹‰à¸²à¸¡à¸µà¹ƒà¸«à¹‰à¸™à¸±à¸šà¸ˆà¸²à¸à¸§à¸±à¸™à¸™à¸±à¹‰à¸™à¹€à¸›à¹‡à¸™à¸«à¸¥à¸±à¸
        if ($request->has('date') && !empty($request->query('date'))) {
            $selectedDate = \Carbon\Carbon::parse($request->query('date'));
            // à¸„à¸³à¸™à¸§à¸“à¸«à¸² weekOffset à¹€à¸—à¸µà¸¢à¸šà¸à¸±à¸šà¸ªà¸±à¸›à¸”à¸²à¸«à¹Œà¸›à¸±à¸ˆà¸ˆà¸¸à¸šà¸±à¸™
            $startOfWeek = $selectedDate->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $currentWeekStart = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $weekOffset = (int) $currentWeekStart->diffInWeeks($selectedDate, false);
        } else {
            // à¸–à¹‰à¸²à¹„à¸¡à¹ˆà¸¡à¸µà¸à¸²à¸£à¹€à¸¥à¸·à¸­à¸ à¹ƒà¸«à¹‰à¹ƒà¸Šà¹‰ weekOffset à¸•à¸²à¸¡à¸›à¸¸à¹ˆà¸¡à¸ªà¸±à¸›à¸”à¸²à¸«à¹Œà¸à¹ˆà¸­à¸™à¸«à¸™à¹‰à¸²/à¸–à¸±à¸”à¹„à¸›à¸›à¸à¸•à¸´
            $weekOffset = (int) $request->query('week', 0);
            $startOfWeek = now()->addWeeks($weekOffset)->startOfWeek(\Carbon\Carbon::SUNDAY);
        }

        $endOfWeek = $startOfWeek->copy()->addDays(6);
        $weekDates = collect(range(0, 6))->map(fn($i) => $startOfWeek->copy()->addDays($i));

        // 2. à¸”à¸¶à¸‡à¸£à¸­à¸šà¸‰à¸²à¸¢à¸žà¸´à¹€à¸¨à¸©à¸—à¸µà¹ˆà¸£à¸°à¸šà¸¸à¸§à¸±à¸™à¸—à¸µà¹ˆà¹à¸™à¹ˆà¸™à¸­à¸™ à¸žà¸£à¹‰à¸­à¸¡à¸ à¸²à¸žà¸¢à¸™à¸•à¸£à¹Œà¹à¸šà¸š Eager Loading (à¸Šà¹ˆà¸§à¸¢à¹à¸à¹‰à¹€à¸§à¹‡à¸šà¸­à¸·à¸” N+1)
        $showtimes = Showtime::with(['movie' => function($q) {
                $q->where('is_active', true);
            }])
            ->whereHas('movie', function ($q) {
                $q->where('is_active', true);
            })
            ->whereBetween('show_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get();

        // 3. à¸”à¸¶à¸‡à¸£à¸­à¸šà¸‰à¸²à¸¢à¸›à¸£à¸°à¸ˆà¸³à¸ªà¸±à¸›à¸”à¸²à¸«à¹Œ (Weekly Schedules) à¸žà¸£à¹‰à¸­à¸¡ Eager Loading
        $weeklySchedules = WeeklySchedule::with(['movie' => function($q) {
                $q->where('is_active', true);
            }])
            ->whereHas('movie', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        // 4. à¸”à¸¶à¸‡à¸ à¸²à¸žà¸¢à¸™à¸•à¸£à¹Œà¸—à¸µà¹ˆà¹€à¸›à¸´à¸”à¸‰à¸²à¸¢à¸—à¸±à¹‰à¸‡à¸«à¸¡à¸”
        $movies = Movie::where('is_active', true)->get();

        // à¸„à¸‡à¹€à¸§à¸¥à¸² 6 à¸ªà¸¥à¹‡à¸­à¸•à¸žà¸·à¹‰à¸™à¸à¸²à¸™à¹„à¸§à¹‰
        $timeSlots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];

        return view('showtimes.index', compact('showtimes', 'weeklySchedules', 'movies', 'timeSlots', 'weekDates', 'weekOffset'));
    }
}
