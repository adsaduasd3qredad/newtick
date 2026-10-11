<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
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
        
        <!-- Brand Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('showtimes.index') }}" class="inline-block mb-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต" class="h-16 w-auto object-contain mx-auto">
            </a>
            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">รีเซ็ตรหัสผ่านเจ้าหน้าที่</h1>
            <p class="text-xs text-gray-500 mt-1">กรอกอีเมลบัญชีผู้ใช้งานเพื่อกู้คืนรหัสผ่าน</p>
        </div>

        <!-- Card -->
        <div class="bg-white p-8 sm:p-10 rounded-2xl border border-gray-200 shadow-xl">
            
            @if (session('status'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold">สร้างลิงก์รีเซ็ตรหัสผ่านสำเร็จ!</p>
                        <p class="mt-1 text-xs">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if (session('demo_reset_url'))
                <div class="mb-6 bg-cyan-50 border border-cyan-200 p-4 rounded-xl text-xs text-cyan-900">
                    <p class="font-bold mb-1">🔗 ลิงก์สำหรับตั้งรหัสผ่านใหม่ (สำหรับทดสอบ / Demo):</p>
                    <a href="{{ session('demo_reset_url') }}" class="text-blue-600 font-mono underline break-all hover:text-blue-800">
                        {{ session('demo_reset_url') }}
                    </a>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">อีเมลบัญชีผู้ดูแลระบบ / เจ้าหน้าที่</label>
                    <input type="email" name="email" required value="{{ old('email') }}" autofocus
                        placeholder="admin@example.com"
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                </div>

                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md transition duration-200">
                    ส่งลิงก์รีเซ็ตรหัสผ่าน
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <a href="{{ route('login') }}" class="hover:text-cyan-600 font-medium inline-flex items-center gap-1">
                    ← กลับหน้าเข้าสู่ระบบ
                </a>
                <a href="{{ route('showtimes.index') }}" class="hover:text-gray-700">
                    หน้าหลัก
                </a>
            </div>
        </div>
    </div>

</body>
</html>

