<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กรอกข้อมูลการจองตั๋ว - ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- แถบประกาศด้านบน -->
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

    <!-- Header / Navbar สีขาวทึบ -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('showtimes.index') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้ท้องฟ้าจำลองรังสิต"
                        class="h-11 w-auto object-contain">
                </a>
            </div>
            <a href="/admin" title="เข้าสู่ระบบเจ้าหน้าที่"
                class="text-slate-400 hover:text-slate-700 p-2 rounded-lg transition-colors inline-flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="11" width="14" height="10" rx="2" ry="2"></rect>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4"></path>
                </svg>
            </a>
        </div>
    </header>

    <!-- เนื้อหาหลัก (Main Content) -->
    <main class="flex-1 flex items-center justify-center p-6 my-6">
        <div class="max-w-3xl w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-slate-200/80">
            <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-slate-900 tracking-tight">
                กรอกข้อมูลการจองตั๋ว
            </h1>

            <!-- ส่วนแสดงโปสเตอร์และข้อมูลรอบฉาย -->
            <div
                class="mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-200/60 flex flex-col sm:flex-row items-center gap-6">
                @if ($showtime->movie)
                    <div class="shrink-0 text-center">
                        @php
                            $movieTitle = $showtime->movie->title_th ?? '';

                            if (str_contains($movieTitle, 'แรงโน้มถ่วง')) {
                                $imageName = 'gravity.jpg';
                            } elseif (str_contains($movieTitle, 'Cosmos') || str_contains($movieTitle, 'คอสโมส')) {
                                $imageName = 'cosmos.jpg';
                            } elseif (str_contains($movieTitle, 'Polaris') || str_contains($movieTitle, 'ดาวเหนือ')) {
                                $imageName = 'polaris.jpg';
                            } elseif (
                                str_contains($movieTitle, 'World') ||
                                str_contains($movieTitle, 'นอกโลก') ||
                                str_contains($movieTitle, 'สุริยะ')
                            ) {
                                $imageName = 'worldbeyon.jpg';
                            } elseif (str_contains($movieTitle, 'Earth') || str_contains($movieTitle, 'จักรวาล')) {
                                $imageName = 'earthuniverse.jpg';
                            } elseif (str_contains($movieTitle, 'Life') || str_contains($movieTitle, 'ชีวิต')) {
                                $imageName = 'life.jpg';
                            } elseif (str_contains($movieTitle, 'Lucia') || str_contains($movieTitle, 'ลูเซีย')) {
                                $imageName = 'lucia.jpg';
                            } elseif (str_contains($movieTitle, 'Oddy') || str_contains($movieTitle, 'ออดี้')) {
                                $imageName = 'oddy.jpg';
                            } elseif (str_contains($movieTitle, 'Solar')) {
                                $imageName = 'solar.jpg';
                            } elseif (str_contains($movieTitle, 'Star') || str_contains($movieTitle, 'ดวงดาว')) {
                                $imageName = 'star.jpg';
                            } elseif (str_contains($movieTitle, 'Dancing') || str_contains($movieTitle, 'เต้น')) {
                                $imageName = 'dancing.jpg';
                            } else {
                                $imageName = 'gravity.jpg';
                            }
                        @endphp

                        <img src="{{ asset('images/' . $imageName) }}"
                            alt="{{ $showtime->movie->title_th ?? 'Movie Poster' }}"
                            class="w-36 h-52 object-cover rounded-xl shadow-md border border-slate-200">
                    </div>
                @endif

                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">
                        {{ $showtime->movie->title_th ?? '-' }}
                    </h2>

                    <div class="flex flex-wrap justify-center sm:justify-start gap-6 text-sm text-slate-600 mb-3">
                        <p><span class="text-slate-400">วันที่:</span>
                            <strong
                                class="text-slate-700">{{ \Carbon\Carbon::parse($showtime->show_date)->translatedFormat('d/m/Y') }}</strong>
                        </p>
                        <p><span class="text-slate-400">เวลา:</span>
                            <strong
                                class="text-slate-700">{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}
                                น.</strong>
                        </p>
                        <p><span class="text-slate-400">ที่นั่งว่าง:</span> <span
                                class="text-emerald-600 font-bold">{{ $showtime->available_seats }} ที่นั่ง</span></p>
                    </div>

                    @if ($showtime->movie && $showtime->movie->description)
                        <p class="text-slate-500 text-xs sm:text-sm leading-relaxed border-t border-slate-200/60 pt-3">
                            {{ $showtime->movie->description }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- แสดงข้อความ Error -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ฟอร์มกรอกข้อมูล -->
            <form action="{{ route('bookings.seats') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">

                <!-- ข้อมูลผู้จองพื้นฐาน -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อ-นามสกุล ผู้จอง</label>
                        <input type="text" name="booker_name" value="{{ old('booker_name') }}" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">เบอร์โทรศัพท์</label>
                        <input type="text" name="booker_phone" value="{{ old('booker_phone') }}"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">อีเมล
                        (สำหรับรับลิงก์/ข้อมูลยืนยัน)</label>
                    <input type="email" name="booker_email" value="{{ old('booker_email') }}" required
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                </div>

                <!-- เลือกประเภทผู้เข้าชม -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ประเภทผู้เข้าชม</label>
                        <select name="visitor_type" id="visitor_type" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                            <option value="individual" {{ old('visitor_type') == 'individual' ? 'selected' : '' }}>
                                บุคคลทั่วไป</option>
                            <option value="school" {{ old('visitor_type') == 'school' ? 'selected' : '' }}>โรงเรียน
                            </option>
                            <option value="government" {{ old('visitor_type') == 'government' ? 'selected' : '' }}>
                                อื่นๆ / หน่วยงานรัฐ</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5"
                            id="quantity-label">จำนวนที่นั่ง</label>
                        <input type="number" name="quantity" id="quantity" min="1" max="10"
                            value="{{ old('quantity', 1) }}" required
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                        <p class="text-xs text-slate-500 mt-1.5" id="quantity-hint">บุคคลทั่วไปจองได้สูงสุด 10 ที่นั่ง
                        </p>
                    </div>
                </div>

                <!-- ส่วนเสริมสำหรับโรงเรียน -->
                <div id="school-fields"
                    class="hidden space-y-4 p-5 bg-slate-50/80 border border-slate-200 rounded-xl mt-4">
                    <h3 class="text-sm font-bold text-slate-700 mb-2">ข้อมูลสถานศึกษาเพิ่มเติม</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">รูปแบบการศึกษา</label>
                            <select name="edu_system"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <option value="in_system">การศึกษาในระบบ</option>
                                <option value="out_system">การศึกษานอกระบบ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ชื่อสถานศึกษา</label>
                            <input type="text" name="school_name" placeholder="ระบุชื่อโรงเรียน/สถานศึกษา"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ระดับชั้นการศึกษา</label>
                            <select name="edu_level"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <option value="kindergarten">ก่อนประถมศึกษา</option>
                                <option value="primary">ประถมศึกษา</option>
                                <option value="secondary">มัธยมศึกษา</option>
                                <option value="university">อุดมศึกษา</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนครู</label>
                            <input type="number" name="teachers_count" placeholder="ใส่เป็นตัวเลข เช่น 10"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนนักเรียน</label>
                            <input type="number" name="students_count" placeholder="ใส่เป็นตัวเลข เช่น 50"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">อื่นๆ (เช่น
                                ผู้ปกครอง)</label>
                            <input type="number" name="others_count" placeholder="ใส่เป็นตัวเลขเช่น 5"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- ส่วนเสริมสำหรับหน่วยงานรัฐ / อื่นๆ -->
                <div id="gov-fields"
                    class="hidden space-y-4 p-5 bg-slate-50/80 border border-slate-200 rounded-xl mt-4">
                    <h3 class="text-sm font-bold text-slate-700 mb-2">ข้อมูลหน่วยงานเพิ่มเติม</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ชื่อหน่วยงาน</label>
                            <input type="text" name="agency_name" placeholder="ระบุชื่อบริษัท หรือหน่วยงาน"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-600 mb-1">จำนวนผู้เข้าชมระดับหัวหน้า</label>
                            <input type="number" name="head_count" placeholder="ใส่เป็นตัวเลขเช่น 2"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนพนักงาน</label>
                            <input type="number" name="staff_count" placeholder="ใส่เป็นตัวเลขเช่น 20"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">อื่นๆ (เช่น
                                ผู้ติดตาม)</label>
                            <input type="number" name="agency_others_count" placeholder="ใส่เป็นตัวเลขเช่น 5"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md transition duration-200 mt-6">
                    ดำเนินการต่อ (เลือกที่นั่ง)
                </button>
            </form>
        </div>
    </main>

    <!-- Footer พื้นหลังสีขาว -->
    <footer class="bg-white text-slate-600 py-10 border-t border-slate-200 mt-auto">
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

    <!-- Script ควบคุมการแสดงผลฟอร์มแยกประเภทและจำนวนที่นั่ง -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visitorTypeSelect = document.getElementById('visitor_type');
            const quantityInput = document.getElementById('quantity');
            const quantityHint = document.getElementById('quantity-hint');
            const schoolFields = document.getElementById('school-fields');
            const govFields = document.getElementById('gov-fields');
            const maxAvailableSeats = {{ $showtime->available_seats }};

            function updateSeatLimit() {
                const selectedType = visitorTypeSelect.value;
                let limit = 10;

                // ซ่อนฟอร์มเสริมทั้งหมดก่อน
                schoolFields.classList.add('hidden');
                govFields.classList.add('hidden');

                if (selectedType === 'school') {
                    limit = 160;
                    quantityHint.textContent = 'โรงเรียน จองได้สูงสุด 160 ที่นั่ง (ตามจำนวนที่นั่งว่าง)';
                    schoolFields.classList.remove('hidden');
                } else if (selectedType === 'government') {
                    limit = 160;
                    quantityHint.textContent = 'หน่วยงานรัฐ/อื่นๆ จองได้สูงสุด 160 ที่นั่ง (ตามจำนวนที่นั่งว่าง)';
                    govFields.classList.remove('hidden');
                } else {
                    quantityHint.textContent = 'บุคคลทั่วไป จองได้สูงสุด 10 ที่นั่ง (ตามจำนวนที่นั่งว่าง)';
                }

                // จำกัดไม่ให้เกินที่นั่งว่างจริงของรอบนั้นๆ ด้วย
                const finalMax = Math.min(limit, maxAvailableSeats);
                quantityInput.max = finalMax;

                if (parseInt(quantityInput.value) > finalMax) {
                    quantityInput.value = finalMax;
                }
            }

            visitorTypeSelect.addEventListener('change', updateSeatLimit);
            updateSeatLimit();
        });
    </script>
</body>

</html>
