<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

$search = <<<HTML
            <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-sm uppercase tracking-wider font-medium">
                <a href="/" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Home</a>
                
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">About Us</a>
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Contact Us</a>
                <a href="#" class="hover:text-gray-300 transition border-b border-gray-800 pb-2">Cinema Location</a>
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
                    <a href="{{ route('login') }}" class="block font-bold text-lg uppercase hover:text-gray-300 mt-8">Login</a>
                @endauth
            </div>
HTML;

$replace = <<<HTML
            <!-- Navigation Links -->
            <nav class="flex flex-col space-y-5 text-lg font-medium">
                <a href="/" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    หน้าแรก (Home)
                </a>
                <a href="/#movies" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                    รอบฉาย (Showtimes)
                </a>
                <a href="https://www.rsci.ac.th/" target="_blank" class="hover:text-cyan-400 transition border-b border-gray-800 pb-3 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    ติดต่อเรา (Contact Us)
                </a>
            </nav>

            <!-- Bottom Actions -->
            <div class="mt-12 space-y-4">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ url('/admin') }}" class="block text-sm font-semibold uppercase hover:text-cyan-400 text-cyan-500">
                            เข้าสู่ระบบหลังบ้าน (Admin)
                        </a>
                    @endif
                    <a href="{{ url('/pos') }}" class="block text-sm font-semibold uppercase hover:text-emerald-400 text-emerald-500 mt-4">
                        เข้าสู่ระบบขายตั๋ว (POS)
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block mt-6 pt-4 border-t border-gray-800">
                        @csrf
                        <button type="submit" class="text-sm font-semibold uppercase hover:text-red-400 text-left w-full text-red-500">ออกจากระบบ (Logout)</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-4 rounded-xl transition mt-8">
                        สำหรับเจ้าหน้าที่ (Staff Login)
                    </a>
                @endauth
            </div>
HTML;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Updated client.blade.php\n";

