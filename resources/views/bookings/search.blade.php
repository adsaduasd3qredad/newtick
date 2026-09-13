<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาตั๋วและตรวจสอบสถานะการจอง - ท้องฟ้าจำลองรังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }

        .font-display {
            font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- Top Announcement -->
    <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-700 text-white text-xs sm:text-sm font-medium py-2 px-4 text-center shadow-sm">
        <a href="https://sci-rangsit.dole.go.th" target="_blank"
            class="hover:underline transition inline-flex items-center justify-center gap-1.5 font-display tracking-wide">
            <span>ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต (ท้องฟ้าจำลองรังสิต)</span>
            <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Header Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <a href="{{ route('showtimes.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                    class="h-10 w-auto object-contain">
                <div class="hidden sm:block">
                    <p class="font-display font-bold text-slate-900 text-sm leading-tight">ท้องฟ้าจำลองรังสิต</p>
                    <p class="text-[11px] text-slate-500">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                </div>
            </a>

            <a href="{{ route('showtimes.index') }}"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition">
                ← กลับหน้าหลัก
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 flex-1 w-full">
        
        <!-- Page Title & Search Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm mb-8 text-center">
            <div class="w-14 h-14 rounded-2xl bg-cyan-50 border border-cyan-200 text-cyan-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 font-display mb-2">
                ค้นหาตั๋วและตรวจสอบสถานะการจอง
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                กรอกเบอร์โทรศัพท์, รหัสการจอง (#123) หรืออีเมลที่ใช้ทำการจอง เพื่อดึงตั๋วเข้าชม E-Ticket และ QR Code
            </p>

            <form action="{{ route('bookings.search') }}" method="GET" class="max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ $query }}" required autofocus
                        placeholder="กรอกเบอร์โทรศัพท์ / รหัสการจอง / อีเมล"
                        class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition font-medium">
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white font-semibold py-3.5 px-8 rounded-2xl shadow-md transition text-sm font-display shrink-0 inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>ค้นหาตั๋ว</span>
                </button>
            </form>
        </div>

        <!-- Search Results Section -->
        @if ($query !== '')
            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-sm font-bold text-slate-700 font-display">
                        ผลการค้นหาสำหรับ: "<span class="text-cyan-700">{{ $query }}</span>"
                    </h2>
                    <span class="text-xs text-slate-500">พบ {{ $bookings->count() }} รายการ</span>
                </div>

                @forelse($bookings as $booking)
                    @php
                        $mTitle = $booking->showtime->movie->title_th ?? '';
                        if (str_contains($mTitle, 'แรงโน้มถ่วง')) {
                            $thumbImg = 'gravity.jpg';
                        } elseif (str_contains($mTitle, 'Cosmos') || str_contains($mTitle, 'คอสโมส')) {
                            $thumbImg = 'cosmos.jpg';
                        } elseif (str_contains($mTitle, 'Polaris') || str_contains($mTitle, 'ดาวเหนือ')) {
                            $thumbImg = 'polaris.jpg';
                        } elseif (str_contains($mTitle, 'World') || str_contains($mTitle, 'นอกโลก') || str_contains($mTitle, 'สุริยะ')) {
                            $thumbImg = 'worldbeyon.jpg';
                        } elseif (str_contains($mTitle, 'Earth') || str_contains($mTitle, 'จักรวาล')) {
                            $thumbImg = 'earthuniverse.jpg';
                        } elseif (str_contains($mTitle, 'Life') || str_contains($mTitle, 'ชีวิต')) {
                            $thumbImg = 'life.jpg';
                        } elseif (str_contains($mTitle, 'Lucia') || str_contains($mTitle, 'ลูเซีย')) {
                            $thumbImg = 'lucia.jpg';
                        } elseif (str_contains($mTitle, 'Oddy') || str_contains($mTitle, 'ออดี้')) {
                            $thumbImg = 'oddy.jpg';
                        } elseif (str_contains($mTitle, 'Solar')) {
                            $thumbImg = 'solar.jpg';
                        } elseif (str_contains($mTitle, 'Star') || str_contains($mTitle, 'ดวงดาว')) {
                            $thumbImg = 'star.jpg';
                        } elseif (str_contains($mTitle, 'Dancing') || str_contains($mTitle, 'เต้น')) {
                            $thumbImg = 'dancing.jpg';
                        } else {
                            $thumbImg = 'gravity.jpg';
                        }

                        $statusBadge = match($booking->status) {
                            'paid' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'ชำระเงินแล้ว (PAID)'],
                            'redeemed' => ['bg-blue-50 text-blue-700 border-blue-200', 'ตรวจตั๋วเข้าชมแล้ว'],
                            'pending', 'awaiting_payment' => ['bg-amber-50 text-amber-700 border-amber-200', 'รอชำระเงิน'],
                            'expired' => ['bg-rose-50 text-rose-700 border-rose-200', 'หมดอายุ'],
                            'cancelled' => ['bg-slate-100 text-slate-600 border-slate-200', 'ยกเลิก'],
                            default => ['bg-slate-100 text-slate-600 border-slate-200', $booking->status],
                        };
                    @endphp

                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/' . $thumbImg) }}" alt="{{ $mTitle }}"
                                class="w-14 h-18 object-cover rounded-xl border border-slate-200 shadow-xs shrink-0">
                            
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-mono font-bold text-slate-800">#{{ $booking->id }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md border {{ $statusBadge[0] }}">
                                        {{ $statusBadge[1] }}
                                    </span>
                                </div>
                                <h3 class="font-display font-bold text-base text-slate-900 line-clamp-1">
                                    {{ $mTitle ?: 'ภาพยนตร์เต็มโดม' }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $booking->showtime ? \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') : '-' }} &bull; รอบ {{ $booking->showtime ? \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') : '-' }} น.</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1.5 text-xs text-slate-600">
                                    <span>ผู้จอง: <strong>{{ $booking->booker_name }}</strong></span>
                                    <span>&bull;</span>
                                    <span>ที่นั่ง: <strong class="text-cyan-700">{{ $booking->quantity }} ที่</strong> ({{ !empty($booking->seats) ? implode(',', $booking->seats) : '-' }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="w-full sm:w-auto flex sm:flex-col items-center sm:items-end justify-between gap-2 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                            <span class="text-sm font-bold text-slate-900 font-display">
                                ฿ {{ number_format($booking->total_amount, 2) }}
                            </span>

                            @if (in_array($booking->status, ['paid', 'redeemed']))
                                <a href="{{ route('bookings.confirmed', $booking->qr_ticket_ref) }}"
                                    class="inline-flex items-center gap-1.5 bg-cyan-50 hover:bg-cyan-100 text-cyan-800 font-semibold px-4 py-2 rounded-xl text-xs transition border border-cyan-200">
                                    <svg class="w-3.5 h-3.5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    <span>ดูตั๋ว E-Ticket</span>
                                </a>
                            @elseif (in_array($booking->status, ['pending', 'awaiting_payment']))
                                <a href="{{ route('bookings.payment', $booking->qr_ticket_ref) }}"
                                    class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold px-4 py-2 rounded-xl text-xs transition shadow-xs">
                                    <span>ชำระเงินต่อ →</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="font-display font-bold text-base text-slate-700">ไม่พบข้อมูลการจอง</h3>
                        <p class="text-xs text-slate-400 mt-1">กรุณาตรวจสอบเบอร์โทรศัพท์ หรือรหัสการจองอีกครั้ง</p>
                    </div>
                @endforelse
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white text-slate-600 py-6 border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center text-xs text-slate-400 space-y-1">
            <p>ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต (ท้องฟ้าจำลองรังสิต) &bull; โทร 02 577 5456 – 9 ต่อ 304</p>
            <p>© 2026 ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>
