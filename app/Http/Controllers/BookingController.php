<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Booking;
use App\Services\PromptPayQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\WeeklySchedule;


class BookingController extends Controller
{
    // ราคาต่อที่นั่ง (ใช้ร่วมกันทั้งหน้าเลือกที่นั่งและตอนสร้าง booking จริง)
    protected int $pricePerSeat = 110;

    public function create(Request $request, $id = null)
    {
        // ถ้าส่งมาแบบ Showtime ปกติ
        if ($id) {
            $showtime = Showtime::with('movie')->findOrFail($id);
        } else {
            // กรณีมาจาก Weekly Schedule (รับค่า day และ time ทาง Query String)
            $date = $request->query('date');
            $time = $request->query('time');
            $weeklyScheduleId = $request->query('weekly_id');

            $weeklySchedule = WeeklySchedule::with('movie')->findOrFail($weeklyScheduleId);

            // ค้นหาหรือสร้าง Showtime จริงขึ้นมาในฐานข้อมูลทันที เพื่อให้อ้างอิง ID ได้
            $showtime = Showtime::firstOrCreate(
                [
                    'show_date' => $date,
                    'show_time' => $time,
                ],
                [
                    'movie_id' => $weeklySchedule->movie_id,
                    'available_seats' => $weeklySchedule->total_seats,
                ]
            );
            $showtime->load('movie');
        }

        return view('bookings.create', compact('showtime'));
    }

    /**
     * STEP ใหม่: รับข้อมูลผู้จอง + จำนวนที่นั่งจากฟอร์ม create แล้วแสดงหน้าเลือกที่นั่ง
     * ยังไม่สร้าง Booking จริง แค่ส่งข้อมูลต่อผ่าน hidden field ในหน้าเลือกที่นั่ง
     */
    public function seats(Request $request)
    {
        // 📌 ปรับ max จาก 10 เป็น 160 เพื่อรองรับบริษัท/ราชการ
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'nullable|string|max:20',
            'visitor_type' => 'required|in:individual,company,government',
            'quantity' => 'required|integer|min:1|max:160',
        ]);

        $showtime = Showtime::with('movie')->findOrFail($validated['showtime_id']);

        // 📌 เช็คเพิ่มเติมว่าถ้าเลือกประเภทบุคคลทั่วไป ต้องไม่เกิน 10 ที่นั่ง
        if ($validated['visitor_type'] === 'individual' && $validated['quantity'] > 10) {
            return back()->withErrors(['quantity' => 'บุคคลทั่วไปสามารถจองได้สูงสุด 10 ที่นั่งเท่านั้น']);
        }

        if ($showtime->available_seats < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'ที่นั่งไม่พอ เหลือ ' . $showtime->available_seats . ' ที่นั่ง',
            ]);
        }

        $bookedSeats = $this->bookedSeatsFor($showtime->id);

        return view('bookings.seats', [
            'showtime' => $showtime,
            'bookedSeats' => $bookedSeats,
            'pricePerSeat' => $this->pricePerSeat,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_phone' => $validated['booker_phone'],
            'visitor_type' => $validated['visitor_type'],
            'quantity' => $validated['quantity'],
        ]);
    }

    public function store(Request $request)
    {
        // 📌 ปรับ max จาก 10 เป็น 160 เช่นกัน
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'nullable|string|max:20',
            'visitor_type' => 'required|in:individual,company,government',
            'quantity' => 'required|integer|min:1|max:160',
            'seats' => 'required|array|min:1',
            'seats.*' => 'string|max:10',
        ]);

        if ($validated['visitor_type'] === 'individual' && $validated['quantity'] > 10) {
            return back()->withErrors(['quantity' => 'บุคคลทั่วไปสามารถจองได้สูงสุด 10 ที่นั่งเท่านั้น']);
        }

        if (count($validated['seats']) !== (int) $validated['quantity']) {
            return back()->withErrors(['seats' => 'จำนวนที่นั่งที่เลือกไม่ตรงกับจำนวนที่ระบุไว้']);
        }

        return DB::transaction(function () use ($validated) {
            // ล็อก row ของ showtime นี้ไว้ก่อน กันคนอื่นจองพร้อมกัน
            $showtime = Showtime::where('id', $validated['showtime_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($showtime->available_seats < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'ที่นั่งไม่พอ เหลือ ' . $showtime->available_seats . ' ที่นั่ง']);
            }

            // เช็คซ้ำว่าที่นั่งที่เลือกยังว่างจริง ป้องกันกรณีมีคนจองซ้อนระหว่างที่กำลังเลือกที่นั่ง
            $takenSeats = $this->bookedSeatsFor($showtime->id, lock: true);
            $conflict = array_intersect($validated['seats'], $takenSeats);

            if (!empty($conflict)) {
                return back()->withErrors([
                    'seats' => 'ที่นั่ง ' . implode(', ', $conflict) . ' เพิ่งถูกจองไปแล้ว กรุณาเลือกที่นั่งใหม่',
                ]);
            }

            // ตัดที่นั่งทันที (ล็อกไว้ก่อน)
            $showtime->decrement('available_seats', $validated['quantity']);

            $booking = Booking::create([
                ...$validated,
                'total_amount' => $this->pricePerSeat * $validated['quantity'],
                'status' => 'pending',
                'qr_ticket_ref' => Str::uuid(),
                'expires_at' => now()->addMinutes(30),
            ]);

            return redirect()->route('bookings.payment', $booking->id);
        });
    }

    public function payment(Booking $booking)
    {
        if ($booking->status !== 'pending' && $booking->status !== 'awaiting_payment') {
            abort(404);
        }

        $qrPayload = PromptPayQr::generatePayload(
            env('PROMPTPAY_TARGET'),
            (float) $booking->total_amount
        );

        return view('bookings.payment', compact('booking', 'qrPayload'));
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:qr_code,counter',
        ]);

        if ($booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);
            return back()->withErrors(['expired' => 'หมดเวลาการจองแล้ว กรุณาจองใหม่']);
        }

        // MVP: ยืนยันเองก่อน (ยังไม่ได้ต่อ API ธนาคารจริง)
        // ของจริงต้องรอเจ้าหน้าที่ตรวจสลิป หรือ webhook จากธนาคาร
        $booking->update([
            'payment_method' => $request->payment_method,
            'status' => 'paid',
            'qr_payment_ref' => (string) Str::uuid(),
        ]);

        return redirect()->route('bookings.confirmed', $booking->id);
    }

    public function confirmed(Booking $booking)
    {
        return view('bookings.confirmed', compact('booking'));
    }

    /**
     * รวมรหัสที่นั่งที่ถูกจองไปแล้วของ showtime นี้ (นับเฉพาะ booking ที่ยังไม่หมดอายุ/ยังไม่ถูกยกเลิก)
     */
    protected function bookedSeatsFor(int $showtimeId, bool $lock = false): array
    {
        $query = Booking::where('showtime_id', $showtimeId)
            ->whereIn('status', ['pending', 'awaiting_payment', 'paid'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->pluck('seats')
            ->filter()
            ->flatten()
            ->values()
            ->all();
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // 1. คืนที่นั่งก่อนลบ
        if ($booking->showtime) {
            $booking->showtime->increment('available_seats', $booking->quantity);
        }

        // 2. ลบข้อมูลการจอง
        $booking->delete();

        return back()->with('success', 'ลบข้อมูลสำเร็จ และคืนที่นั่งเรียบร้อยแล้ว');
    }
}
