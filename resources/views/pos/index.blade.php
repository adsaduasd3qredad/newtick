<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้าขายตั๋วหน้าเคาน์เตอร์ (POS)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800">ระบบขายตั๋วหน้าเคาน์เตอร์ (POS)</h1>
            <a href="/" class="text-sm text-slate-500 hover:text-cyan-600">ออกจากระบบ POS</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($showtimes as $showtime)
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center hover:border-cyan-400 transition">
                    <div>
                        <p class="font-bold text-lg text-slate-900">{{ $showtime->movie->title_th }}</p>
                        <p class="text-sm text-slate-500">เวลา: {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }} น. | ว่าง: {{ $showtime->available_seats }} ที่</p>
                    </div>
                    <a href="{{ route('bookings.create', $showtime->id) }}" 
                       class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-xl font-medium text-sm transition">
                        ขายตั๋ว
                    </a>
                </div>
            @empty
                <div class="col-span-2 text-center py-20 text-slate-400">วันนี้ไม่มีรอบฉาย</div>
            @endforelse
        </div>
    </div>
</body>
</html>