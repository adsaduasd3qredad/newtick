@extends('layouts.client')

@section('title', 'กรอกข้อมูลการจองตั๋ว')

@section('content')
<div class="flex-1 flex items-center justify-center p-6 my-6">
        <div class="max-w-3xl w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-slate-200/80">
            <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-slate-900 tracking-tight">
                กรอกข้อมูลการจองตั๋ว
            </h1>

            <!-- ส่วนแสดงโปสเตอร์และข้อมูลรอบฉาย -->
            <div
                class="mb-8 bg-slate-50 p-6 rounded-2xl border border-slate-200/60 flex flex-col sm:flex-row items-center gap-6">
                @if ($showtime->movie)
                    <div class="shrink-0 text-center">
                        @if ($showtime->movie->poster_path)
                            <img src="{{ Storage::url($showtime->movie->poster_path) }}"
                                alt="{{ $showtime->movie->title_th ?? 'Movie Poster' }}"
                                class="w-36 h-52 object-cover rounded-xl shadow-md border border-slate-200">
                        @else
                            <div class="w-36 h-52 flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-xs text-slate-400">
                                ไม่มีโปสเตอร์
                            </div>
                        @endif
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
            <form id="booking-form" action="{{ route('bookings.seats') }}" method="POST" class="space-y-5"
                data-price-per-seat="{{ (int) $pricePerSeat }}"
                data-max-available-seats="{{ (int) $showtime->available_seats }}">
                @csrf
                <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                @if ($returnToPos)
                    <input type="hidden" name="return_to_pos" value="1">
                @endif

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
                              maxlength="10" required
                              oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
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
                                หน่วยงานรัฐ</option>

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

                <div id="booking-estimate" class="flex flex-col gap-1 rounded-xl border border-cyan-100 bg-cyan-50/70 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="whitespace-nowrap text-sm font-semibold text-slate-700">
                        ค่าตั๋วประมาณ <span id="estimated-total" class="text-base font-bold text-cyan-800">{{ number_format($pricePerSeat, 2) }}</span> บาท
                    </p>
                    <p class="text-xs text-slate-500">ยอด PromptPay อาจมีค่าธรรมเนียม ซึ่งจะแสดงก่อนชำระเงิน</p>
                </div>

                <!-- ส่วนเสริมสำหรับโรงเรียน -->
                <div id="school-fields"
                    class="hidden space-y-4 p-5 bg-slate-50/80 border border-slate-200 rounded-xl mt-4">
                    <h3 class="text-sm font-bold text-slate-700 mb-2">ข้อมูลสถานศึกษาเพิ่มเติม</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">รูปแบบการศึกษา</label>
                            <select name="school_type"
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
                            <select name="education_level"
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
                            <input type="number" name="teachers_count" min="0" max="160" value="{{ old('teachers_count', 0) }}" data-group-count placeholder="ใส่เป็นตัวเลข เช่น 10"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนนักเรียน</label>
                            <input type="number" name="students_count" min="0" max="160" value="{{ old('students_count', 0) }}" data-group-count placeholder="ใส่เป็นตัวเลข เช่น 50"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">อื่นๆ (เช่น
                                ผู้ปกครอง)</label>
                            <input type="number" name="parents_count" min="0" max="160" value="{{ old('parents_count', 0) }}" data-group-count placeholder="ใส่เป็นตัวเลขเช่น 5"
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- ส่วนเสริมสำหรับหน่วยงานรัฐ / อื่นๆ -->
                <div id="gov-fields" class="hidden space-y-4 p-5 bg-slate-50/80 border border-slate-200 rounded-xl mt-4">
                    <h3 class="text-sm font-bold text-slate-700 mb-2">ข้อมูลหน่วยงานรัฐ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ชื่อหน่วยงาน</label>
                            <input type="text" name="gov_agency_name" value="{{ old('gov_agency_name') }}" placeholder="ระบุชื่อหน่วยงานรัฐ" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">แผนก / หน่วยงานย่อย (ถ้ามี)</label>
                            <input type="text" name="gov_department" value="{{ old('gov_department') }}" placeholder="ระบุแผนกหรือหน่วยงานย่อย" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนเจ้าหน้าที่</label>
                            <input type="number" name="gov_officers_count" min="0" max="160" value="{{ old('gov_officers_count', 0) }}" data-group-count placeholder="เช่น 5" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">จำนวนเจ้าหน้าที่ร่วม</label>
                            <input type="number" name="gov_staff_count" min="0" max="160" value="{{ old('gov_staff_count', 0) }}" data-group-count placeholder="เช่น 20" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">ผู้ติดตาม / อื่น ๆ</label>
                            <input type="number" name="gov_others_count" min="0" max="160" value="{{ old('gov_others_count', 0) }}" data-group-count placeholder="เช่น 5" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-3.5 px-6 rounded-xl shadow-md transition duration-200 mt-6">
                    ดำเนินการต่อ (เลือกที่นั่ง)
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('visitor_type');
        const quantityInput = document.getElementById('quantity');
        const quantityHint = document.getElementById('quantity-hint');
        const form = document.getElementById('booking-form');
        const submitButton = form.querySelector('button[type="submit"]');
        const estimatedTotal = document.getElementById('estimated-total');
        const pricePerSeat = Number.parseInt(form.dataset.pricePerSeat || '0', 10) || 0;
        const maxAvailableSeats = Number.parseInt(form.dataset.maxAvailableSeats || '0', 10) || 0;
        const sections = {
            school: document.getElementById('school-fields'),
            government: document.getElementById('gov-fields'),
        };
        const countFields = {
            school: ['teachers_count', 'students_count', 'parents_count'],
            government: ['gov_officers_count', 'gov_staff_count', 'gov_others_count'],
        };

        function updateBookingEstimate() {
            const type = typeSelect.value;
            const isIndividual = type === 'individual';
            let quantity = 0;
            let limit = isIndividual ? 10 : 160;

            Object.entries(sections).forEach(([groupType, section]) => {
                const active = groupType === type;
                section.classList.toggle('hidden', !active);
                section.querySelectorAll('input, select').forEach((input) => {
                    input.disabled = !active;
                });
            });

            if (isIndividual) {
                quantityInput.readOnly = false;
                quantityInput.max = Math.min(limit, maxAvailableSeats);
                quantity = Number.parseInt(quantityInput.value, 10) || 0;
                if (quantity > Number(quantityInput.max)) {
                    quantity = Number(quantityInput.max);
                    quantityInput.value = quantity;
                }
                quantityHint.textContent = `บุคคลทั่วไปจองได้ไม่เกิน ${quantityInput.max} ที่นั่ง`;
            } else {
                quantityInput.readOnly = true;
                quantity = countFields[type].reduce((sum, name) => {
                    const field = document.querySelector(`[name="${name}"]`);
                    return sum + (Number.parseInt(field?.value ?? '0', 10) || 0);
                }, 0);
                quantityInput.value = quantity || '';
                quantityInput.max = limit;
                const available = quantity <= maxAvailableSeats;
                quantityHint.textContent = quantity === 0
                    ? 'กรอกจำนวนผู้เข้าชมแต่ละประเภท ระบบจะรวมเป็นจำนวนที่นั่งให้อัตโนมัติ'
                    : `รวม ${quantity} คน / ${quantity} ที่นั่ง · รอบนี้เหลือ ${maxAvailableSeats} ที่นั่ง`;
                quantityHint.classList.toggle('text-red-600', quantity > maxAvailableSeats);
            }

            estimatedTotal.textContent = (quantity * pricePerSeat).toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const withinCapacity = quantity > 0 && quantity <= maxAvailableSeats && quantity <= limit;
            submitButton.disabled = !withinCapacity;
            submitButton.classList.toggle('opacity-50', !withinCapacity);
            submitButton.classList.toggle('cursor-not-allowed', !withinCapacity);
        }

        typeSelect.addEventListener('change', function () {
            if (typeSelect.value === 'individual') {
                quantityInput.value = '1';
            }
            updateBookingEstimate();
        });
        quantityInput.addEventListener('input', updateBookingEstimate);
        document.querySelectorAll('[data-group-count]').forEach((input) => {
            input.addEventListener('input', updateBookingEstimate);
        });
        updateBookingEstimate();
    });
</script>
@endpush
