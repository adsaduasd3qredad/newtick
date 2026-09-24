<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Booking;
use App\Services\PromptPayQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\WeeklySchedule;


class BookingController extends Controller
{
    // à¸£à¸²à¸„à¸²à¸•à¹ˆà¸­à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡ (à¹ƒà¸Šà¹‰à¸£à¹ˆà¸§à¸¡à¸à¸±à¸™à¸—à¸±à¹‰à¸‡à¸«à¸™à¹‰à¸²à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹à¸¥à¸°à¸•à¸­à¸™à¸ªà¸£à¹‰à¸²à¸‡ booking à¸ˆà¸£à¸´à¸‡)
    public function create(Request $request, $id = null)
    {
        // à¸–à¹‰à¸²à¸ªà¹ˆà¸‡à¸¡à¸²à¹à¸šà¸š Showtime à¸›à¸à¸•à¸´
        if ($id) {
            $showtime = Showtime::with('movie')->findOrFail($id);
        } else {
            // à¸à¸£à¸“à¸µà¸¡à¸²à¸ˆà¸²à¸ Weekly Schedule (à¸£à¸±à¸šà¸„à¹ˆà¸² day à¹à¸¥à¸° time à¸—à¸²à¸‡ Query String)
            $date = $request->query('date');
            $time = $request->query('time');
            $weeklyScheduleId = $request->query('weekly_id');

            $weeklySchedule = WeeklySchedule::with('movie')->findOrFail($weeklyScheduleId);

            // à¸„à¹‰à¸™à¸«à¸²à¸«à¸£à¸·à¸­à¸ªà¸£à¹‰à¸²à¸‡ Showtime à¸ˆà¸£à¸´à¸‡à¸‚à¸¶à¹‰à¸™à¸¡à¸²à¹ƒà¸™à¸à¸²à¸™à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸—à¸±à¸™à¸—à¸µ à¹€à¸žà¸·à¹ˆà¸­à¹ƒà¸«à¹‰à¸­à¹‰à¸²à¸‡à¸­à¸´à¸‡ ID à¹„à¸”à¹‰
            $showtime = Showtime::query()
                ->whereDate('show_date', $date)
                ->whereTime('show_time', $time)
                ->firstOrCreate(
                    ['movie_id' => $weeklySchedule->movie_id],
                    [
                        'show_date' => $date,
                        'show_time' => $time,
                        'available_seats' => $weeklySchedule->total_seats,
                    ]
                );
            $showtime->load('movie');
        }

        if (! $this->canBookShowtime($request, $showtime)) {
            return redirect()->route('showtimes.index')
                ->with('error', 'รอบฉายนี้เริ่มไปแล้ว ไม่สามารถจองย้อนหลังได้');
        }

        return view('bookings.create', [
            'showtime' => $showtime,
            'pricePerSeat' => $this->pricePerSeat(),
            'returnToPos' => $request->boolean('staff'),
            'groupBooking' => $request->boolean('group'),
        ]);
    }

    /**
     * STEP à¹ƒà¸«à¸¡à¹ˆ: à¸£à¸±à¸šà¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸œà¸¹à¹‰à¸ˆà¸­à¸‡ + à¸ˆà¸³à¸™à¸§à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸ˆà¸²à¸à¸Ÿà¸­à¸£à¹Œà¸¡ create à¹à¸¥à¹‰à¸§à¹à¸ªà¸”à¸‡à¸«à¸™à¹‰à¸²à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡
     * à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¸ªà¸£à¹‰à¸²à¸‡ Booking à¸ˆà¸£à¸´à¸‡ à¹à¸„à¹ˆà¸ªà¹ˆà¸‡à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸•à¹ˆà¸­à¸œà¹ˆà¸²à¸™ hidden field à¹ƒà¸™à¸«à¸™à¹‰à¸²à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡
     */
    public function seats(Request $request)
    {
        // ðŸ“Œ à¸›à¸£à¸±à¸š max à¸ˆà¸²à¸ 10 à¹€à¸›à¹‡à¸™ 160 à¹€à¸žà¸·à¹ˆà¸­à¸£à¸­à¸‡à¸£à¸±à¸šà¹‚à¸£à¸‡à¹€à¸£à¸µà¸¢à¸™/à¸šà¸£à¸´à¸©à¸±à¸—/à¸£à¸²à¸Šà¸à¸²à¸£
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,government',
            'quantity' => 'exclude_unless:visitor_type,individual|required|integer|min:1|max:10',
            'return_to_pos' => 'nullable|boolean',
            'pos_amount_paid' => 'nullable|numeric|min:0',
            'pos_notes' => 'nullable|string|max:1000',
            ...$this->visitorDetailRules(),
        ]);

        $validated['quantity'] = $this->resolveVisitorQuantity($validated);
        $visitorDetails = $this->visitorDetailsFor($validated);

        $showtime = Showtime::with('movie')->findOrFail($validated['showtime_id']);

        if (! $this->canBookShowtime($request, $showtime)) {
            return back()->with('error', 'รอบฉายนี้เริ่มไปแล้ว ไม่สามารถจองย้อนหลังได้');
        }

        // à¸—à¸³à¸„à¸§à¸²à¸¡à¸ªà¸°à¸­à¸²à¸”à¸à¸²à¸£à¸ˆà¸­à¸‡à¸—à¸µà¹ˆà¸«à¸¡à¸”à¹€à¸§à¸¥à¸²à¹à¸¥à¸°à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡
        $this->cleanupExpiredBookings($showtime->id);
        $showtime->refresh();

        // ðŸ“Œ à¹€à¸Šà¹‡à¸„à¹€à¸žà¸´à¹ˆà¸¡à¹€à¸•à¸´à¸¡à¸§à¹ˆà¸²à¸–à¹‰à¸²à¹€à¸¥à¸·à¸­à¸à¸›à¸£à¸°à¹€à¸ à¸—à¸šà¸¸à¸„à¸„à¸¥à¸—à¸±à¹ˆà¸§à¹„à¸› à¸•à¹‰à¸­à¸‡à¹„à¸¡à¹ˆà¹€à¸à¸´à¸™ 10 à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡
        if ($validated['visitor_type'] === 'individual' && $validated['quantity'] > 10) {
            return back()->withErrors(['quantity' => 'à¸šà¸¸à¸„à¸„à¸¥à¸—à¸±à¹ˆà¸§à¹„à¸›à¸ªà¸²à¸¡à¸²à¸£à¸–à¸ˆà¸­à¸‡à¹„à¸”à¹‰à¸ªà¸¹à¸‡à¸ªà¸¸à¸” 10 à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹€à¸—à¹ˆà¸²à¸™à¸±à¹‰à¸™']);
        }

        if ($showtime->available_seats < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹„à¸¡à¹ˆà¸žà¸­ à¹€à¸«à¸¥à¸·à¸­ ' . $showtime->available_seats . ' à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡',
            ]);
        }

        $bookedSeats = $this->bookedSeatsFor($showtime->id);

        return view('bookings.seats', [
            'showtime' => $showtime,
            'bookedSeats' => $bookedSeats,
            'pricePerSeat' => $this->pricePerSeat(),
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_phone' => $validated['booker_phone'],
            'visitor_type' => $validated['visitor_type'],
            'quantity' => $validated['quantity'],
            'visitorDetails' => $visitorDetails,
            'returnToPos' => $this->isAuthorizedStaffRequest($request),
            'groupBooking' => in_array($validated['visitor_type'], ['school', 'government'], true),
            'posAmountPaid' => $request->input('pos_amount_paid'),
            'posNotes' => $request->input('pos_notes'),
        ]);
    }

    public function store(Request $request)
    {
        // ðŸ“Œ à¸›à¸£à¸±à¸š max à¸ˆà¸²à¸ 10 à¹€à¸›à¹‡à¸™ 160 à¹€à¸Šà¹ˆà¸™à¸à¸±à¸™
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,government',
            'quantity' => 'exclude_unless:visitor_type,individual|required|integer|min:1|max:10',
            'return_to_pos' => 'nullable|boolean',
            'pos_amount_paid' => 'nullable|numeric|min:0',
            'pos_notes' => 'nullable|string|max:1000',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string|max:10|distinct',
            ...$this->visitorDetailRules(),
        ]);

        $validated['quantity'] = $this->resolveVisitorQuantity($validated);

        if ($validated['visitor_type'] === 'individual' && $validated['quantity'] > 10) {
            return back()->withErrors(['quantity' => 'à¹à¸šà¸šà¸šà¸¸à¸„à¸„à¸¥à¸—à¸±à¹ˆà¸§à¹„à¸›à¸ˆà¸­à¸‡à¹„à¸”à¹‰à¸ªà¸¹à¸‡à¸ªà¸¸à¸” 10 à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸•à¹ˆà¸­à¸à¸²à¸£à¸—à¸³à¸£à¸²à¸¢à¸à¸²à¸£']);
        }

        if (count($validated['seats']) !== (int) $validated['quantity']) {
            return back()->withErrors(['seats' => 'à¸ˆà¸³à¸™à¸§à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸—à¸µà¹ˆà¹€à¸¥à¸·à¸­à¸à¹„à¸¡à¹ˆà¸•à¸£à¸‡à¸à¸±à¸šà¸ˆà¸³à¸™à¸§à¸™à¸—à¸µà¹ˆà¸£à¸°à¸šà¸¸à¹„à¸§à¹‰']);
        }

        $visitorDetails = $this->visitorDetailsFor($validated);

        $returnToPos = $this->isAuthorizedStaffRequest($request);
        unset($validated['return_to_pos']);

        $this->cleanupExpiredBookings($validated['showtime_id']);

        return DB::transaction(function () use ($validated, $visitorDetails, $returnToPos, $request) {
            // à¸¥à¹‡à¸­à¸ row à¸‚à¸­à¸‡ showtime à¸™à¸µà¹‰à¹„à¸§à¹‰à¸à¹ˆà¸­à¸™ à¸à¸±à¸™à¸„à¸™à¸­à¸·à¹ˆà¸™à¸ˆà¸­à¸‡à¸žà¸£à¹‰à¸­à¸¡à¸à¸±à¸™
            $showtime = Showtime::where('id', $validated['showtime_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $this->canBookShowtime($request, $showtime)) {
                return back()->with('error', 'รอบฉายนี้เริ่มไปแล้ว ไม่สามารถจองย้อนหลังได้');
            }

            if ($showtime->available_seats < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹„à¸¡à¹ˆà¸žà¸­ à¹€à¸«à¸¥à¸·à¸­ ' . $showtime->available_seats . ' à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡']);
            }

            // à¹€à¸Šà¹‡à¸„à¸‹à¹‰à¸³à¸§à¹ˆà¸²à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸—à¸µà¹ˆà¹€à¸¥à¸·à¸­à¸à¸¢à¸±à¸‡à¸§à¹ˆà¸²à¸‡à¸ˆà¸£à¸´à¸‡ à¸›à¹‰à¸­à¸‡à¸à¸±à¸™à¸à¸£à¸“à¸µà¸¡à¸µà¸„à¸™à¸ˆà¸­à¸‡à¸‹à¹‰à¸­à¸™à¸£à¸°à¸«à¸§à¹ˆà¸²à¸‡à¸—à¸µà¹ˆà¸à¸³à¸¥à¸±à¸‡à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡
            $takenSeats = $this->bookedSeatsFor($showtime->id, lock: true);
            $conflict = array_intersect($validated['seats'], $takenSeats);

            if (!empty($conflict)) {
                return back()->withErrors([
                    'seats' => 'à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡ ' . implode(', ', $conflict) . ' à¹€à¸žà¸´à¹ˆà¸‡à¸–à¸¹à¸à¸ˆà¸­à¸‡à¹„à¸›à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹ƒà¸«à¸¡à¹ˆ',
                ]);
            }

            // à¸•à¸±à¸”à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸—à¸±à¸™à¸—à¸µ (à¸¥à¹‡à¸­à¸à¹„à¸§à¹‰à¸à¹ˆà¸­à¸™)
            $showtime->decrement('available_seats', $validated['quantity']);

            $booking = Booking::create([
                ...$validated,
                'visitor_details' => $visitorDetails,
                'total_amount' => $this->pricePerSeat() * $validated['quantity'],
                'amount_paid' => null,
                'notes' => $returnToPos ? $request->input('pos_notes') : null,
                'status' => 'pending',
                'qr_ticket_ref' => (string) Str::uuid(),
                'expires_at' => now()->addMinutes(30),
            ]);

            return redirect()->route('bookings.payment', [
                'booking' => $booking->qr_ticket_ref,
                'staff' => $returnToPos ? 1 : null,
            ]);
        });
    }

    public function payment(Request $request, Booking $booking)
    {
        $this->syncCounterPaymentDeadline($booking);

        if ($booking->status !== 'pending' && $booking->status !== 'awaiting_payment') {
            return redirect()->route('bookings.confirmed', $booking->qr_ticket_ref);
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);
            if ($booking->showtime) {
                $booking->showtime->increment('available_seats', $booking->quantity);
            }
            return redirect()->route('showtimes.index')->with('error', 'à¸à¸²à¸£à¸ˆà¸­à¸‡à¸«à¸¡à¸”à¸­à¸²à¸¢à¸¸à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸—à¸³à¸£à¸²à¸¢à¸à¸²à¸£à¹ƒà¸«à¸¡à¹ˆ');
        }

        $qrPayload = PromptPayQr::generatePayload(
            $this->promptPayTarget(),
            (float) $booking->total_amount + $this->paymentFee('qr_code')
        );

        return view('bookings.payment', [
            'booking' => $booking,
            'qrPayload' => $qrPayload,
            'qrPaymentFee' => $this->paymentFee('qr_code'),
            'qrTotal' => (float) $booking->total_amount + $this->paymentFee('qr_code'),
            'returnToPos' => $this->isAuthorizedStaffRequest($request),
        ]);
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:qr_code,counter',
            'return_to_pos' => 'nullable|boolean',
            'has_slip' => 'nullable|boolean',
            'payment_slip' => 'required_if:payment_method,qr_code|nullable|image|max:5120',
        ]);

        if (! in_array($booking->status, ['pending', 'awaiting_payment'], true)) {
            return redirect()->route('bookings.confirmed', $booking->qr_ticket_ref);
        }

        $this->syncCounterPaymentDeadline($booking);
        $booking->refresh();

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $booking->expireAndReleaseSeats();
            return back()->withErrors(['expired' => 'à¸«à¸¡à¸”à¹€à¸§à¸¥à¸²à¸à¸²à¸£à¸ˆà¸­à¸‡à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸ˆà¸­à¸‡à¹ƒà¸«à¸¡à¹ˆ']);
        }

        // A hidden field/query string is not proof of staff authorization.
        // Only an authenticated staff member may complete a payment at the POS.
        $returnToPos = $this->isAuthorizedStaffRequest($request);
        $slipPath = $request->file('payment_slip')?->store('payment-slips', 'public');

        $updates = [
            'payment_method' => $request->input('payment_method'),
            'status' => $returnToPos ? 'paid' : 'awaiting_payment',
            'qr_payment_ref' => (string) Str::uuid(),
        ];

        if (! $returnToPos && $request->input('payment_method') === 'counter') {
            $updates['expires_at'] = $booking->showtime->startsAt()->addMinutes(30);
        }

        $updated = DB::transaction(function () use ($booking, $updates, $slipPath, $request, $returnToPos): bool {
            $showtime = Showtime::whereKey($booking->showtime_id)->lockForUpdate()->first();
            $locked = Booking::whereKey($booking->id)->lockForUpdate()->first();

            if (! $showtime || ! $locked || ! in_array($locked->status, ['pending', 'awaiting_payment'], true)) {
                return false;
            }

            if ($locked->expires_at && $locked->expires_at->isPast()) {
                $locked->update(['status' => 'expired']);
                $showtime->increment('available_seats', $locked->quantity);
                return false;
            }

            $locked->update($updates);
            $fee = $this->paymentFee($request->input('payment_method'));
            $locked->payment()->updateOrCreate(
                ['booking_id' => $locked->id],
                [
                    'ticket_amount' => $locked->total_amount,
                    'transaction_fee' => $fee,
                    'method' => $request->input('payment_method'),
                    'slip_path' => $slipPath,
                    'paid_at' => $returnToPos ? now() : null,
                ]
            );
            $locked->update(['amount_paid' => $returnToPos ? $locked->total_amount + $fee : null]);

            return true;
        });

        if (! $updated) {
            return back()->withErrors(['payment' => 'รายการนี้หมดอายุหรือดำเนินการไปแล้ว']);
        }

        if ($returnToPos) {
            return redirect()->route('pos.index')->with('success', 'ชำระเงินและออกตั๋วเรียบร้อยแล้ว');
        }

        return redirect()->route('bookings.confirmed', $booking->qr_ticket_ref);
    }

    public function confirmed(Booking $booking)
    {
        $this->syncCounterPaymentDeadline($booking);
        $booking->load(['showtime.movie']);
        return view('bookings.confirmed', compact('booking'));
    }

    private function syncCounterPaymentDeadline(Booking $booking): void
    {
        if ($booking->status !== 'awaiting_payment' || $booking->payment_method !== 'counter' || ! $booking->showtime) {
            return;
        }

        $deadline = $booking->showtime->startsAt()->addMinutes(30);
        if (! $booking->expires_at || ! $booking->expires_at->equalTo($deadline)) {
            $booking->update(['expires_at' => $deadline]);
        }
    }

    /**
     * à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸­à¸±à¸•à¹‚à¸™à¸¡à¸±à¸•à¸´à¸ªà¸³à¸«à¸£à¸±à¸šà¸£à¸²à¸¢à¸à¸²à¸£à¸—à¸µà¹ˆà¸«à¸¡à¸”à¹€à¸§à¸¥à¸²
     */
    protected function cleanupExpiredBookings(?int $showtimeId = null): void
    {
        $query = Booking::whereIn('status', ['pending', 'awaiting_payment'])
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                    ->orWhere('payment_method', 'counter');
            });

        if ($showtimeId) {
            $query->where('showtime_id', $showtimeId);
        }

        $expiredBookings = $query->get();
        foreach ($expiredBookings as $expired) {
            $this->syncCounterPaymentDeadline($expired);
            $expired->expireAndReleaseSeats();
        }
    }

    /**
     * à¸£à¸§à¸¡à¸£à¸«à¸±à¸ªà¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸—à¸µà¹ˆà¸–à¸¹à¸à¸ˆà¸­à¸‡à¹„à¸›à¹à¸¥à¹‰à¸§à¸‚à¸­à¸‡ showtime à¸™à¸µà¹‰ (à¸™à¸±à¸šà¹€à¸‰à¸žà¸²à¸° booking à¸—à¸µà¹ˆà¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¸«à¸¡à¸”à¸­à¸²à¸¢à¸¸/à¸¢à¸±à¸‡à¹„à¸¡à¹ˆà¸–à¸¹à¸à¸¢à¸à¹€à¸¥à¸´à¸)
     */
    protected function bookedSeatsFor(int $showtimeId, bool $lock = false): array
    {
        $query = Booking::where('showtime_id', $showtimeId)
            ->whereIn('status', ['pending', 'awaiting_payment', 'paid', 'redeemed'])
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

    public function search(Request $request)
    {
        $reference = trim($request->input('reference', ''));
        $phone = trim($request->input('phone', ''));
        $bookings = collect();

        if ($reference !== '' || $phone !== '') {
            $validated = $request->validate([
                'reference' => ['required', 'uuid'],
                'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            ]);

            $bookings = Booking::where('qr_ticket_ref', $validated['reference'])
                ->where('booker_phone', $validated['phone'])
                ->with(['showtime.movie'])
                ->get();
        }

        return view('bookings.search', compact('bookings', 'reference', 'phone'));
    }

    private function canBookShowtime(Request $request, Showtime $showtime): bool
    {
        return $showtime->isBookable();
    }

    private function isAuthorizedStaffRequest(Request $request): bool
    {
        return $request->boolean('return_to_pos') || $request->boolean('staff')
            ? auth()->check() && in_array(auth()->user()->role, ['admin', 'staff'], true)
            : false;
    }

    private function visitorDetailRules(): array
    {
        return [
            'school_name' => 'nullable|string|max:255',
            'school_type' => 'nullable|in:in_system,out_system',
            'education_level' => 'nullable|in:kindergarten,primary,secondary,university',
            'teachers_count' => 'nullable|integer|min:0|max:160',
            'students_count' => 'nullable|integer|min:0|max:160',
            'parents_count' => 'nullable|integer|min:0|max:160',
            'gov_agency_name' => 'nullable|string|max:255',
            'gov_department' => 'nullable|string|max:255',
            'gov_officers_count' => 'nullable|integer|min:0|max:160',
            'gov_staff_count' => 'nullable|integer|min:0|max:160',
            'gov_others_count' => 'nullable|integer|min:0|max:160',
        ];
    }

    private function resolveVisitorQuantity(array $validated): int
    {
        if ($validated['visitor_type'] === 'individual') {
            $quantity = (int) ($validated['quantity'] ?? 0);
            if ($quantity < 1 || $quantity > 10) {
                throw ValidationException::withMessages([
                    'quantity' => 'บุคคลทั่วไปจองได้ 1–10 ที่นั่งต่อรายการ',
                ]);
            }

            return $quantity;
        }

        $countFields = match ($validated['visitor_type']) {
            'school' => ['teachers_count', 'students_count', 'parents_count'],
            'government' => ['gov_officers_count', 'gov_staff_count', 'gov_others_count'],
        };

        $quantity = array_sum(array_map(
            fn (string $field): int => (int) ($validated[$field] ?? 0),
            $countFields,
        ));

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 คน',
            ]);
        }

        if ($quantity > 160) {
            throw ValidationException::withMessages([
                'quantity' => 'จำนวนผู้เข้าชมรวมต้องไม่เกิน 160 คนต่อรายการ',
            ]);
        }

        return $quantity;
    }

    private function visitorDetailsFor(array $validated): array
    {
        $fields = match ($validated['visitor_type']) {
            'school' => [
                'school_name', 'school_type', 'education_level',
                'teachers_count', 'students_count', 'parents_count',
            ],
            'government' => [
                'gov_agency_name', 'gov_department',
                'gov_officers_count', 'gov_staff_count', 'gov_others_count',
            ],
            default => [],
        };

        $details = array_intersect_key($validated, array_flip($fields));

        foreach ($details as $field => $value) {
            if (str_ends_with($field, '_count')) {
                $details[$field] = (int) $value;
            }
        }

        return $details;
    }

    private function pricePerSeat(): int
    {
        return (int) config('ticketing.price_per_seat');
    }

    private function paymentFee(string $method): int
    {
        return $method === 'qr_code' ? (int) config('ticketing.qr_payment_fee') : 0;
    }

    private function promptPayTarget(): string
    {
        return (string) config('ticketing.promptpay_target');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // The Booking model owns the seat-release rule in its deleting event.
        $booking->delete();

        return back()->with('success', 'à¸¥à¸šà¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸ªà¸³à¹€à¸£à¹‡à¸ˆ à¹à¸¥à¸°à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢à¹à¸¥à¹‰à¸§');
    }
}
