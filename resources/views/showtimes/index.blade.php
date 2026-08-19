<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองตั๋ว - ท้องฟ้าจำลองรังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --void: #080b14;
            --void-2: #10182c;
            --nebula: #7c3aed;
            --aurora: #22d3ee;
            --star: #fbbf24;
            --paper: #f6f7fb;
            --ink: #1c2333;
        }

        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }

        .font-display {
            font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif;
        }

        /* signature element: drifting starfield used once, in the hero only */
        .starfield {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1.5px 1.5px at 20% 30%, #fff, transparent),
                radial-gradient(1.5px 1.5px at 65% 15%, #fff, transparent),
                radial-gradient(1px 1px at 80% 55%, #fff, transparent),
                radial-gradient(1.5px 1.5px at 40% 70%, #fff, transparent),
                radial-gradient(1px 1px at 90% 80%, #fff, transparent),
                radial-gradient(1.5px 1.5px at 10% 85%, #fff, transparent),
                radial-gradient(1px 1px at 55% 45%, #fff, transparent);
            background-repeat: repeat;
            background-size: 260px 260px;
            opacity: .55;
            animation: twinkle 5s ease-in-out infinite alternate;
        }

        @keyframes twinkle {
            0% {
                opacity: .35;
            }

            100% {
                opacity: .75;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .starfield {
                animation: none;
            }
        }

        .orbit-ring {
            position: absolute;
            border: 1px solid rgba(34, 211, 238, .25);
            border-radius: 9999px;
        }

        .show-card {
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #f3f6ff 100%);
            border: 1px solid #e2e8f5;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .show-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, var(--aurora), var(--nebula));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity .15s ease;
            pointer-events: none;
        }

        .show-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -8px rgba(30, 41, 96, .25);
        }

        .show-card:hover::before {
            opacity: 1;
        }

        .btn-book {
            background: linear-gradient(135deg, var(--aurora), #2563eb);
        }

        .btn-book:hover {
            background: linear-gradient(135deg, #06b6d4, #1d4ed8);
        }

        .day-col {
            background: linear-gradient(180deg, #f8fafc 0%, #eef1fb 100%);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<!-- แถบประกาศด้านบน (จำลองตามเว็บต้นแบบ) -->
<div
    class="bg-gradient-to-r from-cyan-500 via-sky-500 to-violet-600 text-white text-sm font-medium py-2.5 px-4 text-center shadow-sm">
    <a href="https://sci-rangsit.dole.go.th" target="_blank"
        class="hover:opacity-90 transition inline-flex items-center justify-center gap-2 font-display tracking-wide">
        <span aria-hidden="true">✦</span>
        <span>คลิก <strong class="text-yellow-300 underline underline-offset-2 font-bold">ที่นี่</strong>
            เพื่อเข้าสู่เว็บไซต์ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</span>
        <span aria-hidden="true">✦</span>
    </a>
</div>

<body class="bg-[var(--paper)] text-[var(--ink)] min-h-screen">

    <!-- Navbar / Header หน้าแรก -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex justify-between items-center">
            <!-- โลโก้ทางซ้าย -->
            <div class="flex items-center">
                <a href="{{ route('showtimes.index') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                        class="h-11 w-auto object-contain">
                </a>
            </div>

            <!-- 📌 กลุ่มไอคอนทางขวา (กุญแจสำหรับพนักงาน และไขควงสำหรับแอดมิน) -->
            <div class="flex items-center space-x-1">
                <!-- 🔑 ไอคอนกุญแจ (สำหรับ Staff / พนักงานเคาน์เตอร์ ไปหน้า /login) -->
                <a href="{{ route('login') }}" title="เข้าสู่ระบบพนักงาน (Staff)"
                    class="text-slate-400 hover:text-cyan-600 hover:bg-slate-100 p-2.5 rounded-xl transition-colors inline-flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="5" y="11" width="14" height="10" rx="2" ry="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4"></path>
                    </svg>
                </a>

                <!-- 🪛 ไอคอนไขควง (สำหรับ Admin ไปหลังบ้าน Filament โดยตรง) -->
                <a href="{{ url('/admin') }}" title="ระบบจัดการหลังบ้าน (Admin)"
                    class="text-slate-400 hover:text-amber-600 hover:bg-slate-100 p-2.5 rounded-xl transition-colors inline-flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.42 15.17L17.25 21A2.121 2.121 0 0020.25 18l-5.83-5.83M15 5.5l3.5 3.5m-9.5 3.5l-5 5V21h3.5l5-5m-2.5-7.5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section
        class="relative bg-[var(--void)] text-white py-24 px-6 text-center overflow-hidden border-b border-slate-800">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/solar.jpg') }}" alt="Solar System Background"
                class="w-full h-full object-cover object-[0%_40%] opacity-60">
            <div
                class="absolute inset-0 bg-gradient-to-r from-[var(--void)]/70 via-[var(--void)]/25 to-[var(--void)]/70">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-[var(--void)]/30 via-transparent to-[var(--void)]"></div>
            <div class="starfield"></div>
        </div>

        <!-- decorative orbit rings, purely visual -->
        <div class="orbit-ring w-[520px] h-[520px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 hidden md:block">
        </div>
        <div class="orbit-ring w-[720px] h-[720px] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 hidden md:block"
            style="border-color: rgba(124,58,237,.18);"></div>

        <div class="relative z-10 max-w-5xl mx-auto">
            <span
                class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 text-sm font-semibold text-cyan-100 bg-cyan-500/10 border border-cyan-400/40 rounded-full backdrop-blur-md font-display tracking-wide">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-300 animate-pulse"></span>
                ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต
            </span>
            <h1
                class="font-display text-3xl md:text-5xl font-bold mb-4 tracking-tight leading-snug drop-shadow-md text-white">
                จองตั๋วเข้าชมรอบการแสดง<span class="text-cyan-300">ท้องฟ้าจำลอง</span>
            </h1>
            <p class="text-slate-200 text-sm md:text-base max-w-4xl mx-auto font-light leading-relaxed drop-shadow">
                เรียนรู้เรื่องราวทางดาราศาสตร์ผ่านภาพยนตร์เต็มโดมความคมชัดระดับ 4K
                พร้อมระบบจองรอบการแสดงออนไลน์ที่สะดวกรวดเร็ว
            </p>
        </div>
    </section>

    <!-- Main Content: Matrix Table -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <h3 class="font-display text-xl font-bold text-slate-800 border-l-4 border-cyan-500 pl-3">
                ตารางรอบการแสดงประจำสัปดาห์
            </h3>

            <!-- ปรับส่วนนี้ใหม่ -->
            <div class="flex items-center gap-2">
                <a href="{{ route('showtimes.index', ['week' => $weekOffset - 1]) }}"
                    class="px-3.5 py-1.5 text-sm bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-cyan-300 transition font-display">
                    ‹
                </a>

                <!-- ช่องเลือกวันที่แบบ Pop-up -->
                <input type="text" id="datePicker"
                    class="w-40 text-center text-xs py-1.5 bg-white border border-slate-200 rounded-full cursor-pointer hover:border-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-200"
                    value="{{ $weekDates->first()->format('d/m/Y') }} - {{ $weekDates->last()->format('d/m/Y') }}"
                    readonly>

                <a href="{{ route('showtimes.index', ['week' => $weekOffset + 1]) }}"
                    class="px-3.5 py-1.5 text-sm bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-cyan-300 transition font-display">
                    ›
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse text-sm">
                    <thead>
                        <tr class="bg-[var(--void)] text-white">
                            <th class="p-4 border-r border-white/10 w-36 font-display font-semibold text-cyan-200">วัน /
                                เวลา</th>
                            @foreach ($timeSlots as $time)
                                <th
                                    class="p-4 border-r border-white/10 font-display font-semibold last:border-r-0 {{ $time === '12:00' ? 'bg-black/20 text-slate-400' : '' }}">
                                    {{ $time }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @php
                            $thDayNames = [
                                'Sunday' => 'อาทิตย์',
                                'Monday' => 'จันทร์',
                                'Tuesday' => 'อังคาร',
                                'Wednesday' => 'พุธ',
                                'Thursday' => 'พฤหัสบดี',
                                'Friday' => 'ศุกร์',
                                'Saturday' => 'เสาร์',
                            ];
                        @endphp

                        @foreach ($weekDates as $date)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-display font-bold day-col border-r border-slate-200 text-slate-700">
                                    {{ $thDayNames[$date->englishDayOfWeek] }}<br>
                                    <span
                                        class="text-xs font-normal text-slate-400">{{ $date->format('d/m/y') }}</span>
                                </td>

                                @foreach ($timeSlots as $time)
                                    <td
                                        class="p-3 border-r border-slate-200 last:border-r-0 align-middle {{ $time === '12:00' ? 'bg-slate-50 text-slate-400 text-xs font-medium uppercase tracking-wider font-display' : '' }}">
                                        @if ($time === '12:00')
                                            พักเที่ยง
                                        @else
                                            @php
                                                $dayOfWeekIso = $date->dayOfWeekIso;

                                                // 1. เช็ครอบพิเศษจากตาราง Showtimes
                                                $specialShow = $showtimes->first(
                                                    fn($item) => $item->show_date->format('Y-m-d') ===
                                                        $date->format('Y-m-d') &&
                                                        \Carbon\Carbon::parse($item->show_time)->format('H:i') ===
                                                            $time,
                                                );

                                                // 2. เช็ครอบปกติจากตาราง Weekly Schedules (แม่แบบประจำสัปดาห์)
                                                $weeklyShow = $weeklySchedules->first(
                                                    fn($item) => (int) $item->day_of_week === $dayOfWeekIso &&
                                                        \Carbon\Carbon::parse($item->show_time)->format('H:i') ===
                                                            $time,
                                                );

                                                $activeShow = $specialShow ?? $weeklyShow;
                                            @endphp

                                            @if ($activeShow)
                                                <div class="show-card p-3 rounded-xl text-left">

                                                    <p
                                                        class="font-display font-bold text-slate-800 text-xs mb-1.5 line-clamp-1">
                                                        {{ $activeShow->movie->title_th ?? '-' }}
                                                    </p>
                                                    <span class="text-[11px] text-slate-500 block mb-2.5">
                                                        @if (isset($specialShow))
                                                            ว่าง: <strong
                                                                class="text-[var(--star)]">{{ $specialShow->available_seats }}</strong>
                                                            ที่นั่ง
                                                        @else
                                                            ที่นั่งรวม: <strong
                                                                class="text-[var(--star)]">{{ $activeShow->total_seats ?? '-' }}</strong>
                                                        @endif
                                                    </span>

                                                    <a href="{{ isset($specialShow) ? route('bookings.create', $specialShow->id) : route('bookings.create', ['weekly_id' => $activeShow->id, 'date' => $date->format('Y-m-d'), 'time' => $time]) }}"
                                                        class="btn-book inline-block text-white text-xs font-medium font-display px-3 py-1.5 rounded-full shadow-sm transition">
                                                        จองรอบนี้
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-slate-300">–</span>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Contact + Map Section -->
    <section class="bg-[var(--void)] text-white mt-20">
        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-0">
            <div class="px-6 md:px-12 py-16 flex flex-col justify-center">
                <h3 class="font-display text-2xl md:text-3xl font-bold text-cyan-300 mb-6">ติดต่อเรา</h3>

                <div class="space-y-2 text-slate-200 text-sm leading-relaxed mb-6">
                    <p>ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</p>
                    <p>ที่อยู่ 5 หมู่ 2 ต.รังสิต อ.ธัญบุรี จ.ปทุมธานี 12110</p>
                    <p>โทร 02 577 5456 – 9 ต่อ 304</p>
                </div>

                <a href="https://www.facebook.com/profile.php?id=100057238039008#" target="_blank"
                    class="inline-flex items-center gap-2 text-sm text-slate-200 hover:text-cyan-300 transition mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="w-5 h-5 fill-current text-blue-400">
                        <path
                            d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.44 2.91h-2.34V22c4.78-.79 8.44-4.94 8.44-9.94Z" />
                    </svg>
                    ท้องฟ้าจำลองรังสิต
                </a>

                <a href="https://www.google.com/maps/dir/?api=1&destination=Rangsit+Science+Center+for+Education,+Pathum+Thani"
                    target="_blank"
                    class="btn-book inline-flex items-center gap-2 w-fit text-white text-sm font-medium font-display px-5 py-2.5 rounded-full shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4 fill-current">
                        <path
                            d="M12 2C7.86 2 4.5 5.36 4.5 9.5c0 5.62 6.62 11.7 7.5 12.5.88-.8 7.5-6.88 7.5-12.5C19.5 5.36 16.14 2 12 2Zm0 10.25a2.75 2.75 0 1 1 0-5.5 2.75 2.75 0 0 1 0 5.5Z" />
                    </svg>
                    แผนที่การเดินทาง
                </a>
            </div>

            <div class="min-h-[380px] md:min-h-full">
                <iframe class="w-full h-full min-h-[380px] border-0" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps?q=Rangsit+Science+Center+for+Education,+5+Moo+2+Tambon+Rangsit,+Amphoe+Thanyaburi,+Pathum+Thani+12110&output=embed">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer พื้นหลังสีขาว -->
    <footer class="bg-white text-slate-600 py-10 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                    class="h-10 w-auto object-contain bg-white p-1 rounded-md shadow-sm border border-slate-100">
                <div>
                    <p class="text-slate-900 font-display font-semibold text-sm">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต
                    </p>
                    <p class="text-xs text-slate-500">RANGSIT SCIENCE CENTRE FOR EDUCATION</p>
                </div>
            </div>

            <div class="text-center md:text-right text-xs space-y-1">
                <p class="text-slate-500">Copyright © 2026 ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต. All rights reserved</p>
                <p class="text-slate-800 font-medium">โทร 02 577 5456 – 9 ต่อ 304</p>
            </div>
        </div>
    </footer>
    <script>
        flatpickr("#datePicker", {
            dateFormat: "d/m/Y",
            // เมื่อเลือกวันที่ ให้ระบบ Redirect ไปยัง URL พร้อมค่า date
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const date = instance.formatDate(selectedDates[0], "Y-m-d");
                    window.location.href = "{{ route('showtimes.index') }}?date=" + date;
                }
            }
        });
    </script>
</body>

</html>
