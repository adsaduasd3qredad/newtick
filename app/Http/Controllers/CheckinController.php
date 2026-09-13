<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function form()
    {
        $todayCheckins = Booking::whereDate('checked_in_at', today())
            ->with('showtime.movie')
            ->orderBy('checked_in_at', 'desc')
            ->take(15)
            ->get();

        $todayCheckedInSeats = Booking::whereDate('checked_in_at', today())->sum('quantity');

        return view('checkin.form', compact('todayCheckins', 'todayCheckedInSeats'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'qr_ticket_ref' => 'required|string',
        ]);

        $queryRef = trim($request->qr_ticket_ref);

        // รองรับทั้ง UUID QR ref, รหัส Booking ID แบบ #123 หรือ 123
        $booking = Booking::where('qr_ticket_ref', $queryRef)
            ->orWhere('id', ltrim($queryRef, '#'))
            ->with('showtime.movie')
            ->first();

        if (! $booking) {
            return back()->withErrors(['qr_ticket_ref' => 'ไม่พบรหัสการจองหรือ QR Ticket นี้ในระบบ'])->withInput();
        }

        if ($booking->status === 'redeemed') {
            $timeStr = $booking->checked_in_at ? $booking->checked_in_at->format('d/m/Y H:i น.') : 'ก่อนหน้านี้';
            return back()->withErrors(['qr_ticket_ref' => "⚠️ QR ตั๋วนี้ (#{$booking->id}) ถูกใช้รับตั๋วไปแล้วเมื่อ {$timeStr}"])->withInput();
        }

        if ($booking->status === 'expired' || $booking->status === 'cancelled') {
            return back()->withErrors(['qr_ticket_ref' => "⚠️ รายการจองนี้ถูกยกเลิกหรือหมดอายุแล้ว (สถานะ: {$booking->status})"])->withInput();
        }

        if ($booking->status !== 'paid') {
            return back()->withErrors(['qr_ticket_ref' => "⚠️ การจอง #{$booking->id} ยังไม่ได้ชำระเงิน (สถานะ: {$booking->status}) กรุณาชำระเงินที่เคาน์เตอร์ก่อน"])->withInput();
        }

        $booking->update([
            'status' => 'redeemed',
            'checked_in_at' => now(),
        ]);

        return view('checkin.success', compact('booking'));
    }
}