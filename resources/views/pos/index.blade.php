@extends('pos.layout')
@section('title', 'รอบฉาย')
@section('content')
    <!-- Main Container -->
    <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">

        <!-- Date Bar & Controls -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800">
                        รอบฉายวันที่: {{ $selectedDate->locale('th')->translatedFormat('j F Y') }}
                    </h2>
                    @if ($isToday)
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full">
                            วันนี้
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    พบทั้งหมด <strong class="text-slate-800 font-semibold">{{ $showtimes->count() }}</strong> รอบฉาย
                    @if ($showtimes->isNotEmpty())
                        | ที่นั่งว่างรวม <strong class="text-cyan-600 font-semibold">{{ $showtimes->sum('available_seats') }}</strong> ที่นั่ง
                    @endif
                </p>
            </div>

            <!-- Date Navigation Buttons -->
            <div class="flex items-center flex-wrap gap-2">
                <a href="{{ route('pos.index', ['date' => $prevDate]) }}"
                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                    ‹ วันก่อนหน้า
                </a>

                @if (!$isToday)
                    <a href="{{ route('pos.index') }}"
                        class="px-3.5 py-2 text-xs font-medium text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-xl transition">
                        วันนี้
                    </a>
                @endif

                <a href="{{ route('pos.index', ['date' => $nextDate]) }}"
                    class="inline-flex items-center gap-1 px-3.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                    วันถัดไป ›
                </a>

                <!-- Date Picker Input -->
                <form action="{{ route('pos.index') }}" method="GET" class="inline-flex items-center">
                    <input type="date" name="date" value="{{ $dateString }}"
                        onchange="this.form.submit()"
                        class="text-xs py-1.5 px-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-200 text-slate-700 font-medium">
                </form>
            </div>
        </div>

        <!-- Showtime Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
            @forelse($showtimes as $showtime)
                @php
                    $movieTitle = $showtime->movie->title_th ?? $showtime->movie->title ?? '';
                    $totalSeats = $showtime->total_seats ?? 160;
                    $availableSeats = $showtime->available_seats;
                    $bookedSeats = $totalSeats - $availableSeats;
                    $isSoldOut = $availableSeats <= 0;
                    $isPastShowtime = ! $showtime->isBookable();
                    $percentBooked = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100) : 0;
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-cyan-300 transition flex flex-col overflow-hidden">
                    
                    <!-- Showtime Card Body -->
                    <div class="p-6 flex gap-6 flex-1">
                        <!-- Poster Image -->
                        <div class="w-36 h-52 sm:w-40 sm:h-56 2xl:w-44 2xl:h-60 shrink-0 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-slate-100">
                            @if ($showtime->movie?->poster_path)
                                <img src="{{ Storage::url($showtime->movie->poster_path) }}"
                                    alt="{{ $movieTitle }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">
                                    ไม่มีโปสเตอร์
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 flex flex-col justify-between min-w-0">
                            <div>
                                <!-- Time Badge -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-bold text-slate-800 bg-slate-100 rounded-lg font-display">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }} น.</span>
                                    </span>
                                    @if ($isPastShowtime)
                                        <span class="px-2 py-1 text-[11px] font-semibold bg-slate-200 text-slate-600 rounded-md">
                                            เริ่มฉายแล้ว · ปิดขาย
                                        </span>
                                    @elseif ($isSoldOut)
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-rose-100 text-rose-700 rounded-md">
                                            เต็ม
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-emerald-50 text-emerald-700 rounded-md">
                                            เปิดขาย
                                        </span>
                                    @endif
                                </div>

                                <!-- Movie Title -->
                                <h3 class="font-bold text-slate-900 text-xl 2xl:text-2xl leading-snug line-clamp-2" title="{{ $movieTitle }}">
                                    {{ $movieTitle ?: 'ไม่ระบุชื่อภาพยนตร์' }}
                                </h3>

                                @if ($showtime->movie && $showtime->movie->duration_minutes)
                                    <p class="text-sm text-slate-500 mt-2">
                                        ความยาว {{ $showtime->movie->duration_minutes }} นาที
                                    </p>
                                @endif
                            </div>

                            <!-- Seat Details & Bar -->
                            <div class="mt-3">
                                <div class="flex justify-between text-sm text-slate-500 mb-2">
                                    <span>ที่นั่งว่าง: <strong class="{{ $isSoldOut ? 'text-rose-600' : 'text-cyan-600' }} font-semibold">{{ $availableSeats }}</strong> / {{ $totalSeats }}</span>
                                    <span>{{ $percentBooked }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-cyan-500 h-1.5 rounded-full transition-all duration-300" style="--percent: {{ $percentBooked }}%; width: var(--percent);"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-xs text-slate-400 font-mono">
                            รอบ #{{ $showtime->id }}
                        </span>

                        @if ($isPastShowtime)
                            <button disabled class="bg-slate-200 text-slate-500 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed">
                                รอบฉายเริ่มแล้ว ปิดการขาย
                            </button>
                        @elseif ($isSoldOut)
                            <button disabled class="bg-slate-300 text-slate-500 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed">
                                ที่นั่งเต็มแล้ว
                            </button>
                        @else
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    data-id="{{ $showtime->id }}"
                                    data-title="{{ $movieTitle }}"
                                    data-time="{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}"
                                    data-avail="{{ $availableSeats }}"
                                    onclick="handleQuickSellClick(this)"
                                    class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm hover:shadow transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Walk-in</span>
                                </button>

                                <a href="{{ route('bookings.create', [$showtime->id, 'staff' => 1]) }}"
                                    title="เลือกที่นั่งในโดมเอง"
                                    class="inline-flex items-center gap-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 px-3 py-2 rounded-xl text-xs font-medium transition">
                                    <span>เลือกที่นั่ง</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-slate-700 text-lg font-bold">ไม่มีรอบฉายภาพยนตร์ในวันที่เลือก</h3>
                    <p class="text-sm text-slate-400 mt-1 max-w-md mx-auto">
                        สามารถกดปุ่ม "สร้างรอบฉายอัตโนมัติจากสัปดาห์นี้" ที่ระบบหลังบ้าน (Admin -> รอบฉาย) หรือเพิ่มรอบฉายด้วยตนเอง
                    </p>

                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('pos.index') }}"
                            class="px-4 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            กลับไปดูรอบฉายวันนี้
                        </a>
                        @if (auth()->check() && auth()->user()->role === 'admin')
                            <a href="{{ url('/admin/showtimes') }}"
                                class="px-4 py-2 text-xs font-medium text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition shadow-sm">
                                ไปหน้ารอบฉาย (Admin)
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Quick Sell Modal for Walk-in Customers -->
    <div id="quickSellModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-display font-bold text-lg text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                    <span>ขายตั๋วด่วน (Walk-in)</span>
                </h3>
                <button type="button" onclick="closeQuickSellModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1 rounded-lg hover:bg-slate-100 transition">
                    ✕
                </button>
            </div>

            <form action="{{ route('pos.quick-sell') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="showtime_id" id="modal_showtime_id">

                <!-- Movie Info in modal -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs">
                    <p class="font-bold text-slate-900 text-sm font-display" id="modal_movie_title">-</p>
                    <p class="text-slate-500 mt-1 flex items-center gap-3">
                        <span>รอบเวลา: <strong class="text-cyan-700 font-bold" id="modal_show_time">-</strong> น.</span>
                        <span>ว่าง: <strong class="text-emerald-600 font-bold" id="modal_avail_seats">-</strong> ที่นั่ง</span>
                    </p>
                </div>

                <!-- Quantity Quick Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">จำนวนตั๋ว (ที่นั่ง)</label>
                    <div class="grid grid-cols-6 gap-1.5 mb-2">
                        @foreach([1, 2, 3, 4, 5, 10] as $q)
                            <button type="button" onclick="setQuantity(Number(this.textContent.trim()))"
                                class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 text-slate-800 transition">
                                {{ $q }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="stepQuantity(-1)" class="w-10 h-10 rounded-xl border border-slate-200 hover:bg-slate-100 font-bold text-slate-700 flex items-center justify-center text-lg">-</button>
                        <input type="number" name="quantity" id="modal_quantity" min="1" max="160" value="1" required
                            oninput="updateModalTotal()"
                            class="flex-1 text-center bg-white border border-slate-300 rounded-xl px-3 py-2 text-base text-slate-800 font-bold focus:ring-2 focus:ring-emerald-500">
                        <button type="button" onclick="stepQuantity(1)" class="w-10 h-10 rounded-xl border border-slate-200 hover:bg-slate-100 font-bold text-slate-700 flex items-center justify-center text-lg">+</button>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">ช่องทางชำระเงิน</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-emerald-300 bg-emerald-50/50 cursor-pointer hover:border-emerald-500 transition">
                            <input type="radio" name="payment_method" value="counter" checked class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">💵 เงินสด (Cash)</span>
                                <span class="text-[10px] text-slate-500">ไม่มีค่าธรรมเนียม</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 transition">
                            <input type="radio" name="payment_method" value="qr_code" class="text-cyan-600 focus:ring-cyan-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">📱 PromptPay QR</span>
                                <span class="text-[10px] text-slate-500">+{{ config('ticketing.qr_payment_fee', 10) }}฿ ค่าธรรมเนียม</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Customer Name Info -->
                <div class="bg-amber-50/70 p-3 rounded-xl border border-amber-200 text-xs text-amber-900">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-[11px]">ชื่อผู้จอง (อัตโนมัติ):</span>
                        <span class="text-[11px] font-bold text-amber-700">Walk-in (รันเลขอัตโนมัติ)</span>
                    </div>
                    <p class="text-[11px] text-amber-700">ระบบจะสร้างชื่อ Walk-in 1, Walk-in 2... ให้อัตโนมัติ ไม่ต้องพิมพ์</p>
                    <div class="mt-2 pt-2 border-t border-amber-200/60">
                        <input type="text" name="booker_name" placeholder="ระบุชื่อเฉพาะกรณีลูกค้าแจ้ง (เว้นว่างไว้ได้)"
                            class="w-full bg-white border border-amber-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Total Amount Summary -->
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-emerald-800 font-semibold block">ยอดชำระสุทธิ</span>
                        <span class="text-[11px] text-emerald-600">ที่นั่งละ {{ number_format(config('ticketing.price_per_seat', 50), 2) }} ฿ (จัดที่นั่งให้อัตโนมัติ)</span>
                    </div>
                    <div class="text-right">
                        <span class="font-display font-bold text-2xl text-emerald-700" id="modal_total_amount">{{ number_format(config('ticketing.price_per_seat', 50), 2) }} ฿</span>
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-md hover:shadow-lg transition duration-200 text-base font-display flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>บันทึกการขาย & พิมพ์สลิปตั๋ว</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        const modal = document.getElementById('quickSellModal');
        const showtimeIdInput = document.getElementById('modal_showtime_id');
        const movieTitleEl = document.getElementById('modal_movie_title');
        const showTimeEl = document.getElementById('modal_show_time');
        const availSeatsEl = document.getElementById('modal_avail_seats');
        const quantityInput = document.getElementById('modal_quantity');
        const totalAmountEl = document.getElementById('modal_total_amount');
        const pricePerSeat = Number("{{ (int) config('ticketing.price_per_seat', 50) }}");

        let currentMaxSeats = 160;

        function handleQuickSellClick(btn) {
            openQuickSellModal(
                btn.getAttribute('data-id'),
                btn.getAttribute('data-title'),
                btn.getAttribute('data-time'),
                parseInt(btn.getAttribute('data-avail'), 10)
            );
        }

        function openQuickSellModal(id, title, time, availSeats) {
            showtimeIdInput.value = id;
            movieTitleEl.textContent = title;
            showTimeEl.textContent = time;
            availSeatsEl.textContent = availSeats;
            currentMaxSeats = availSeats;
            quantityInput.max = availSeats;
            quantityInput.value = 1;
            updateModalTotal();
            modal.classList.remove('hidden');
        }

        function closeQuickSellModal() {
            modal.classList.add('hidden');
        }

        function setQuantity(val) {
            if (val <= currentMaxSeats) {
                quantityInput.value = val;
                updateModalTotal();
            }
        }

        function stepQuantity(delta) {
            let val = (parseInt(quantityInput.value) || 1) + delta;
            if (val >= 1 && val <= currentMaxSeats) {
                quantityInput.value = val;
                updateModalTotal();
            }
        }

        function updateModalTotal() {
            const qty = parseInt(quantityInput.value) || 0;
            const total = qty * pricePerSeat;
            totalAmountEl.textContent = total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ฿';
        }

        // Close on ESC key or click outside
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeQuickSellModal();
            }
        });

        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeQuickSellModal();
                }
            });
        }
    </script>

@endsection
