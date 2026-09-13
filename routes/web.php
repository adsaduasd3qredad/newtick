<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\PosController;

// --- 1. หน้าแรกและระบบจองฝั่งลูกค้า ---
Route::get('/', [ShowtimeController::class, 'index'])->name('showtimes.index');
Route::get('/bookings/search', [BookingController::class, 'search'])
    ->middleware('throttle:10,1')
    ->name('bookings.search');

Route::get('/bookings/create/{showtime?}', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings/seats', [BookingController::class, 'seats'])->name('bookings.seats');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{booking:qr_ticket_ref}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
Route::post('/bookings/{booking:qr_ticket_ref}/confirm', [BookingController::class, 'confirmPayment'])->name('bookings.confirm');
Route::get('/bookings/{booking:qr_ticket_ref}/confirmed', [BookingController::class, 'confirmed'])->name('bookings.confirmed');

// --- 2. ระบบ Login / Logout กลางสำหรับพนักงาน (Staff) ---
Route::get('/login', function () {
    return view('auth.login');
})->middleware('throttle:6,1')->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        if (Auth::user()->role === 'admin') {
            return redirect('/admin');
        }

        if (Auth::user()->role === 'staff') {
            return redirect()->intended('/pos');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'บัญชีนี้ไม่มีสิทธิ์เข้าสู่ระบบเจ้าหน้าที่',
        ]);
    }

    return back()->withErrors([
        'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// --- 3. กลุ่มเส้นทางพนักงาน (POS & Checkin) ---
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    
    // ตรวจตั๋ว
    Route::get('/checkin', [CheckinController::class, 'form'])->name('checkin.form');
    Route::post('/checkin', [CheckinController::class, 'process'])->name('checkin.process');

    // หน้า POS 
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/quick-sell', [PosController::class, 'quickSell'])->name('quick-sell');
        Route::get('/receipt/{booking}', [PosController::class, 'receipt'])->name('receipt');
        
        Route::get('/scan', [PosController::class, 'scan'])->name('scan');
        Route::post('/verify', [PosController::class, 'verifyBooking'])->name('verify');
        Route::post('/confirm-checkin/{booking}', [PosController::class, 'confirmCheckin'])->name('confirm-checkin');
        
        Route::get('/orders', [PosController::class, 'orders'])->name('orders');
        Route::get('/reports', [PosController::class, 'reports'])->name('reports');
        Route::get('/reports/pdf', [PosController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/csv', [PosController::class, 'exportCsv'])->name('reports.csv');
    });
});