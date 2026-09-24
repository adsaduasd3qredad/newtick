<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัตรเข้าชมภาพยนตร์ (E-Ticket) - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
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

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .ticket-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- แถบประกาศด้านบน -->
    <div class="no-print bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-700 text-white text-xs sm:text-sm font-medium py-2 px-4 text-center shadow-sm">
        <a href="https://sci-rangsit.dole.go.th" target="_blank"
            class="hover:underline transition inline-flex items-center justify-center gap-1.5 font-display tracking-wide">
            <span>ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต (ท้องฟ้าจำลองรังสิต)</span>
            <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    <!-- Header / Navbar -->
    <header class="no-print bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <a href="{{ route('showtimes.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                    class="h-10 sm:h-11 w-auto object-contain">
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
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8 my-4">
        <div class="max-w-3xl w-full">

            <!-- Step indicator -->
            <div class="no-print flex items-center justify-center gap-2 sm:gap-6 mb-8 overflow-x-auto py-2">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'กรอกข้อมูล'],
                        ['n' => 2, 'label' => 'เลือกที่นั่ง'],
                        ['n' => 3, 'label' => 'ชำระเงิน'],
                        ['n' => 4, 'label' => 'เสร็จสมบูรณ์'],
                    ];
                    $currentStep = 4;
                @endphp
                @foreach ($steps as $step)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-sm bg-emerald-500 text-white ring-4 ring-emerald-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] sm:text-[11px] mt-1.5 text-center max-w-[70px] text-slate-800 font-semibold">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if (!$loop->last)
                            <div class="w-4 sm:w-14 h-0.5 bg-emerald-500 mx-1 sm:mx-2 mt-[-14px]"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Ticket Card (Boarding Pass Cinema Design) -->
            <div class="ticket-card bg-white rounded-3xl shadow-lg border border-slate-200 overflow-hidden">
                
                <!-- Ticket Header -->
                <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-center gap-4 border-b border-indigo-900">
                    <div class="flex items-center gap-4 text-center sm:text-left">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 border border-cyan-400/30 flex items-center justify-center text-cyan-300 shadow-inner shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-cyan-300 font-semibold tracking-wider uppercase font-display">
                                บัตรเข้าชมภาพยนตร์เต็มโดม (E-TICKET)
                            </span>
                            <h2 class="text-xl sm:text-2xl font-bold font-display text-white">
                                ท้องฟ้าจำลองรังสิต
                            </h2>
                        </div>
                    </div>

                    <div class="text-center sm:text-right">
                        <span class="px-3 py-1 text-xs font-bold bg-emerald-500 text-white rounded-full inline-block shadow-sm">
                            ชำระเงินสำเร็จ (PAID)
                        </span>
                        <p class="text-xs text-slate-300 mt-1.5 font-mono">
                            รหัสจอง: <strong class="text-white text-sm">#{{ $booking->id }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Ticket Body Grid -->
                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    
                    <!-- Movie & Details (2 Cols) -->
                    <div class="md:col-span-2 space-y-5">
                        
                        <!-- Movie Title -->
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-semibold">ภาพยนตร์รอบการแสดง</p>
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 font-display mt-0.5">
                                {{ $booking->showtime->movie->title_th ?? '-' }}
                            </h3>
                            @if ($booking->showtime->movie && $booking->showtime->movie->title_en)
                                <p class="text-xs text-slate-500">{{ $booking->showtime->movie->title_en }}</p>
                            @endif
                        </div>

                        <!-- Date & Time Grid -->
                        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200/70 text-sm">
                            <div>
                                <p class="text-xs text-slate-400 font-medium">วันที่จัดแสดง</p>
                                <p class="font-bold text-slate-800 font-display mt-0.5">
                                    {{ \Carbon\Carbon::parse($booking->showtime->show_date)->locale('th')->translatedFormat('l j F Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">รอบเวลา</p>
                                <div class="flex items-center gap-1 font-bold text-cyan-700 font-display text-base mt-0.5">
                                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }} น.</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50/60 p-4">
                            <p class="text-xs text-cyan-700 font-semibold">จำนวนผู้เข้าชม</p>
                            <p class="mt-1 text-xl font-bold text-slate-900">{{ $booking->quantity }} คน</p>
                            <p class="mt-1 text-xs text-slate-500">เจ้าหน้าที่จะตรวจสอบและจัดการที่นั่งจากรหัสการจอง</p>
                        </div>

                        <!-- Booker info & payment -->
                        <div class="pt-3 border-t border-slate-100 flex flex-wrap justify-between items-center text-xs text-slate-600 gap-2">
                            <div>
                                <span>ผู้จอง: <strong class="text-slate-800">{{ $booking->booker_name }}</strong></span>
                                @if ($booking->booker_phone)
                                    <span class="text-slate-400 ml-2">({{ $booking->booker_phone }})</span>
                                @endif
                            </div>
                            <div>
                                                            <div class="flex items-center gap-2">
                                <span>ยอดชำระ: <strong class="text-emerald-600 text-sm font-bold">{{ number_format($booking->total_amount, 2) }} บาท</strong></span>
                                @if(in_array($booking->status, ['pending', 'awaiting_payment']))
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[10px] font-bold">รอชำระเงิน</span>
                                @elseif($booking->status == 'paid')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">ชำระแล้ว</span>
                                @endif
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Stub (1 Col) -->
                    <div class="text-center flex flex-col items-center justify-center p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                        <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-slate-200 mb-3">
                            {!! QrCode::size(160)->generate($booking->qr_ticket_ref) !!}
                        </div>
                        <p class="text-xs font-bold text-slate-700">สแกนเพื่อเข้าชม</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 max-w-[180px] break-all font-mono">
                            Ref: {{ substr($booking->qr_ticket_ref, 0, 13) }}...
                        </p>
                    </div>
                </div>

                @if($booking->status === 'awaiting_payment' && $booking->payment_method === 'counter' && $booking->expires_at)
                    @php($counterWindowStartsAt = $booking->showtime->startsAt()->subMinutes(30))
                    <div id="counter-payment-window"
                        data-start="{{ $counterWindowStartsAt->toIso8601String() }}"
                        data-end="{{ $booking->expires_at->toIso8601String() }}"
                        class="no-print mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-center text-sm text-amber-900">
                        <p>จองแบบชำระหน้าเคาน์เตอร์ได้ถึง {{ $booking->expires_at->format('d/m/Y H:i') }} น.</p>
                        <p id="counter-payment-countdown" class="mt-1 font-semibold"></p>
                    </div>
                @endif

                <!-- Ticket Instructions Footer -->
                <div class="bg-slate-50/90 px-6 sm:px-8 py-3.5 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                    <p>กรุณาแสดงหน้านี้หรือตั๋วที่พิมพ์แก่เจ้าหน้าที่ ณ ประตูทางเข้าก่อนรอบฉาย 15 นาที</p>
                    <p class="font-mono text-slate-400">Issued: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Action Buttons (Print / Home) -->
            <div class="no-print mt-8 flex flex-wrap justify-center gap-4">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold py-3 px-6 rounded-xl border border-slate-300 shadow-sm transition text-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>พิมพ์ตั๋ว / บันทึก PDF</span>
                </button>

                <a href="{{ route('showtimes.index') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md transition text-sm font-display">
                    <span>กลับสู่หน้าหลัก</span>
                    <span>→</span>
                </a>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="no-print bg-white text-slate-600 py-6 border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center text-xs text-slate-400 space-y-1">
            <p>ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต (ท้องฟ้าจำลองรังสิต) &bull; โทร 02 577 5456 – 9 ต่อ 304</p>
            <p>© 2026 ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved.</p>
        </div>
    </footer>
    @if(isset($counterWindowStartsAt))
        <script>
            const counterWindowStartsAt = new Date(@json($counterWindowStartsAt->toIso8601String())).getTime();
            const counterWindowEndsAt = new Date(@json($booking->expires_at->toIso8601String())).getTime();
            const counterCountdown = document.getElementById('counter-payment-countdown');

            function updateCounterPaymentWindow() {
                const now = Date.now();
                if (now < counterWindowStartsAt) {
                    counterCountdown.textContent = `เริ่มนับถอยหลังวันที่ ${new Date(counterWindowStartsAt).toLocaleString('th-TH')}`;
                    return;
                }

                const remaining = Math.max(0, counterWindowEndsAt - now);
                if (remaining === 0) {
                    counterCountdown.textContent = 'หมดเวลาชำระเงิน';
                    return;
                }

                const minutes = Math.floor(remaining / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);
                counterCountdown.textContent = `เวลาชำระคงเหลือ ${minutes} นาที ${seconds} วินาที`;
            }

            updateCounterPaymentWindow();
            setInterval(updateCounterPaymentWindow, 1000);
        </script>
    @endif
</body>

</html>
