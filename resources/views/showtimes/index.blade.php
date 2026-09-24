@extends('layouts.client')

@section('title', 'Ticket System - Cinema')

@section('content')

    <section class="relative isolate overflow-hidden bg-slate-900 text-white">
        <img src="{{ asset('images/solar.jpg') }}" alt="ภาพระบบสุริยะ" class="absolute inset-0 -z-20 h-full w-full object-cover object-center opacity-35">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-slate-950/95 via-slate-900/85 to-slate-900/65"></div>
        <div class="mx-auto grid min-h-[420px] max-w-7xl items-center gap-8 px-5 py-12 sm:px-8 sm:py-16 lg:grid-cols-[1.15fr_.85fr] lg:gap-12 lg:py-20">
            <div class="max-w-2xl">
                <p class="text-sm font-medium text-cyan-200">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                <h1 class="mt-4 text-3xl font-bold leading-snug tracking-tight sm:text-4xl lg:text-5xl">ท้องฟ้าจำลองรังสิต</h1>
                <p class="mt-4 max-w-xl text-base leading-8 text-slate-100 sm:text-lg">เรียนรู้ดาราศาสตร์ผ่านภาพยนตร์เต็มโดม พร้อมระบบฉายความละเอียด 4K</p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="#schedule" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-200/50">ดูรอบฉายและจองที่นั่ง</a>
                    <a href="#movies" class="inline-flex min-h-12 items-center justify-center rounded-lg border border-white/50 px-6 py-3 font-semibold text-white transition hover:bg-white/10 focus:outline-none focus:ring-4 focus:ring-white/30">ภาพยนตร์ที่กำลังฉาย</a>
                </div>
            </div>

            <div class="mx-auto w-full max-w-[310px] lg:ml-auto lg:mr-4">
                <div id="hero-movie-carousel" class="overflow-hidden rounded-2xl border border-white/25 bg-white p-2.5 shadow-xl" role="region" aria-roledescription="carousel" aria-label="โปสเตอร์ภาพยนตร์ที่กำลังฉาย">
                    <div class="relative aspect-[3/4] overflow-hidden rounded-xl bg-slate-200">
                        @forelse($movies as $movie)
                            <article data-title="{{ $movie->title_th }}" class="hero-movie-slide absolute inset-0 {{ $loop->first ? '' : 'hidden' }}" role="group" aria-roledescription="สไลด์" aria-label="{{ $loop->iteration }} จาก {{ $loop->count }}: {{ $movie->title_th }}">
                                @if($movie->poster_path)
                                    <img src="{{ Storage::url($movie->poster_path) }}" alt="โปสเตอร์ภาพยนตร์ {{ $movie->title_th }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center bg-slate-800 p-6 text-center text-lg font-semibold text-white">{{ $movie->title_th }}</div>
                                @endif
                            </article>
                        @empty
                            <div class="flex h-full items-center justify-center p-6 text-center text-sm text-slate-600">ขณะนี้ไม่มีภาพยนตร์ที่เปิดฉาย</div>
                        @endforelse

                    </div>
                    @if($movies->count() > 1)
                        <div class="flex items-center justify-between gap-3 px-2 pt-3 text-xs text-slate-700">
                            <div class="min-w-0">
                                <p class="text-[11px] text-slate-500">ภาพยนตร์ที่กำลังฉาย</p>
                                <p data-hero-title class="truncate text-sm font-semibold text-slate-900">{{ $movies->first()->title_th }}</p>
                            </div>
                            <span class="shrink-0 text-slate-500"><span data-hero-current>1</span> / {{ $movies->count() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($movies->count() > 1)
        @push('scripts')
            <script>
                const initializeHeroCarousel = function () {
                    const carousel = document.getElementById('hero-movie-carousel');
                    if (!carousel) return;

                    const slides = Array.from(carousel.querySelectorAll('.hero-movie-slide'));
                    const counter = carousel.querySelector('[data-hero-current]');
                    const title = carousel.querySelector('[data-hero-title]');
                    if (slides.length < 2 || !counter || !title) return;

                    let activeIndex = 0;
                    let timer = null;
                    const showSlide = function (index) {
                        activeIndex = (index + slides.length) % slides.length;
                        slides.forEach((slide, slideIndex) => {
                            const isActive = slideIndex === activeIndex;
                            slide.hidden = !isActive;
                            slide.classList.toggle('hidden', !isActive);
                        });
                        counter.textContent = String(activeIndex + 1);
                        title.textContent = slides[activeIndex].dataset.title || '';
                    };

                    const startTimer = function () {
                        if (timer === null && !document.hidden) {
                            timer = window.setInterval(() => showSlide(activeIndex + 1), 5000);
                        }
                    };
                    const stopTimer = function () {
                        if (timer !== null) window.clearInterval(timer);
                        timer = null;
                    };

                    document.addEventListener('visibilitychange', function () {
                        if (document.hidden) stopTimer();
                        else startTimer();
                    });
                    startTimer();
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initializeHeroCarousel, { once: true });
                } else {
                    initializeHeroCarousel();
                }
            </script>
        @endpush
    @endif

    <section id="movies" class="bg-slate-50 py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-cyan-700">Now showing</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">ภาพยนตร์ที่กำลังฉาย</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">เลือกเรื่องที่สนใจ แล้วดูรอบฉายเพื่อจองที่นั่ง</p>
                </div>
                <a href="#schedule" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-800 hover:text-cyan-600">ไปที่ตารางรอบฉาย <span aria-hidden="true">↓</span></a>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4 xl:grid-cols-5">
                @forelse($movies as $movie)
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <a href="#schedule" class="block focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-cyan-500" aria-label="ดูรอบฉายภาพยนตร์ {{ $movie->title_th }}">
                            <div class="relative aspect-[3/4] overflow-hidden bg-slate-200">
                                @if($movie->poster_path)
                                    <img src="{{ Storage::url($movie->poster_path) }}" alt="โปสเตอร์ภาพยนตร์ {{ $movie->title_th }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-cyan-900 p-4 text-center text-sm font-medium text-white">{{ $movie->title_th }}</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="line-clamp-2 min-h-12 font-semibold leading-6 text-slate-900">{{ $movie->title_th }}</h3>
                                @if($movie->title_en)
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $movie->title_en }}</p>
                                @endif
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-cyan-800">ดูรอบฉาย <span aria-hidden="true">→</span></span>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <p class="font-semibold text-slate-800">ยังไม่มีภาพยนตร์ที่เปิดฉาย</p>
                        <p class="mt-2 text-sm text-slate-500">โปรดกลับมาตรวจสอบโปรแกรมภาพยนตร์อีกครั้ง</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Showtimes Schedule (Timetable refactored to minimalist style) -->
    <section id="schedule" class="scroll-mt-20 border-t border-slate-200 bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 px-4 py-3 text-sm font-medium text-red-700" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="mb-7 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-cyan-700">Plan your visit</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">ตารางรอบฉาย</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ \Carbon\Carbon::parse($weekDates->first())->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($weekDates->last())->translatedFormat('d M Y') }} · เลือกรอบที่เปิดจองเพื่อเลือกที่นั่ง</p>
                </div>
                
                <!-- Week Navigator -->
                <div class="flex items-center gap-2">
                    <a aria-label="สัปดาห์ก่อนหน้า" href="{{ route('showtimes.index', ['week' => $weekOffset - 1]) }}" class="inline-flex min-h-11 items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:border-cyan-700 hover:bg-cyan-50 focus:outline-none focus:ring-4 focus:ring-cyan-100">← สัปดาห์ก่อน</a>
                    <a aria-label="สัปดาห์ถัดไป" href="{{ route('showtimes.index', ['week' => $weekOffset + 1]) }}" class="inline-flex min-h-11 items-center rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-200">สัปดาห์ถัดไป →</a>
                </div>
            </div>


            <!-- Minimalist Timetable (Redesigned like Mockup 3) -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full min-w-[900px] border-collapse text-center">
                    <thead class="sticky top-0 bg-slate-50">
                        <tr class="border-b border-slate-200">
                            <th scope="col" class="w-44 border-r border-slate-200 px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">วัน / วันที่</th>
                            @foreach ($timeSlots as $time)
                                <th scope="col" class="border-r border-slate-200 px-2 py-4 text-sm font-bold text-slate-800">{{ $time }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($weekDates as $index => $date)
                            <tr>
                                <!-- Day Column -->
                                <th scope="row" class="border-r border-slate-200 bg-slate-50 px-4 py-4 text-left align-middle">
                                    <div class="font-semibold text-slate-900">{{ $date->translatedFormat('l') }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $date->format('d/m/Y') }}</div>
                                </th>
                                
                                @foreach($timeSlots as $time)
                                    @php
                                        $cellShowtime = $showtimes->first(function($st) use ($date, $time) {
                                            return $st->show_date->toDateString() === $date->toDateString() && $st->show_time === $time.':00';
                                        });
                                        $canBook = $cellShowtime?->isBookable() ?? false;
                                    @endphp

                                    @if($time === '12:00')
                                        @if($index === 0)
                                            <td rowspan="7" class="w-24 border-r border-slate-200 bg-amber-50 px-2 text-amber-800">
                                                <div class="flex h-full items-center justify-center">
                                                    <span class="text-sm font-semibold [writing-mode:vertical-rl] sm:[writing-mode:horizontal-tb]">พักเครื่อง</span>
                                                </div>
                                            </td>
                                        @endif
                                    @else
                                        <td class="relative w-[140px] border-r border-slate-200 p-1 align-top {{ !$cellShowtime || !$canBook ? 'bg-slate-50' : 'bg-white' }}">
                                            @if($cellShowtime && $canBook)
                                                <a href="{{ route('bookings.create', $cellShowtime->id) }}" aria-label="จองรอบ {{ $time }} ภาพยนตร์ {{ $cellShowtime->movie->title_th }} เหลือ {{ $cellShowtime->available_seats }} ที่นั่ง" class="group relative block min-h-[120px] w-full overflow-hidden rounded-xl bg-slate-900 text-left focus:outline-none focus:ring-4 focus:ring-cyan-500">
                                                    @if($cellShowtime->movie->poster_path)
                                                        <img src="{{ Storage::url($cellShowtime->movie->poster_path) }}" alt="{{ $cellShowtime->movie->title_th }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-70 group-hover:opacity-100">
                                                    @endif
                                                    
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10"></div>
                                                    
                                                    <div class="absolute bottom-0 left-0 right-0 p-2 text-center flex flex-col justify-end h-full">
                                                        @php
                                                            $scheduleTitle = $cellShowtime->movie->title_en ?: $cellShowtime->movie->title_th;
                                                            if ($cellShowtime->movie->title_en === 'Star Lecture') {
                                                                $scheduleTitle = $cellShowtime->movie->title_th;
                                                            }
                                                        @endphp
                                                        <div class="w-full overflow-hidden text-ellipsis whitespace-nowrap font-bold text-xs leading-tight text-white drop-shadow-md" title="{{ $scheduleTitle }}">
                                                            {{ $scheduleTitle }}
                                                        </div>
                                                    </div>
                                                </a>
                                            @else
                                                <div class="flex min-h-[120px] flex-col items-center justify-center gap-1 rounded-xl border border-dashed border-slate-200 px-2 text-center text-xs text-slate-400">
                                                    <span class="font-medium text-slate-500">{{ $cellShowtime ? ($cellShowtime->available_seats < 1 ? 'ที่นั่งเต็ม' : 'ปิดจองแล้ว') : 'ไม่มีรอบ' }}</span>
                                                    @if($cellShowtime)
                                                        <span>{{ $time }} น.</span>
                                                    @endif
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
