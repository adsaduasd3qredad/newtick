<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รับตั๋วสำเร็จ</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white p-8 max-w-lg mx-auto text-center">
    <h1 class="text-2xl font-bold text-green-400 mb-2">✓ รับตั๋วสำเร็จ</h1>
    <div class="bg-gray-800 rounded-lg p-4 mt-4 text-left">
        <p class="text-sm text-gray-400">ภาพยนตร์</p>
        <p class="font-medium mb-3">{{ $booking->showtime->movie->title_th }}</p>

        <p class="text-sm text-gray-400">รอบฉาย</p>
        <p class="font-medium mb-3">
            {{ \Carbon\Carbon::parse($booking->showtime->show_date)->translatedFormat('l d/m/Y') }}
            {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }} น.
        </p>

        <p class="text-sm text-gray-400">ผู้จอง</p>
        <p class="font-medium mb-3">{{ $booking->booker_name }} ({{ $booking->quantity }} ที่นั่ง)</p>
    </div>

    <a href="{{ route('checkin.form') }}" class="inline-block mt-6 bg-blue-600 px-6 py-2 rounded font-medium">
        เช็คอินคนถัดไป
    </a>
</body>
</html>