<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - @yield('title', 'ระบบขายตั๋วหน้าเคาน์เตอร์')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'IBM Plex Sans Thai', sans-serif; }
        .font-display { font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif; }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10 w-auto object-contain rounded">
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold text-slate-800 leading-tight">POS System</h1>
                    </div>
                </a>
                
                <!-- Main Nav -->
                <nav class="hidden md:flex gap-4">
                    <a href="{{ route('pos.index') }}" class="text-sm font-medium {{ request()->routeIs('pos.index') ? 'text-cyan-600 border-b-2 border-cyan-600' : 'text-slate-500 hover:text-slate-800' }} pb-1">รอบฉาย (Showtimes)</a>
                    <a href="{{ route('pos.scan') }}" class="text-sm font-medium {{ request()->routeIs('pos.scan') ? 'text-cyan-600 border-b-2 border-cyan-600' : 'text-slate-500 hover:text-slate-800' }} pb-1">เช็คคิวอาร์ (Scan QR)</a>
                    <a href="{{ route('pos.orders') }}" class="text-sm font-medium {{ request()->routeIs('pos.orders') ? 'text-cyan-600 border-b-2 border-cyan-600' : 'text-slate-500 hover:text-slate-800' }} pb-1">รายการจอง (Orders)</a>
                    <a href="{{ route('pos.reports') }}" class="text-sm font-medium {{ request()->routeIs('pos.reports*') ? 'text-cyan-600 border-b-2 border-cyan-600' : 'text-slate-500 hover:text-slate-800' }} pb-1">รายงาน (Reports)</a>
                </nav>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <span class="text-xs text-slate-500 hidden md:inline">เจ้าหน้าที่: <strong class="text-slate-700">{{ Auth::user()->name ?? 'Staff User' }}</strong></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-600 border border-rose-200 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition">ออกจากระบบ</button>
                </form>
            </div>
        </div>
        <!-- Mobile Nav -->
        <div class="md:hidden flex overflow-x-auto px-4 py-2 bg-slate-50 border-t border-slate-200 gap-4 text-xs font-medium whitespace-nowrap">
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'text-cyan-600' : 'text-slate-600' }}">Showtimes</a>
            <a href="{{ route('pos.scan') }}" class="{{ request()->routeIs('pos.scan') ? 'text-cyan-600' : 'text-slate-600' }}">Scan QR</a>
            <a href="{{ route('pos.orders') }}" class="{{ request()->routeIs('pos.orders') ? 'text-cyan-600' : 'text-slate-600' }}">Orders</a>
            <a href="{{ route('pos.reports') }}" class="{{ request()->routeIs('pos.reports*') ? 'text-cyan-600' : 'text-slate-600' }}">Reports</a>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-auto">
        ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต &bull; ระบบ POS
    </footer>

    @stack('scripts')
</body>
</html>

