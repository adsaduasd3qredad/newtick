@extends('pos.layout')
@section('title', 'รอบฉาย')
@section('content')
    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">

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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($showtimes as $showtime)
                @php
                    $movieTitle = $showtime->movie->title_th ?? $showtime->movie->title ?? '';

                    if (str_contains($movieTitle, 'แรงโน้มถ่วง')) {
                        $imageName = 'gravity.jpg';
                    } elseif (str_contains($movieTitle, 'Cosmos') || str_contains($movieTitle, 'คอสโมส')) {
                        $imageName = 'cosmos.jpg';
                    } elseif (str_contains($movieTitle, 'Polaris') || str_contains($movieTitle, 'ดาวเหนือ')) {
                        $imageName = 'polaris.jpg';
                    } elseif (
                        str_contains($movieTitle, 'World') ||
                        str_contains($movieTitle, 'นอกโลก') ||
                        str_contains($movieTitle, 'สุริยะ')
                    ) {
                        $imageName = 'worldbeyon.jpg';
                    } elseif (str_contains($movieTitle, 'Earth') || str_contains($movieTitle, 'จักรวาล')) {
                        $imageName = 'earthuniverse.jpg';
                    } elseif (str_contains($movieTitle, 'Life') || str_contains($movieTitle, 'ชีวิต')) {
                        $imageName = 'life.jpg';
                    } elseif (str_contains($movieTitle, 'Lucia') || str_contains($movieTitle, 'ลูเซีย')) {
                        $imageName = 'lucia.jpg';
                    } elseif (str_contains($movieTitle, 'Oddy') || str_contains($movieTitle, 'ออดี้')) {
                        $imageName = 'oddy.jpg';
                    } elseif (str_contains($movieTitle, 'Solar')) {
                        $imageName = 'solar.jpg';
                    } elseif (str_contains($movieTitle, 'Star') || str_contains($movieTitle, 'ดวงดาว')) {
                        $imageName = 'star.jpg';
                    } elseif (str_contains($movieTitle, 'Dancing') || str_contains($movieTitle, 'เต้น')) {
                        $imageName = 'dancing.jpg';
                    } else {
                        $imageName = 'gravity.jpg';
                    }

                    $totalSeats = $showtime->total_seats ?? 160;
                    $availableSeats = $showtime->available_seats;
                    $bookedSeats = $totalSeats - $availableSeats;
                    $isSoldOut = $availableSeats <= 0;
                    $percentBooked = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100) : 0;
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md hover:border-cyan-300 transition flex flex-col overflow-hidden">
                    
                    <!-- Showtime Card Body -->
                    <div class="p-5 flex gap-4 flex-1">
                        <!-- Poster Image -->
                        <div class="w-24 h-32 shrink-0 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-slate-100">
                            <img src="{{ asset('images/' . $imageName) }}"
                                alt="{{ $movieTitle }}"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Info -->
                        <div class="flex-1 flex flex-col justify-between min-w-0">
                            <div>
                                <!-- Time Badge -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-slate-800 bg-slate-100 rounded-lg font-display">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }} น.</span>
                                    </span>
                                    @if ($isSoldOut)
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
                                <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2" title="{{ $movieTitle }}">
                                    {{ $movieTitle ?: 'ไม่ระบุชื่อภาพยนตร์' }}
                                </h3>

                                @if ($showtime->movie && $showtime->movie->duration_minutes)
                                    <p class="text-xs text-slate-400 mt-1">
                                        ความยาว {{ $showtime->movie->duration_minutes }} นาที
                                    </p>
                                @endif
                            </div>

                            <!-- Seat Details & Bar -->
                            <div class="mt-3">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>ที่นั่งว่าง: <strong class="{{ $isSoldOut ? 'text-rose-600' : 'text-cyan-600' }} font-semibold">{{ $availableSeats }}</strong> / {{ $totalSeats }}</span>
                                    <span>{{ $percentBooked }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-cyan-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $percentBooked }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-xs text-slate-400 font-mono">
                            รอบ #{{ $showtime->id }}
                        </span>

                        @if ($isSoldOut)
                            <button disabled class="bg-slate-300 text-slate-500 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed">
                                ที่นั่งเต็มแล้ว
                            </button>
                        @else
                            <a href="{{ route('bookings.create', [$showtime->id, 'staff' => 1]) }}"
                                title="เลือกที่นั่งในโดมเอง"
                                class="inline-flex items-center gap-1.5 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-xs transition">
                                <span>เลือกที่นั่งและขายตั๋ว</span>
                                <span aria-hidden="true">→</span>
                            </a>
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

    @if(false)
    <!-- Legacy Quick Sell markup retained for reference; the active POS flow uses seat selection. -->
    <div id="quickSellModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-display font-bold text-lg text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>ขายตั๋วด่วนหน้าร้าน (Quick Sell)</span>
                </h3>
                <button type="button" onclick="closeQuickSellModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">
                    ✕
                </button>
            </div>

            <form action="{{ route('pos.quick-sell') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="showtime_id" id="modal_showtime_id">

                <!-- Movie Info in modal -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs">
                    <p class="font-bold text-slate-900 text-sm font-display" id="modal_movie_title">-</p>
                    <p class="text-slate-500 mt-0.5">
                        รอบเวลา: <strong class="text-cyan-700" id="modal_show_time">-</strong> น.
                        &bull; ว่าง: <strong class="text-emerald-600" id="modal_avail_seats">-</strong> ที่นั่ง
                    </p>
                </div>

                <!-- Quantity Quick Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">จำนวนที่นั่ง (ใบ)</label>
                    <div class="grid grid-cols-5 gap-2 mb-2">
                        <button type="button" onclick="setQuantity(1)" class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 text-slate-800 transition">1</button>
                        <button type="button" onclick="setQuantity(2)" class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 text-slate-800 transition">2</button>
                        <button type="button" onclick="setQuantity(3)" class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 text-slate-800 transition">3</button>
                        <button type="button" onclick="setQuantity(4)" class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 text-slate-800 transition">4</button>
                        <button type="button" onclick="setQuantity(5)" class="py-2 text-xs font-bold rounded-xl border border-slate-200 hover:border-cyan-500 hover:bg-cyan-50 text-slate-800 transition">5</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="quantity" id="modal_quantity" min="1" max="160" value="1" required
                            oninput="updateModalTotal()"
                            class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-bold focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">ช่องทางชำระเงิน</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 transition">
                            <input type="radio" name="payment_method" value="counter" checked class="text-cyan-600 focus:ring-cyan-500">
                            <span class="text-xs font-semibold text-slate-800">เงินสด (Cash)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:border-cyan-400 transition">
                            <input type="radio" name="payment_method" value="qr_code" class="text-cyan-600 focus:ring-cyan-500">
                            <span class="text-xs font-semibold text-slate-800">PromptPay QR</span>
                        </label>
                    </div>
                </div>

                <!-- Optional Customer Name / Phone -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">ชื่อลูกค้า (ถ้ามี)</label>
                        <input type="text" name="booker_name" placeholder="Walk-in"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">เบอร์โทร (ถ้ามี)</label>
                        <input type="text" name="booker_phone" placeholder="08xxxxxxxx"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800">
                    </div>
                </div>

                <!-- Total Amount Summary -->
                <div class="bg-cyan-50 p-4 rounded-2xl border border-cyan-200 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-cyan-800 font-semibold block">ยอดชำระสุทธิ</span>
                        <span class="text-[11px] text-cyan-600">ตั๋วใบละ 110 ฿ (จัดที่นั่งอัตโนมัติ)</span>
                    </div>
                    <div class="text-right">
                        <span class="font-display font-bold text-xl text-cyan-900" id="modal_total_amount">110.00 ฿</span>
                    </div>
                </div>

                <!-- Action Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-md transition duration-200 text-sm font-display flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>บันทึกการขาย & พิมพ์สลิปความร้อน</span>
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

        let currentMaxSeats = 160;

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

        function updateModalTotal() {
            const qty = parseInt(quantityInput.value) || 0;
            const total = qty * 110;
            totalAmountEl.textContent = total.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ฿';
        }
    </script>


    @endif

@endsection
