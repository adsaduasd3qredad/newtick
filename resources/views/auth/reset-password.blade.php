<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งรหัสผ่านใหม่ - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body class="text-gray-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full relative z-10">
        
        <div class="text-center mb-8">
            <a href="{{ route('showtimes.index') }}" class="inline-block mb-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต" class="h-16 w-auto object-contain mx-auto">
            </a>
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">ตั้งรหัสผ่านใหม่</h1>
            <p class="text-xs text-gray-500 mt-1">กำหนดรหัสผ่านใหม่สำหรับเข้าใช้งานระบบ</p>
        </div>

        <div class="bg-white p-8 sm:p-10 rounded-2xl border border-gray-200 shadow-xl">
            
            @if (isset($errors) && $errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">อีเมล</label>
                    <input type="email" name="email" required value="{{ old('email', $email) }}" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-xl px-4 py-3.5 text-gray-600 focus:outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)</label>
                    <input type="password" name="password" required autofocus
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ยืนยันรหัสผ่านใหม่อีกครั้ง</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                </div>

                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md transition duration-200">
                    บันทึกรหัสผ่านใหม่
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center text-xs text-gray-500">
                <a href="{{ route('login') }}" class="hover:text-cyan-600 font-medium inline-flex items-center gap-1">
                    ← กลับหน้าเข้าสู่ระบบ
                </a>
            </div>
        </div>
    </div>

</body>
</html>

