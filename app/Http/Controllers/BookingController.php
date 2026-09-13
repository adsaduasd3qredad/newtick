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
    // à¸£à¸²à¸„à¸²à¸•à¹ˆà¸­à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡ (à¹ƒà¸Šà¹‰à¸£à¹ˆà¸§à¸¡à¸à¸±à¸™à¸—à¸±à¹‰à¸‡à¸«à¸™à¹‰à¸²à¹€à¸¥à¸·à¸­à¸à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹à¸¥à¸°à¸•à¸­à¸™à¸ªà¸£à¹‰à¸²à¸‡ booking à¸ˆà¸£à¸´à¸‡)
    protected int $pricePerSeat = 50;

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
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
        ]);

        $showtime = Showtime::with('movie')->findOrFail($validated['showtime_id']);

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
        // ðŸ“Œ à¸›à¸£à¸±à¸š max à¸ˆà¸²à¸ 10 à¹€à¸›à¹‡à¸™ 160 à¹€à¸Šà¹ˆà¸™à¸à¸±à¸™
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
            'seats' => 'required|array|min:1',
            'seats.*' => 'string|max:10',
            'school_name' => 'nullable|string|max:255',
            'school_type' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'teachers_count' => 'nullable|integer|min:0',
            'students_count' => 'nullable|integer|min:0',
            'parents_count' => 'nullable|integer|min:0',
            'gov_agency_name' => 'nullable|string|max:255',
            'gov_department' => 'nullable|string|max:255',
            'gov_officers_count' => 'nullable|integer|min:0',
        ]);

        if ($validated['visitor_type'] === 'individual' && $validated['quantity'] > 10) {
            return back()->withErrors(['quantity' => 'à¹à¸šà¸šà¸šà¸¸à¸„à¸„à¸¥à¸—à¸±à¹ˆà¸§à¹„à¸›à¸ˆà¸­à¸‡à¹„à¸”à¹‰à¸ªà¸¹à¸‡à¸ªà¸¸à¸” 10 à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸•à¹ˆà¸­à¸à¸²à¸£à¸—à¸³à¸£à¸²à¸¢à¸à¸²à¸£']);
        }

        if (count($validated['seats']) !== (int) $validated['quantity']) {
            return back()->withErrors(['seats' => 'à¸ˆà¸³à¸™à¸§à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸—à¸µà¹ˆà¹€à¸¥à¸·à¸­à¸à¹„à¸¡à¹ˆà¸•à¸£à¸‡à¸à¸±à¸šà¸ˆà¸³à¸™à¸§à¸™à¸—à¸µà¹ˆà¸£à¸°à¸šà¸¸à¹„à¸§à¹‰']);
        }

        $visitorDetails = [];
        if ($validated['visitor_type'] === 'school') {
            $visitorDetails = [
                'school_name' => $validated['school_name'] ?? null,
                'school_type' => $validated['school_type'] ?? null,
                'education_level' => $validated['education_level'] ?? null,
                'teachers_count' => $validated['teachers_count'] ?? 0,
                'students_count' => $validated['students_count'] ?? 0,
                'parents_count' => $validated['parents_count'] ?? 0,
            ];
        } elseif ($validated['visitor_type'] === 'government') {
            $visitorDetails = [
                'gov_agency_name' => $validated['gov_agency_name'] ?? null,
                'gov_department' => $validated['gov_department'] ?? null,
                'gov_officers_count' => $validated['gov_officers_count'] ?? 0,
            ];
        }

        $this->cleanupExpiredBookings($validated['showtime_id']);

        return DB::transaction(function () use ($validated, $visitorDetails) {
            // à¸¥à¹‡à¸­à¸ row à¸‚à¸­à¸‡ showtime à¸™à¸µà¹‰à¹„à¸§à¹‰à¸à¹ˆà¸­à¸™ à¸à¸±à¸™à¸„à¸™à¸­à¸·à¹ˆà¸™à¸ˆà¸­à¸‡à¸žà¸£à¹‰à¸­à¸¡à¸à¸±à¸™
            $showtime = Showtime::where('id', $validated['showtime_id'])
                ->lockForUpdate()
                ->firstOrFail();

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
                'total_amount' => $this->pricePerSeat * $validated['quantity'],
                'status' => 'pending',
                'qr_ticket_ref' => (string) Str::uuid(),
                'expires_at' => now()->addMinutes(30),
            ]);

            return redirect()->route('bookings.payment', $booking->id);
        });
    }

    public function payment(Booking $booking)
    {
        if ($booking->status !== 'pending' && $booking->status !== 'awaiting_payment') {
            return redirect()->route('bookings.confirmed', $booking->id);
        }

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);
            if ($booking->showtime) {
                $booking->showtime->increment('available_seats', $booking->quantity);
            }
            return redirect()->route('showtimes.index')->with('error', 'à¸à¸²à¸£à¸ˆà¸­à¸‡à¸«à¸¡à¸”à¸­à¸²à¸¢à¸¸à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸—à¸³à¸£à¸²à¸¢à¸à¸²à¸£à¹ƒà¸«à¸¡à¹ˆ');
        }

        $qrPayload = PromptPayQr::generatePayload(
            env('PROMPTPAY_TARGET', '0800000000'),
            (float) $booking->total_amount
        );

        return view('bookings.payment', compact('booking', 'qrPayload'));
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:qr_code,counter',
        ]);

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);
            if ($booking->showtime) {
                $booking->showtime->increment('available_seats', $booking->quantity);
            }
            return back()->withErrors(['expired' => 'à¸«à¸¡à¸”à¹€à¸§à¸¥à¸²à¸à¸²à¸£à¸ˆà¸­à¸‡à¹à¸¥à¹‰à¸§ à¸à¸£à¸¸à¸“à¸²à¸ˆà¸­à¸‡à¹ƒà¸«à¸¡à¹ˆ']);
        }

        $booking->update([
            'payment_method' => $request->payment_method,
            'status' => 'awaiting_payment',
            'qr_payment_ref' => (string) Str::uuid(),
        ]);

        return redirect()->route('bookings.confirmed', $booking->id);
    }

    public function confirmed(Booking $booking)
    {
        $booking->load(['showtime.movie']);
        return view('bookings.confirmed', compact('booking'));
    }

    /**
     * à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸­à¸±à¸•à¹‚à¸™à¸¡à¸±à¸•à¸´à¸ªà¸³à¸«à¸£à¸±à¸šà¸£à¸²à¸¢à¸à¸²à¸£à¸—à¸µà¹ˆà¸«à¸¡à¸”à¹€à¸§à¸¥à¸²
     */
    protected function cleanupExpiredBookings(?int $showtimeId = null): void
    {
        $query = Booking::whereIn('status', ['pending', 'awaiting_payment'])
            ->where('expires_at', '<', now());

        if ($showtimeId) {
            $query->where('showtime_id', $showtimeId);
        }

        $expiredBookings = $query->get();
        foreach ($expiredBookings as $expired) {
            if ($expired->showtime) {
                $expired->showtime->increment('available_seats', $expired->quantity);
            }
            $expired->update(['status' => 'expired']);
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
        $query = trim($request->input('q', ''));
        $bookings = collect();

        if ($query !== '') {
            $cleanId = ltrim($query, '#');
            $bookings = Booking::where('booker_phone', 'like', "%{$query}%")
                ->orWhere('booker_email', 'like', "%{$query}%")
                ->orWhere('id', is_numeric($cleanId) ? (int)$cleanId : 0)
                ->orWhere('qr_ticket_ref', $query)
                ->with(['showtime.movie'])
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();
        }

        return view('bookings.search', compact('bookings', 'query'));
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // 1. à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¸à¹ˆà¸­à¸™à¸¥à¸š
        if ($booking->showtime && in_array($booking->status, ['pending', 'awaiting_payment', 'paid'])) {
            $booking->showtime->increment('available_seats', $booking->quantity);
        }

        // 2. à¸¥à¸šà¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸à¸²à¸£à¸ˆà¸­à¸‡
        $booking->delete();

        return back()->with('success', 'à¸¥à¸šà¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸ªà¸³à¹€à¸£à¹‡à¸ˆ à¹à¸¥à¸°à¸„à¸·à¸™à¸—à¸µà¹ˆà¸™à¸±à¹ˆà¸‡à¹€à¸£à¸µà¸¢à¸šà¸£à¹‰à¸­à¸¢à¹à¸¥à¹‰à¸§');
    }
}

