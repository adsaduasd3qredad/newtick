<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบตรวจตั๋วเข้าชม (Ticket Check-in) - ท้องฟ้าจำลองรังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }

        .font-display {
            font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

    <!-- Top Bar -->
    <header class="bg-slate-800/90 backdrop-blur-md border-b border-slate-700 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้" class="h-9 w-auto object-contain rounded">
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-white font-display leading-tight">ระบบตรวจตั๋วเข้าชม</h1>
                        <p class="text-[11px] text-slate-400">ท้องฟ้าจำลองรังสิต</p>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('pos.index') }}"
                    class="text-xs font-semibold text-cyan-400 hover:text-cyan-300 bg-cyan-950/60 hover:bg-cyan-900/60 px-3.5 py-2 rounded-xl transition border border-cyan-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span>ไปหน้าขายตั๋ว POS</span>
                </a>

                @if (auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ url('/admin') }}"
                        class="text-xs font-semibold text-amber-400 hover:text-amber-300 bg-amber-950/60 hover:bg-amber-900/60 px-3 py-2 rounded-xl transition border border-amber-800">
                        Admin
                    </a>
                @endif

                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-950/50 px-3 py-2 rounded-xl transition border border-rose-900">
                    ออก
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left / Scanner Form (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Scanner Card -->
            <div class="bg-slate-800/90 rounded-3xl border border-slate-700 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-white font-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>สแกนหรือกรอกรหัสตั๋ว</span>
                    </h2>
                    <button type="button" id="toggleCameraBtn" onclick="toggleCamera()"
                        class="text-xs font-medium text-cyan-400 hover:text-cyan-300 bg-cyan-950/80 border border-cyan-800 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        <span>เปิดกล้องสแกน</span>
                    </button>
                </div>

                <!-- Camera Container (Hidden by default) -->
                <div id="reader-container" class="hidden mb-4 rounded-2xl overflow-hidden border border-slate-700 bg-black">
                    <div id="reader" class="w-full"></div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="mb-4 bg-rose-500/10 border border-rose-500/30 text-rose-300 p-4 rounded-xl text-xs flex items-start gap-2">
                        <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Scan / Input Form -->
                <form id="checkinForm" method="POST" action="{{ route('checkin.process') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                            รหัส QR Ticket หรือ รหัส Booking (เช่น #123)
                        </label>
                        <div class="relative">
                            <input type="text" name="qr_ticket_ref" id="qr_input" autofocus required
                                value="{{ old('qr_ticket_ref') }}"
                                placeholder="สแกน QR หรือพิมพ์รหัสที่นี่..."
                                class="w-full bg-slate-900 border border-slate-600 rounded-2xl px-4 py-3.5 text-white font-mono text-base placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            * รองรับทั้งเครื่องยิงบาร์โค้ด USB, กล้องมือถือ และพิมพ์รหัสการจอง
                        </p>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition duration-200 text-sm font-display flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>ยืนยันตรวจตั๋วเข้าชม</span>
                    </button>
                </form>
            </div>

            <!-- Stats Mini Box -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-slate-800/80 rounded-2xl border border-slate-700/80 p-4 text-center">
                    <p class="text-xs text-slate-400">เช็คอินวันนี้แล้ว</p>
                    <p class="text-2xl font-bold font-display text-emerald-400 mt-1">
                        {{ $todayCheckins->count() }} <span class="text-xs text-slate-400 font-normal">รายการ</span>
                    </p>
                </div>

                <div class="bg-slate-800/80 rounded-2xl border border-slate-700/80 p-4 text-center">
                    <p class="text-xs text-slate-400">จำนวนที่นั่งที่เข้าชมแล้ว</p>
                    <p class="text-2xl font-bold font-display text-cyan-400 mt-1">
                        {{ $todayCheckedInSeats }} <span class="text-xs text-slate-400 font-normal">ที่นั่ง</span>
                    </p>
                </div>
            </div>

        </div>

        <!-- Right / Recent Check-in Logs (7 Cols) -->
        <div class="lg:col-span-7">
            <div class="bg-slate-800/90 rounded-3xl border border-slate-700 p-6 shadow-xl h-full flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-white font-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>ประวัติการตรวจตั๋ววันนี้ (ล่าสุด)</span>
                    </h2>
                    <span class="text-xs text-slate-400">
                        {{ now()->locale('th')->translatedFormat('j F Y') }}
                    </span>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-700 text-slate-400 font-semibold">
                                <th class="pb-3">เวลาตรวจ</th>
                                <th class="pb-3">รหัสจอง</th>
                                <th class="pb-3">ผู้จอง</th>
                                <th class="pb-3">ภาพยนตร์ & รอบ</th>
                                <th class="pb-3 text-right">ที่นั่ง</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50 text-slate-300">
                            @forelse($todayCheckins as $chk)
                                <tr class="hover:bg-slate-700/30 transition">
                                    <td class="py-3 text-emerald-400 font-mono font-semibold">
                                        {{ $chk->checked_in_at ? $chk->checked_in_at->format('H:i:s') : '-' }} น.
                                    </td>
                                    <td class="py-3 font-mono font-bold text-white">
                                        #{{ $chk->id }}
                                    </td>
                                    <td class="py-3 font-medium">
                                        {{ $chk->booker_name }}
                                        <span class="block text-[10px] text-slate-500">{{ $chk->booker_phone }}</span>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-semibold text-slate-200 line-clamp-1">
                                            {{ $chk->showtime->movie->title_th ?? '-' }}
                                        </p>
                                        <span class="text-[10px] text-slate-400">
                                            รอบ {{ \Carbon\Carbon::parse($chk->showtime->show_time)->format('H:i') }} น.
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-bold text-cyan-300">
                                        {{ $chk->quantity }} ที่
                                        @if (!empty($chk->seats))
                                            <span class="block text-[10px] text-slate-400 font-normal truncate max-w-[100px] text-right">
                                                {{ implode(', ', $chk->seats) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500">
                                        ยังไม่มีประวัติการเช็คอินในวันนี้
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <script>
        const inputEl = document.getElementById('qr_input');
        inputEl.focus();

        let html5QrCode = null;
        let isCameraRunning = false;

        function toggleCamera() {
            const container = document.getElementById('reader-container');
            const btn = document.getElementById('toggleCameraBtn');

            if (!isCameraRunning) {
                container.classList.remove('hidden');
                btn.textContent = 'ปิดกล้อง';
                btn.classList.add('bg-rose-950/80', 'text-rose-400', 'border-rose-800');

                html5QrCode = new Html5Qrcode("reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        inputEl.value = decodedText;
                        html5QrCode.stop().then(() => {
                            container.classList.add('hidden');
                            document.getElementById('checkinForm').submit();
                        });
                    },
                    (errorMessage) => {
                        // ignore scan errors
                    }
                ).catch((err) => {
                    alert('ไม่สามารถเปิดกล้องได้: ' + err);
                });
                isCameraRunning = true;
            } else {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        container.classList.add('hidden');
                        btn.textContent = 'เปิดกล้องสแกน';
                        btn.classList.remove('bg-rose-950/80', 'text-rose-400', 'border-rose-800');
                        isCameraRunning = false;
                    });
                }
            }
        }
    </script>
</body>

</html>