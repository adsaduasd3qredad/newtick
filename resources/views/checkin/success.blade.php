<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจตั๋วสำเร็จ - ท้องฟ้าจำลองรังสิต</title>
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

<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800/95 border border-slate-700 rounded-3xl p-8 shadow-2xl text-center">
        
        <!-- Animated Success Icon -->
        <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold rounded-full uppercase tracking-wider font-display">
            ตรวจตั๋วสำเร็จ (CHECKED-IN)
        </span>

        <h1 class="text-2xl font-bold font-display text-white mt-3 mb-1">
            ยินดีต้อนรับสู่ห้องฉาย
        </h1>
        <p class="text-xs text-slate-400 font-mono">
            รหัสการจอง: <strong class="text-white">#{{ $booking->id }}</strong>
        </p>

        <!-- Booking Details Card -->
        <div class="bg-slate-900/80 rounded-2xl p-5 my-6 text-left border border-slate-700 space-y-4">
            <div>
                <p class="text-[11px] text-slate-400 font-medium">ภาพยนตร์รอบการแสดง</p>
                <p class="text-base font-bold text-white font-display mt-0.5">
                    {{ $booking->showtime->movie->title_th ?? '-' }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800 text-xs">
                <div>
                    <p class="text-slate-400">วันที่รอบฉาย</p>
                    <p class="font-semibold text-slate-200 mt-0.5">
                        {{ \Carbon\Carbon::parse($booking->showtime->show_date)->locale('th')->translatedFormat('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-slate-400">เวลา</p>
                    <p class="font-bold text-cyan-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }} น.
                    </p>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-800 text-xs">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-slate-400">ผู้จอง:</span>
                    <span class="font-semibold text-white">{{ $booking->booker_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">จำนวนที่นั่ง:</span>
                    <span class="font-bold text-cyan-300 font-display">{{ $booking->quantity }} ที่นั่ง</span>
                </div>
            </div>

            @if (!empty($booking->seats))
                <div class="pt-2 border-t border-slate-800">
                    <p class="text-[11px] text-slate-400 mb-1.5 font-medium">หมายเลขที่นั่ง:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($booking->seats as $s)
                            <span class="px-2 py-0.5 bg-cyan-950 border border-cyan-700 text-cyan-300 rounded text-xs font-bold font-mono">
                                {{ $s }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="space-y-3">
            <a href="{{ route('checkin.form') }}" id="nextBtn"
                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg transition duration-200 text-sm font-display">
                <span>ตรวจตั๋วคนถัดไป (Enter)</span>
                <span>→</span>
            </a>

            <a href="{{ route('pos.index') }}"
                class="inline-block text-xs text-slate-400 hover:text-slate-200 transition">
                กลับหน้าขายตั๋ว POS
            </a>
        </div>

    </div>

    <!-- Audio Beep & Keyboard Listeners -->
    <script>
        // Web Audio API Synth Success Beep
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = "sine";
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
            osc.frequency.setValueAtTime(1760, audioCtx.currentTime + 0.1); // A6 note
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.3);
        } catch (e) {}

        // Listen for Enter or Space to go to next checkin
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                window.location.href = "{{ route('checkin.form') }}";
            }
        });
    </script>
</body>

</html>