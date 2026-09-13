<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ticket System - Cinema')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Using a clean sans-serif font like Prompt or Kanit to match the modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flatpickr (if needed for date selection) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f9f9f9; /* Light grey/off-white background */
            color: #1a1a1a;
        }
        /* Custom slide menu transition */
        #slide-menu {
            transition: transform 0.3s ease-in-out;
        }
        .slide-menu-open {
            transform: translateX(0);
        }
        .slide-menu-closed {
            transform: translateX(100%);
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            
            <!-- Logo (Left) -->
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="ศว. รังสิต" class="h-10 w-auto rounded">
                <span class="font-bold text-lg hidden sm:block text-[#1c1c1c]">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</span>
            </a>

            

            <!-- Hamburger (Right) -->
            <button id="menu-btn" class="p-2 text-gray-600 hover:text-black focus:outline-none focus:ring-2 focus:ring-black">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </header>

    <!-- Slide-out Menu -->
    <div id="slide-menu" class="fixed inset-y-0 right-0 w-80 bg-[#1c1c1c] text-white z-50 slide-menu-closed shadow-2xl overflow-y-auto">
        <div class="p-6">
            <!-- Close button -->
            <div class="flex justify-end mb-8">
                <button id="close-menu-btn" class="text-white/80 hover:text-yellow-200 focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

                        <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-sm uppercase tracking-wider font-medium">
                <a href="/" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Home
                </a>
                <a href="/#about" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    About Us
                </a>
                <a href="/#contact" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Contact Us
                </a>
                <a href="/#contact" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Cinema Location
                </a>
            </nav>

            <!-- Bottom Actions -->
            <div class="mt-12 space-y-4">
                @auth
                    <a href="{{ url('/admin') }}" class="block text-sm font-semibold uppercase hover:text-gray-300">Admin Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="text-sm font-semibold uppercase hover:text-red-400 text-left">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-sm font-semibold uppercase hover:text-gray-300">Login</a>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Overlay for menu -->
    <div id="menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity"></div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

        <!-- Contact & Footer Section -->
    <div id="contact" class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-16 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="text-center md:text-left flex-1">
                <h2 class="text-3xl text-cyan-500 mb-6 font-bold uppercase tracking-wider">ติดต่อเรา</h2>
                <p class="text-gray-800 font-medium mb-1 text-lg">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                <p class="text-gray-600 mb-1">ที่อยู่ 5 หมู่ 2 ต.รังสิต อ.ธัญบุรี จ.ปทุมธานี 12110</p>
                <p class="text-gray-600 mb-6">โทร 02 577 5456 - 9 ต่อ 304</p>
                
                <div class="flex items-center justify-center md:justify-start gap-2 mb-6">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.04c-5.5 0-10 4.49-10 10.02 0 5 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7a10 10 0 008.44-9.9c0-5.53-4.5-10.02-10-10.02z"></path></svg>
                    <span class="font-medium text-gray-700">ท้องฟ้าจำลองรังสิต</span>
                </div>
                
                <a href="https://www.google.com/maps/search/?api=1&query=ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต" target="_blank" class="inline-block bg-cyan-500 hover:bg-cyan-600 text-white font-medium py-3 px-10 rounded-xl shadow transition">
                    แผนที่การเดินทาง
                </a>
            </div>
            
            <div class="flex-1 w-full max-w-lg rounded-xl overflow-hidden shadow-sm border border-gray-200 h-64 bg-gray-100 flex items-center justify-center">
                <iframe src="https://maps.google.com/maps?q=Rangsit+Science+Centre+for+Education&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>

    <!-- Black Footer -->
    <footer class="bg-gradient-to-r from-[#d94a11] to-[#f47e20] text-white/90 py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Footer Logo & Copyright -->
                <div class="flex flex-col md:flex-row items-center gap-4 text-center md:text-left">
                    <div class="bg-white p-1 rounded">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="h-10 rounded">
                    </div>
                    <div>
                        <p class="text-white font-medium text-sm">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                        <p class="text-[10px] uppercase tracking-wider">RANGSIT SCIENCE CENTRE FOR EDUCATION</p>
                        <p class="text-[11px] mt-2">Copyright &copy; {{ date('Y') }} ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved</p>
                    </div>
                </div>
                
                <!-- Footer Links -->
                <div class="text-[12px] flex flex-wrap justify-center gap-4 text-white/80">
                    <a href="/" class="hover:text-yellow-200 transition">หน้าแรก</a>
                    <span>|</span>
                    <a href="/#movies" class="hover:text-yellow-200 transition">โปรแกรมภาพยนตร์</a>
                    <span>|</span>
                    <a href="#" class="hover:text-yellow-200 transition">โปรโมชั่น</a>
                    <span>|</span>
                    <a href="#" class="hover:text-yellow-200 transition">ข่าวสารและกิจกรรม</a>
                    <span class="text-white ml-2">โทร 02 577 5456 - 9 ต่อ 304</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const closeBtn = document.getElementById('close-menu-btn');
            const slideMenu = document.getElementById('slide-menu');
            const overlay = document.getElementById('menu-overlay');

            function openMenu() {
                slideMenu.classList.remove('slide-menu-closed');
                slideMenu.classList.add('slide-menu-open');
                overlay.classList.remove('hidden');
            }

            function closeMenu() {
                slideMenu.classList.remove('slide-menu-open');
                slideMenu.classList.add('slide-menu-closed');
                overlay.classList.add('hidden');
            }

            menuBtn.addEventListener('click', openMenu);
            closeBtn.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
        });
    </script>
    @stack('scripts')
</body>
</html>

