<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        // ดึงรอบฉายของวันนี้เท่านั้น เพื่อให้พนักงานเลือกได้เร็ว
        $today = now()->toDateString();
        $showtimes = Showtime::with('movie')
            ->where('show_date', $today)
            ->orderBy('show_time', 'asc')
            ->get();

        return view('pos.index', compact('showtimes'));
    }
}