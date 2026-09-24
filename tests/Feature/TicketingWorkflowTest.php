<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\User;
use App\Models\WeeklySchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TicketingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['ticketing.price_per_seat' => 50, 'ticketing.qr_payment_fee' => 10]);
        Carbon::setTestNow(Carbon::parse('2026-09-23 08:00:00', 'Asia/Bangkok'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_online_booking_reserves_seats_and_online_payment_waits_for_review(): void
    {
        Storage::fake('public');
        $showtime = $this->showtime();

        $response = $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();

        $response->assertRedirect(route('bookings.payment', $booking->qr_ticket_ref));
        $this->assertSame(159, $showtime->fresh()->available_seats);
        $this->assertSame(['A1'], $booking->seats);
        $this->assertSame('pending', $booking->status);
        $this->assertSame('50.00', $booking->total_amount);

        $this->get(route('bookings.payment', $booking->qr_ticket_ref))
            ->assertOk()
            ->assertSee('60.00');

        $this->post(route('bookings.confirm', $booking->qr_ticket_ref), [
            'payment_method' => 'qr_code',
            'has_slip' => '1',
            'payment_slip' => UploadedFile::fake()->image('slip.png'),
        ])->assertRedirect(route('bookings.confirmed', $booking->qr_ticket_ref));

        $booking->refresh();
        $this->assertSame('awaiting_payment', $booking->status);
        $this->assertNull($booking->amount_paid);
        $this->assertEquals(10, (float) $booking->payment->transaction_fee);
        $this->assertTrue(Storage::disk('public')->exists($booking->payment->slip_path));
    }

    public function test_counter_reservation_expires_thirty_minutes_after_showtime_and_returns_seats(): void
    {
        $showtime = $this->showtime('2026-09-23', '10:00:00');
        $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();

        $this->post(route('bookings.confirm', $booking->qr_ticket_ref), [
            'payment_method' => 'counter',
        ])->assertRedirect(route('bookings.confirmed', $booking->qr_ticket_ref));

        $booking->refresh();
        $this->assertSame('awaiting_payment', $booking->status);
        $this->assertSame('2026-09-23 10:30:00', $booking->expires_at->timezone('Asia/Bangkok')->format('Y-m-d H:i:s'));

        Carbon::setTestNow(Carbon::parse('2026-09-23 10:31:00', 'Asia/Bangkok'));
        $this->artisan('bookings:cancel-expired')->assertSuccessful();
        $this->artisan('bookings:cancel-expired')->assertSuccessful();

        $this->assertSame('expired', $booking->fresh()->status);
        $this->assertSame(160, $showtime->fresh()->available_seats);
    }

    public function test_duplicate_seat_codes_are_rejected(): void
    {
        $showtime = $this->showtime();

        $this->from('/')->post(route('bookings.store'), [
            ...$this->bookingInput($showtime),
            'quantity' => 2,
            'seats' => ['A1', 'A1'],
        ])->assertSessionHasErrors('seats.1');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertSame(160, $showtime->fresh()->available_seats);
    }

    public function test_repeated_public_payment_confirmation_cannot_downgrade_a_paid_booking(): void
    {
        Storage::fake('public');
        $showtime = $this->showtime();
        $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();
        $this->actingAs(User::factory()->create(['role' => 'staff']));

        $this->post(route('pos.confirm-checkin', $booking), ['payment_method' => 'counter']);
        $booking->refresh();
        $this->assertSame('paid', $booking->status);
        $amountPaid = $booking->amount_paid;

        $this->post(route('bookings.confirm', $booking->qr_ticket_ref), [
            'payment_method' => 'qr_code',
            'payment_slip' => UploadedFile::fake()->image('slip.png'),
        ])->assertRedirect(route('bookings.confirmed', $booking->qr_ticket_ref));

        $this->assertSame('paid', $booking->fresh()->status);
        $this->assertSame($amountPaid, $booking->fresh()->amount_paid);
    }

    public function test_deleting_paid_booking_does_not_make_its_seats_available_again(): void
    {
        $showtime = $this->showtime();
        $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();
        $booking->update(['status' => 'paid']);

        $booking->delete();

        $this->assertSame(159, $showtime->fresh()->available_seats);
    }

    public function test_pos_cash_sale_is_paid_before_ticket_checkin_and_repeat_checkin_is_blocked(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $showtime = $this->showtime('2026-09-23', '10:00:00');
        $this->actingAs($staff);

        $this->post(route('pos.quick-sell'), [
            'showtime_id' => $showtime->id,
            'quantity' => 1,
            'payment_method' => 'counter',
        ])->assertRedirect();

        $booking = Booking::firstOrFail();
        $this->assertSame('paid', $booking->status);
        $this->assertSame('50.00', $booking->amount_paid);
        $this->assertEquals(0, (float) $booking->payment->transaction_fee);
        $this->assertNull($booking->checked_in_at);

        $this->post(route('checkin.process'), ['qr_ticket_ref' => $booking->qr_ticket_ref])
            ->assertOk();
        $this->assertSame('redeemed', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->checked_in_at);

        $this->from('/checkin')->post(route('checkin.process'), ['qr_ticket_ref' => $booking->qr_ticket_ref])
            ->assertRedirect('/checkin')
            ->assertSessionHasErrors('qr_ticket_ref');
    }

    public function test_started_showtime_is_closed_for_pos_and_direct_staff_booking(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $showtime = $this->showtime('2026-09-23', '07:00:00');
        $this->actingAs($staff);

        $this->get(route('pos.index', ['date' => '2026-09-23']))
            ->assertOk()
            ->assertSee('bg-slate-200 text-slate-600');

        $this->get(route('bookings.create', ['showtime' => $showtime->id, 'staff' => 1]))
            ->assertRedirect(route('showtimes.index'));

        $this->from(route('pos.index', ['date' => '2026-09-23']))
            ->post(route('pos.quick-sell'), [
                'showtime_id' => $showtime->id,
                'quantity' => 1,
                'payment_method' => 'counter',
            ])
            ->assertSessionHasErrors('showtime');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertSame(160, $showtime->fresh()->available_seats);
    }

    public function test_same_movie_slot_is_reused_but_a_different_movie_gets_its_own_showtime(): void
    {
        $firstMovie = $this->showtime('2026-09-24', '10:00:00')->movie;
        $firstSchedule = WeeklySchedule::create([
            'movie_id' => $firstMovie->id,
            'day_of_week' => 4,
            'show_time' => '10:00:00',
            'total_seats' => 160,
        ]);
        $query = ['date' => '2026-09-24', 'time' => '10:00:00', 'weekly_id' => $firstSchedule->id];

        $this->get(route('bookings.create', $query))->assertOk();
        $firstShowtimeId = Showtime::firstOrFail()->id;
        $this->get(route('bookings.create', $query))->assertOk();
        $this->assertSame(1, Showtime::count());

        $secondMovie = Movie::create([
            'title_th' => 'ภาพยนตร์อีกเรื่อง',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
        $secondSchedule = WeeklySchedule::create([
            'movie_id' => $secondMovie->id,
            'day_of_week' => 4,
            'show_time' => '10:00:00',
            'total_seats' => 160,
        ]);

        $this->get(route('bookings.create', [
            'date' => '2026-09-24',
            'time' => '10:00:00',
            'weekly_id' => $secondSchedule->id,
        ]))->assertOk();

        $this->assertSame(2, Showtime::count());
        $this->assertSame($firstMovie->id, Showtime::findOrFail($firstShowtimeId)->movie_id);
        $this->assertSame(2, Showtime::whereDate('show_date', '2026-09-24')->whereTime('show_time', '10:00:00')->count());
    }

    public function test_pos_can_collect_cash_for_an_existing_online_reservation_before_checkin(): void
    {
        $showtime = $this->showtime();
        $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();
        $this->actingAs(User::factory()->create(['role' => 'staff']));

        $this->post(route('pos.confirm-checkin', $booking), [
            'payment_method' => 'counter',
        ])->assertRedirect(route('pos.receipt', $booking->id));

        $booking->refresh();
        $this->assertSame('paid', $booking->status);
        $this->assertNull($booking->checked_in_at);
        $this->assertSame('50.00', $booking->amount_paid);
        $this->assertEquals(0, (float) $booking->payment->transaction_fee);

        $this->post(route('pos.confirm-checkin', $booking))
            ->assertRedirect(route('pos.receipt', $booking->id));
        $this->assertSame('redeemed', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->checked_in_at);
    }

    public function test_admin_dashboard_renders_stats_and_revenue_chart_without_sales(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin')
            ->assertOk()
            ->assertSee('StatsOverview', false)
            ->assertSee('RevenueTrendChart', false);
    }

    public function test_revenue_chart_widget_renders_with_empty_sales_data(): void
    {
        Livewire::test(\App\Filament\Widgets\RevenueTrendChart::class)
            ->assertSee('รายรับรายวัน')
            ->assertSee('2026-09')
            ->assertSee('2026-08')
            ->set('filter', '2026-08')
            ->assertSet('filter', '2026-08');
    }

    public function test_group_booking_counts_are_recalculated_and_saved_for_school_and_government(): void
    {
        $groupBookings = [
            [
                'type' => 'school',
                'details' => [
                    'school_name' => 'โรงเรียนทดสอบ',
                    'school_type' => 'in_system',
                    'education_level' => 'secondary',
                    'teachers_count' => 5,
                    'students_count' => 50,
                    'parents_count' => 0,
                ],
                'expected' => 55,
            ],
            [
                'type' => 'government',
                'details' => [
                    'gov_agency_name' => 'หน่วยงานทดสอบ',
                    'gov_department' => 'ฝ่ายทดสอบ',
                    'gov_officers_count' => 5,
                    'gov_staff_count' => 4,
                    'gov_others_count' => 3,
                ],
                'expected' => 12,
            ],
        ];

        foreach ($groupBookings as $index => $group) {
            $showtime = $this->showtime('2026-09-' . (24 + $index), '10:00:00');
            $this->get(route('bookings.create', $showtime->id))
                ->assertOk()
                ->assertDontSee('value="company"', false)
                ->assertDontSee('company_employees_count', false)
                ->assertSee('estimated-total', false);

            $input = [
                ...$this->bookingInput($showtime),
                'visitor_type' => $group['type'],
                'quantity' => 1, // The server must calculate from group counts.
                ...$group['details'],
            ];

            $this->post(route('bookings.seats'), $input)
                ->assertOk()
                ->assertViewHas('quantity', $group['expected'])
                ->assertViewHas('visitorDetails', $group['details']);

            $input['seats'] = $this->seatCodes($group['expected']);
            $response = $this->post(route('bookings.store'), $input);
            $booking = Booking::where('showtime_id', $showtime->id)->firstOrFail();

            $response->assertRedirect(route('bookings.payment', $booking->qr_ticket_ref));
            $this->assertSame($group['expected'], $booking->quantity);
            $this->assertSame($group['expected'] * 50, (int) $booking->total_amount);
            $this->assertSame($group['details'], $booking->visitor_details);
        }
    }

    public function test_guest_cannot_mark_online_payment_as_paid_by_adding_staff_flag(): void
    {
        $showtime = $this->showtime();
        $this->post(route('bookings.store'), $this->bookingInput($showtime));
        $booking = Booking::firstOrFail();

        $this->post(route('bookings.confirm', ['booking' => $booking->qr_ticket_ref, 'staff' => 1]), [
            'payment_method' => 'counter',
        ])->assertRedirect(route('bookings.confirmed', $booking->qr_ticket_ref));

        $this->assertSame('awaiting_payment', $booking->fresh()->status);
        $this->assertNull($booking->fresh()->amount_paid);
    }

    private function showtime(string $date = '2026-09-24', string $time = '10:00:00'): Showtime
    {
        $movie = Movie::create([
            'title_th' => 'ภาพยนตร์ทดสอบ',
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        return Showtime::create([
            'movie_id' => $movie->id,
            'show_date' => $date,
            'show_time' => $time,
            'total_seats' => 160,
            'available_seats' => 160,
        ]);
    }

    private function bookingInput(Showtime $showtime): array
    {
        return [
            'showtime_id' => $showtime->id,
            'booker_name' => 'Test User',
            'booker_email' => 'test@example.com',
            'booker_phone' => '0812345678',
            'visitor_type' => 'individual',
            'quantity' => 1,
            'seats' => ['A1'],
        ];
    }

    private function seatCodes(int $quantity): array
    {
        $seats = [];
        $rowSizes = ['A' => 16, 'B' => 16, 'C' => 18, 'D' => 18, 'E' => 18, 'F' => 18, 'G' => 18, 'H' => 20, 'I' => 20];

        foreach ($rowSizes as $row => $count) {
            for ($number = 1; $number <= $count && count($seats) < $quantity; $number++) {
                $seats[] = $row . $number;
            }
        }

        return $seats;
    }
}
