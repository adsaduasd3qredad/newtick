@extends('layouts.client')

@section('title', 'Ticket System - Cinema')

@section('content')

    <!-- Hero Section (Promotional Banner) -->
    <section class="relative bg-black overflow-hidden border-b border-gray-900">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/solar.jpg') }}" alt="Solar System" class="w-full h-full object-cover opacity-90">
            <!-- Overlay to ensure text readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent"></div>
            <!-- Bottom gradient for smooth transition to the next section -->
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between min-h-[500px] relative z-10 pb-10">
            <!-- Left: Text -->
            <div class="w-full md:w-3/5 p-8 md:p-12">
                <div class="space-y-6 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight drop-shadow-2xl">
                        เปิดประสบการณ์การเรียนรู้<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 font-extrabold drop-shadow-md">
                            ผ่านโดมท้องฟ้าจำลอง
                        </span>
                    </h1>
                    <p class="text-gray-100 text-lg md:text-xl mt-4 max-w-xl drop-shadow-lg font-medium leading-relaxed">
                        ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต <br>
                        <span class="font-light text-gray-300">แหล่งเรียนรู้ดาราศาสตร์และอวกาศสำหรับทุกคน</span>
                    </p>
                    
                    <div class="pt-8">
                        <a href="#schedule" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold text-lg rounded-full shadow-[0_4px_14px_0_rgba(242,101,34,0.39)] hover:shadow-[0_6px_20px_rgba(242,101,34,0.23)] hover:shadow-orange-500/50 transition-all transform hover:-translate-y-1">
                            ดูรอบฉายวันนี้
                            <svg class="w-5 h-5 ml-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Right: Empty space to let the planets show clearly -->
            <div class="w-full md:w-2/5 hidden md:block">
            </div>
        </div>
    </section>

    

    <!-- Movies Grid -->
    <section class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @forelse($movies as $movie)
                <div class="group cursor-pointer relative overflow-hidden bg-gray-100 aspect-[2/3]">
                    @if($movie->poster_path)
                        <img src="{{ Storage::url($movie->poster_path) }}" alt="{{ $movie->title_th }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400 font-medium">
                            NO POSTER
                        </div>
                    @endif
                    
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-60 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                        <h3 class="text-white font-semibold text-lg">{{ $movie->title_th }}</h3>
                        <p class="text-gray-300 text-sm mt-1 truncate">{{ $movie->title_en }}</p>
                        <a href="#schedule" class="mt-4 bg-white text-black text-center text-sm font-medium py-2 uppercase tracking-wide hover:bg-gray-200 transition">
                            Book Now
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    ไม่มีภาพยนตร์ที่กำลังเข้าฉายในขณะนี้
                </div>
            @endforelse
        </div>
    </section>

    <!-- Showtimes Schedule (Timetable refactored to minimalist style) -->
    <section id="schedule" class="bg-white py-16 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-[#1c1c1c] uppercase tracking-wide">Showtimes Schedule</h2>
                    <p class="text-gray-500 text-sm mt-1">ตารางรอบฉายภาพยนตร์ ({{ \Carbon\Carbon::parse($weekDates->first())->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($weekDates->last())->translatedFormat('d M Y') }})</p>
                </div>
                
                <!-- Week Navigator -->
                <div class="flex items-center space-x-2 mt-4 md:mt-0">
                    <a href="{{ route('showtimes.index', ['week' => $weekOffset - 1]) }}" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium transition">&larr; PREV WEEK</a>
                    <a href="{{ route('showtimes.index', ['week' => $weekOffset + 1]) }}" class="px-4 py-2 bg-[#1c1c1c] text-white hover:bg-black text-sm font-medium transition">NEXT WEEK &rarr;</a>
                </div>
            </div>

            <!-- Minimalist Timetable (Redesigned like Mockup 3) -->
            <div class="overflow-x-auto pb-4 shadow-2xl rounded-lg">
                <table class="w-full text-center border-collapse min-w-[800px] border border-gray-300 bg-white">
                    <thead class="bg-white">
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-4 px-4 font-bold text-gray-900 border border-gray-300 w-48">Date / Day</th>
                            @foreach ($timeSlots as $time)
                                <th class="py-4 px-2 font-bold text-gray-900 border border-gray-300">{{ $time }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @foreach($weekDates as $index => $date)
                            <tr>
                                <!-- Day Column -->
                                <td class="py-4 px-4 border border-gray-300 bg-[#5482f5] text-white font-medium align-middle">
                                    <div class="font-bold text-lg">{{ $date->format('l') }}</div>
                                    <div class="text-xs opacity-90">{{ $date->format('d/m/y') }}</div>
                                </td>
                                
                                @foreach($timeSlots as $time)
                                    @php
                                        $cellShowtime = $showtimes->first(function($st) use ($date, $time) {
                                            return $st->show_date->toDateString() === $date->toDateString() && $st->show_time === $time.':00';
                                        });
                                    @endphp

                                    @if($time === '12:00')
                                        @if($index === 0)
                                            <td rowspan="7" class="border border-gray-300 bg-gray-50 text-gray-800 font-bold text-lg align-middle w-24">
                                                <div class="flex items-center justify-center h-full">
                                                    พักเครื่อง
                                                </div>
                                            </td>
                                        @endif
                                    @else
                                        <td class="p-0 border border-gray-300 align-top w-[140px] relative {{ !$cellShowtime ? 'bg-[#999999]' : 'bg-black' }}">
                                            @if($cellShowtime)
                                                <!-- Valid Showtime (Can Book) -->
                                                <a href="{{ route('bookings.create', $cellShowtime->id) }}" class="block relative w-full h-full min-h-[110px] overflow-hidden group cursor-pointer">
                                                    @if($cellShowtime->movie->poster_path)
                                                        <img src="{{ Storage::url($cellShowtime->movie->poster_path) }}" alt="{{ $cellShowtime->movie->title_th }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-70 group-hover:opacity-100">
                                                    @endif
                                                    
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10"></div>
                                                    
                                                    <div class="absolute bottom-0 left-0 right-0 p-2 text-center flex flex-col justify-end h-full">
                                                        <div class="font-bold text-xs text-white leading-tight drop-shadow-md mb-1" title="{{ $cellShowtime->movie->title_th }}">
                                                            {{ Str::limit($cellShowtime->movie->title_th, 20) }}
                                                        </div>
                                                        <div class="inline-flex mx-auto items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $cellShowtime->available_seats > 0 ? 'bg-cyan-500 text-white' : 'bg-red-500 text-white' }}">
                                                            {{ $cellShowtime->available_seats > 0 ? $cellShowtime->available_seats . ' Seats' : 'FULL' }}
                                                        </div>
                                                    </div>
                                                </a>
                                            @else
                                                <!-- Empty slot -->
                                                <div class="flex items-center justify-center h-full min-h-[110px] text-gray-200 font-bold text-xl">
                                                    -
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            
                
            </div>
        </div>
    </section>

@endsection
