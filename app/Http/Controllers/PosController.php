<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    protected int $pricePerSeat = 110;

    public function index(Request $request)
    {
        $dateParam = $request->query('date');
        $selectedDate = $dateParam ? Carbon::parse($dateParam) : Carbon::today();
        $dateString = $selectedDate->toDateString();

        // ดึงรอบพิเศษ (ที่ผ่านการสร้างมาแล้ว) พร้อมหนังและการจอง
        $showtimes = Showtime::with(['movie', 'bookings'])
            ->whereHas('movie', function ($q) use ($dateString) {
                $q->active($dateString);
            })
            ->whereDate('show_date', $dateString)
            ->orderBy('show_time', 'asc')
            ->get();

        $prevDate = $selectedDate->copy()->subDay()->toDateString();
        $nextDate = $selectedDate->copy()->addDay()->toDateString();
        $isToday = $selectedDate->isToday();

        return view('pos.index', compact('showtimes', 'selectedDate', 'dateString', 'prevDate', 'nextDate', 'isToday'));
    }

    /**
     * ขายตั๋วด่วนหน้าร้าน (Walk-in Quick Checkout)
     */
    public function quickSell(Request $request)
    {
        $validated = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'quantity' => 'required|integer|min:1|max:160',
            'payment_method' => 'required|in:counter,qr_code',
            'booker_name' => 'nullable|string|max:255',
            'booker_phone' => 'nullable|string|max:20',
            'visitor_type' => 'nullable|in:individual,school,government,company',
        ]);

        return DB::transaction(function () use ($validated) {
            $showtime = Showtime::where('id', $validated['showtime_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $qty = (int) $validated['quantity'];

            if ($showtime->available_seats < $qty) {
                return back()->withErrors(['quantity' => "ที่นั่งไม่พอ เหลือ {$showtime->available_seats} ที่นั่ง"]);
            }

            // หาที่นั่งที่ถูกจองไปแล้ว
            $bookedSeats = Booking::where('showtime_id', $showtime->id)
                ->whereIn('status', ['pending', 'awaiting_payment', 'paid', 'redeemed'])
                ->pluck('seats')
                ->filter()
                ->flatten()
                ->all();

            // รายการที่นั่งทั้งหมด 160 ที่นั่ง (แถว A - I)
            $allSeats = [];
            $rows = [
                'A' => 16, 'B' => 16, 'C' => 18, 'D' => 18,
                'E' => 18, 'F' => 18, 'G' => 18, 'H' => 20, 'I' => 20
            ];
            foreach ($rows as $rowLetter => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $allSeats[] = $rowLetter . $i;
                }
            }

            // เลือกที่นั่งว่างลำดับแรกๆ
            $availableSeatsList = array_values(array_diff($allSeats, $bookedSeats));
            $assignedSeats = array_slice($availableSeatsList, 0, $qty);

            // ตัดจำนวนที่นั่งว่าง
            $showtime->decrement('available_seats', $qty);

            $bookerName = !empty($validated['booker_name']) ? trim($validated['booker_name']) : 'ลูกค้าหน้าร้าน (Walk-in)';
            $visitorType = $validated['visitor_type'] ?? 'individual';

            $booking = Booking::create([
                'showtime_id' => $showtime->id,
                'booker_name' => $bookerName,
                'booker_email' => 'pos@sci-rangsit.local',
                'booker_phone' => $validated['booker_phone'] ?? null,
                'visitor_type' => $visitorType,
                'quantity' => $qty,
                'seats' => $assignedSeats,
                'total_amount' => $this->pricePerSeat * $qty,
                'status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'qr_ticket_ref' => (string) Str::uuid(),
            ]);

            return redirect()->route('pos.receipt', $booking->id);
        });
    }

    public function scan()
    {
        return view('pos.scan');
    }

        public function verifyBooking(Request $request)
    {
        $ref = $request->input('ref');
        $cleanId = ltrim($ref, '#');
        $booking = Booking::with('showtime.movie')
            ->where('qr_payment_ref', $ref)
            ->orWhere('qr_ticket_ref', $ref)
            ->orWhere('id', is_numeric($cleanId) ? (int)$cleanId : 0)
            ->first();

        if (!$booking) {
            return redirect()->route('pos.scan')->withErrors(['ref' => 'ไม่พบข้อมูลการจองรหัสนี้']);
        }

        $qrPayload = \App\Services\PromptPayQr::generatePayload(
            env('PROMPTPAY_TARGET', '0800000000'),
            (float) $booking->total_amount
        );

        return view('pos.verify', compact('booking', 'qrPayload'));
    }

        public function confirmCheckin(Request $request, Booking $booking)
    {
        if (in_array($booking->status, ['pending', 'awaiting_payment'])) {
            $paymentMethod = $request->input('payment_method', 'cash');
            $booking->update([
                'payment_method' => $paymentMethod,
                'status' => 'redeemed',
                'checked_in_at' => now(),
            ]);
            return redirect()->route('pos.receipt', $booking->id)->with('success', 'ชำระเงินและออกตั๋วเรียบร้อยแล้ว');
        }

        if ($booking->status === 'paid' || $booking->status === 'redeemed') {
            $booking->update([
                'status' => 'redeemed',
                'checked_in_at' => now()
            ]);
            return redirect()->route('pos.receipt', $booking->id)->with('success', 'ออกตั๋วเรียบร้อยแล้ว');
        }
        
        return back()->withErrors(['error' => 'สถานะไม่ถูกต้อง ไม่สามารถดำเนินการได้']);
    }

    public function orders()
    {
        $bookings = Booking::with('showtime.movie')->latest()->paginate(20);
        return view('pos.orders', compact('bookings'));
    }

    public function reports()
    {
        // Daily Report and Sales logic could go here or separate routes
        $dailySales = Booking::whereIn('status', ['paid', 'redeemed'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as tickets'))
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();
            
        $paymentMethods = Booking::whereIn('status', ['paid', 'redeemed'])
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as tickets'))
            ->groupBy('payment_method')
            ->get();

        return view('pos.reports', compact('dailySales', 'paymentMethods'));
    }

    public function exportPdf()
    {
        $dailySales = Booking::whereIn('status', ['paid', 'redeemed'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as tickets'))
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();
            
        $paymentMethods = Booking::whereIn('status', ['paid', 'redeemed'])
            ->select('payment_method', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as tickets'))
            ->groupBy('payment_method')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pos.pdf_report', compact('dailySales', 'paymentMethods'));
        return $pdf->download('report.pdf');
    }

    public function exportCsv()
    {
        $dailySales = Booking::whereIn('status', ['paid', 'redeemed'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(id) as tickets'))
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        $csvData = "Date,Total Revenue,Tickets\n";
        foreach ($dailySales as $row) {
            $csvData .= "{$row->date},{$row->total},{$row->tickets}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="report.csv"');
    }

    /**
     * หน้าสลิปตั๋วความร้อน (Thermal 80mm / 58mm Receipt)
     */
    public function receipt(Booking $booking)
    {
        $booking->load(['showtime.movie']);
        return view('pos.receipt', compact('booking'));
    }
}
