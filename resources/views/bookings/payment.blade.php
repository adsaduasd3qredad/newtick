<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- แถบประกาศด้านบน -->
    <div
        class="bg-gradient-to-r from-cyan-500 via-sky-500 to-violet-600 text-white text-sm font-medium py-2.5 px-4 text-center shadow-sm">
        <a href="https://sci-rangsit.dole.go.th" target="_blank"
            class="hover:opacity-90 transition inline-flex items-center justify-center gap-2 font-display tracking-wide">
            <span aria-hidden="true">✦</span>
            <span>คลิก <strong class="text-yellow-300 underline underline-offset-2 font-bold">ที่นี่</strong>
                เพื่อเข้าสู่เว็บไซต์ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</span>
            <span aria-hidden="true">✦</span>
        </a>
    </div>

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('showtimes.index') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                        class="h-11 w-auto object-contain">
                </a>
            </div>
            <a href="/admin" title="เข้าสู่ระบบเจ้าหน้าที่"
                class="text-slate-400 hover:text-slate-700 p-2 rounded-lg transition-colors inline-flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="11" width="14" height="10" rx="2" ry="2"></rect>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4"></path>
                </svg>
            </a>
        </div>
    </header>

    <!-- เนื้อหาหลัก -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8 my-6">
        <div class="max-w-3xl w-full bg-white p-6 sm:p-12 rounded-3xl shadow-sm border border-slate-200/85">

            <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-slate-900 tracking-tight">ชำระเงิน</h1>

            <!-- Step indicator (สถานะปัจจุบันอยู่ที่ 3: ชำระเงิน) -->
            <div class="flex items-center justify-center gap-2 sm:gap-6 mb-10 overflow-x-auto py-2">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'กรอกข้อมูล'],
                        ['n' => 2, 'label' => 'เลือกที่นั่ง'],
                        ['n' => 3, 'label' => 'ชำระเงิน'],
                        ['n' => 4, 'label' => 'เสร็จสมบูรณ์'],
                    ];
                    $currentStep = 3;
                @endphp
                @foreach ($steps as $step)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-sm
                                    {{ $step['n'] < $currentStep ? 'bg-cyan-600 text-white' : ($step['n'] == $currentStep ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-400') }}">
                                @if ($step['n'] < $currentStep)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $step['n'] }}
                                @endif
                            </div>
                            <span
                                class="text-[10px] sm:text-[11px] mt-1.5 text-center max-w-[70px] {{ $step['n'] <= $currentStep ? 'text-slate-800 font-semibold' : 'text-slate-400' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if (!$loop->last)
                            <div
                                class="w-4 sm:w-14 h-0.5 {{ $step['n'] < $currentStep ? 'bg-cyan-600' : 'bg-slate-200' }} mx-1 sm:mx-2 mt-[-14px]">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- ข้อมูลการจองและยอดชำระ -->
            <div class="text-center mb-6">
                <p class="text-slate-600 text-sm sm:text-base mb-1">รหัสจอง <strong
                        class="text-slate-900 font-bold text-lg">#{{ $booking->id }}</strong></p>
                <p class="text-slate-600 text-sm sm:text-base">ยอดชำระ <strong
                        class="text-emerald-600 font-bold text-xl sm:text-2xl">{{ number_format($booking->total_amount, 2) }}
                        บาท</strong></p>
            </div>

            <!-- เวลานับถอยหลัง -->
            <div class="flex justify-center mb-8">
                <div
                    class="bg-rose-50 border border-rose-200 text-rose-600 px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>กรุณาชำระภายใน <span id="countdown"
                            class="font-bold tracking-wider text-rose-700">--</span></span>
                </div>
            </div>

            <!-- ข้อความ Error ถ้ามี -->
            @if ($errors->any())
                <div
                    class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm shadow-sm max-w-md mx-auto">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- กล่องแสดง QR Code -->
            <div class="flex justify-center mb-10">
                <div
                    class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.06)] border border-slate-200/80 text-center max-w-xs w-full transition-transform hover:-translate-y-1 duration-300">
                    <div
                        class="bg-slate-50 w-full aspect-square rounded-2xl flex items-center justify-center border border-slate-200/60 mb-4 p-4">
                        {!! QrCode::size(220)->generate($qrPayload) !!}
                    </div>
                    <p class="text-slate-600 text-sm font-semibold">สแกนจ่ายผ่านแอปธนาคาร</p>
                </div>
            </div>

            <!-- ฟอร์มยืนยันการชำระเงิน (คง Route และพารามิเตอร์เดิมเป๊ะ) -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                <form method="POST" action="{{ route('bookings.confirm', $booking->id) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="payment_method" value="qr_code">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md transition duration-200">
                        ฉันชำระเงินผ่าน QR แล้ว
                    </button>
                </form>

                <form method="POST" action="{{ route('bookings.confirm', $booking->id) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="payment_method" value="counter">
                    <button type="submit"
                        class="w-full bg-white hover:bg-slate-50 text-slate-700 font-semibold py-3.5 px-6 rounded-xl shadow-sm border border-slate-300 transition duration-200">
                        ชำระที่เคาน์เตอร์แทน
                    </button>
                </form>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white text-slate-600 py-8 border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                    class="h-10 w-auto object-contain bg-white p-1 rounded-md shadow-sm border border-slate-100">
                <div>
                    <p class="text-slate-900 font-display font-semibold text-sm">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                    <p class="text-xs text-slate-500">RANGSIT SCIENCE CENTRE FOR EDUCATION</p>
                </div>
            </div>

            <div class="text-center md:text-right text-xs space-y-1">
                <p class="text-slate-500">Copyright © 2026 ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved</p>
                <p class="text-slate-800 font-medium">โทร 02 577 5456 – 9 ต่อ 304</p>
            </div>
        </div>
    </footer>

    <!-- Script นับเวลาถอยหลัง (ใช้โค้ดเดิมของคุณ) -->
    <script>
        const expiresAt = new Date("{{ $booking->expires_at->toIso8601String() }}").getTime();
        const el = document.getElementById('countdown');

        const timer = setInterval(() => {
            const diff = Math.max(0, expiresAt - Date.now());
            if (diff <= 0) {
                clearInterval(timer);
                el.textContent = "0 นาที 0 วินาที";
                return;
            }
            const m = Math.floor(diff / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = `${m} นาที ${s} วินาที`;
        }, 1000);
    </script>
</body>

</html>
