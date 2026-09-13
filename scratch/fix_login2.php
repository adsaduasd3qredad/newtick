<?php
$file = 'resources/views/auth/login.blade.php';
$content = file_get_contents($file);

$newContent = <<<HTML
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #141414; /* Match the footer background */
        }
    </style>
</head>

<body class="text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full relative z-10">
        
        <!-- Brand Logo -->
        <div class="text-center mb-10">
            <a href="{{ route('showtimes.index') }}" class="inline-block mb-6">
                <!-- We can put the logo on a white pill or just use it if it's transparent. Since it's a jpg, we'll give it a white rounded background -->
                <div class="bg-white p-3 rounded-2xl inline-block shadow-lg">
                    <img src="{{ asset('images/logo.jpg') }}" alt="ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต" class="h-16 w-auto object-contain">
                </div>
            </a>
            <h1 class="text-3xl font-bold text-white tracking-wide uppercase">Login</h1>
        </div>

        <!-- Login Card -->
        <div class="bg-[#1c1c1c] p-8 sm:p-10 rounded-2xl border border-gray-800 shadow-2xl">
            
            @if (isset(\$errors) && \$errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ \$errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}" autofocus
                        class="w-full bg-[#141414] border border-gray-700 rounded-xl px-4 py-3.5 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full bg-[#141414] border border-gray-700 rounded-xl px-4 py-3.5 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition">
                </div>

                <button type="submit"
                    class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition duration-200 uppercase tracking-widest mt-2">
                    Sign In
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('showtimes.index') }}"
                    class="text-sm text-gray-500 hover:text-cyan-400 transition inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>กลับสู่หน้าหลัก</span>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
HTML;

file_put_contents($file, $newContent);
echo "Updated login.blade.php fully\n";

