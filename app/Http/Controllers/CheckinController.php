<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function form()
    {
        return view('checkin.form');
    }

    public function process(Request $request)
    {
        $request->validate([
            'qr_ticket_ref' => 'required|string',
        ]);

        $booking = Booking::where('qr_ticket_ref', $request->qr_ticket_ref)
            ->with('showtime.movie')
            ->first();

        if (! $booking) {
            return back()->withErrors(['qr_ticket_ref' => 'ไม่พบรหัสการจองนี้ในระบบ']);
        }

        if ($booking->status === 'redeemed') {
            return back()->withErrors(['qr_ticket_ref' => 'QR นี้ถูกใช้รับตั๋วไปแล้วเมื่อ ' . $booking->checked_in_at->format('d/m/Y H:i')]);
        }

        if ($booking->status !== 'paid') {
            return back()->withErrors(['qr_ticket_ref' => 'การจองนี้ยังไม่ได้ชำระเงิน (สถานะ: ' . $booking->status . ')']);
        }

        $booking->update([
            'status' => 'redeemed',
            'checked_in_at' => now(),
        ]);

        return view('checkin.success', compact('booking'));
    }
}