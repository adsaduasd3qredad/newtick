@extends('layouts.client')

@section('title', 'ชำระเงิน')

@section('content')
<div class="flex-1 flex items-center justify-center p-6 my-6">
        <div class="max-w-3xl w-full bg-white p-6 sm:p-12 rounded-3xl shadow-sm border border-slate-200/85">

            <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-slate-900 tracking-tight">ชำระเงิน</h1>

            <!-- Step indicator (สถานะปัจจุบันอยู่ที่ 3: ชำระเงิน) -->
            <div class="flex items-center justify-center gap-2 sm:gap-6 mb-10 overflow-x-auto py-2">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'กรอกข้อมูล'],
                        ['n' => 2, 'label' => 'เลือกที่นั่ง'],
                        ['n' => 3, 'label' => 'ชำระเงิน'],
                        ['n' => 4, 'label' => 'เสร็จสมบูรณ์'],
                    ];
                    $currentStep = 3;
                @endphp
                @foreach ($steps as $step)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-sm
                                    {{ $step['n'] < $currentStep ? 'bg-cyan-600 text-white' : ($step['n'] == $currentStep ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-400') }}">
                                @if ($step['n'] < $currentStep)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $step['n'] }}
                                @endif
                            </div>
                            <span
                                class="text-[10px] sm:text-[11px] mt-1.5 text-center max-w-[70px] {{ $step['n'] <= $currentStep ? 'text-slate-800 font-semibold' : 'text-slate-400' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if (!$loop->last)
                            <div
                                class="w-4 sm:w-14 h-0.5 {{ $step['n'] < $currentStep ? 'bg-cyan-600' : 'bg-slate-200' }} mx-1 sm:mx-2 mt-[-14px]">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- ข้อมูลการจองและยอดชำระ -->
            <div class="text-center mb-6">
                <p class="text-slate-600 text-sm sm:text-base mb-1">รหัสจอง <strong
                        class="text-slate-900 font-bold text-lg">#{{ $booking->id }}</strong></p>
                <p class="text-slate-600 text-sm sm:text-base">ยอดชำระ <strong
                        id="payment-total" class="text-emerald-600 font-bold text-xl sm:text-2xl">{{ number_format($qrTotal, 2) }}
                        บาท</strong></p>
            </div>

            <!-- เวลานับถอยหลัง -->
            <div class="flex justify-center mb-8">
                <div
                    class="bg-rose-50 border border-rose-200 text-rose-600 px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>กรุณาชำระภายใน <span id="countdown"
                            class="font-bold tracking-wider text-rose-700">--</span></span>
                </div>
            </div>

            <!-- ข้อความ Error ถ้ามี -->
            @if ($errors->any())
                <div
                    class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl text-sm shadow-sm max-w-md mx-auto">
                    {{ $errors->first() }}
                </div>
            @endif

                        <div id="legacy-payment-instructions" class="hidden bg-blue-50 border border-blue-200 text-blue-800 p-6 rounded-2xl mb-8 max-w-md mx-auto text-center shadow-sm">
                <svg class="w-10 h-10 mx-auto text-blue-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <h3 class="font-bold text-lg mb-2">ขั้นตอนต่อไป</h3>
                <p class="text-sm">
                    กรุณากดยืนยันเพื่อรับ <strong>รหัสตั๋ว (E-Ticket)</strong><br>
                    นำรหัสนี้ไปแสดงที่จุดจำหน่ายตั๋วเพื่อชำระเงิน<br>หรือยืนยันการโอนเงิน (แสดงสลิป) กับเจ้าหน้าที่
                </p>
            </div>

            <div id="promptpay-panel" class="bg-blue-50 border border-blue-200 text-blue-800 p-6 rounded-2xl mb-8 max-w-md mx-auto text-center shadow-sm">
                <h3 class="font-bold text-lg mb-3">สแกนเพื่อชำระเงินผ่าน PromptPay</h3>
                <div class="bg-white p-3 rounded-2xl inline-block shadow-sm">{!! QrCode::size(220)->generate($qrPayload) !!}</div>
                <p class="text-sm mt-3">ราคาบัตร {{ number_format($booking->total_amount, 2) }} + ค่าธรรมเนียม {{ number_format($qrPaymentFee, 2) }} = ยอดชำระ {{ number_format($qrTotal, 2) }} บาท</p>
            </div>

            <div class="flex justify-center max-w-md mx-auto">
                <form method="POST" enctype="multipart/form-data" action="{{ route('bookings.confirm', $booking->qr_ticket_ref) }}" class="w-full space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="payment-option flex items-start gap-3 p-4 rounded-xl border-2 border-cyan-500 bg-cyan-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="qr_code" checked class="mt-1 payment-method">
                            <span><strong class="block text-sm">โอนผ่าน PromptPay</strong><small class="text-xs text-slate-500">แนบสลิปเพื่อยืนยัน</small></span>
                        </label>
                        <label class="payment-option flex items-start gap-3 p-4 rounded-xl border-2 border-slate-200 bg-white cursor-pointer">
                            <input type="radio" name="payment_method" value="counter" class="mt-1 payment-method">
                            <span><strong class="block text-sm">จ่ายที่เคาน์เตอร์</strong><small class="text-xs text-slate-500">นำตั๋วออนไลน์ไปชำระภายหลัง</small></span>
                        </label>
                    </div>
                    @if ($returnToPos)
                        <input type="hidden" name="return_to_pos" value="1">
                    @endif
                    <div id="qr-payment-fields" class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 bg-white cursor-pointer">
                            <input type="checkbox" name="has_slip" value="1" class="h-5 w-5 text-cyan-600" id="has-slip-check" checked>
                            <span class="text-sm font-semibold">ลูกค้ามีสลิปการโอนเงิน (สแกนตรวจสลิปอัตโนมัติ)</span>
                        </label>

                        <div class="border-2 border-dashed border-slate-300 hover:border-cyan-500 rounded-2xl p-4 bg-white transition text-center">
                            <input type="file" name="payment_slip" id="payment-slip-input" accept="image/*"
                                class="hidden">
                            <label for="payment-slip-input" class="cursor-pointer block">
                                <div id="slip-upload-placeholder" class="py-4">
                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-700">คลิกเพื่อเลือกรูปภาพสลิป หรือแตะที่นี่</p>
                                    <p class="text-xs text-slate-400 mt-1">รองรับ JPG, PNG หรือภาพถ่ายจากแอปธนาคาร</p>
                                </div>
                                <div id="slip-preview-container" class="hidden">
                                    <img id="slip-preview-img" src="" alt="สลิปที่เลือก" class="max-h-64 mx-auto rounded-xl shadow-md border border-slate-200 object-contain">
                                    <p class="text-xs text-cyan-600 font-semibold mt-2 hover:underline">คลิกเพื่อเปลี่ยนรูปภาพสลิป</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div id="counter-payment-note" class="hidden p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                        ระบบจะออกตั๋วออนไลน์ให้ แต่สถานะจะเป็น “รอชำระเงิน” ให้นำตั๋วไปชำระที่เคาน์เตอร์
                    </div>
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex items-center justify-center gap-2 text-lg">
                        <span>ยืนยันการชำระเงิน</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
        const paymentMethods = document.querySelectorAll('.payment-method');
        const qrPaymentFields = document.getElementById('qr-payment-fields');
        const counterPaymentNote = document.getElementById('counter-payment-note');
        const promptpayPanel = document.getElementById('promptpay-panel');
        const paymentSlip = document.querySelector('input[name="payment_slip"]');
        const paymentTotal = document.getElementById('payment-total');
        const ticketTotal = {{ json_encode((float) $booking->total_amount) }};
        const qrTotal = {{ json_encode((float) $qrTotal) }};

        function updatePaymentMethod() {
            const isQr = document.querySelector('.payment-method:checked')?.value === 'qr_code';
            qrPaymentFields.classList.toggle('hidden', !isQr);
            counterPaymentNote.classList.toggle('hidden', isQr);
            promptpayPanel.classList.toggle('hidden', !isQr);
            paymentSlip.required = isQr;
            paymentTotal.textContent = (isQr ? qrTotal : ticketTotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const slipInput = document.getElementById('payment-slip-input');
        const slipPlaceholder = document.getElementById('slip-upload-placeholder');
        const slipPreviewContainer = document.getElementById('slip-preview-container');
        const slipPreviewImg = document.getElementById('slip-preview-img');

        if (slipInput) {
            slipInput.addEventListener('change', function(e) {
                const file = e.target.files?.[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        slipPreviewImg.src = evt.target.result;
                        slipPreviewContainer.classList.remove('hidden');
                        slipPlaceholder.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        paymentMethods.forEach((method) => method.addEventListener('change', updatePaymentMethod));
        updatePaymentMethod();

        const expiresAt = new Date("{{ $booking->expires_at->toIso8601String() }}").getTime();
        const el = document.getElementById('countdown');

        const timer = setInterval(() => {
            const diff = Math.max(0, expiresAt - Date.now());
            if (diff <= 0) {
                clearInterval(timer);
                el.textContent = "0 นาที 0 วินาที";
                return;
            }
            const m = Math.floor(diff / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            el.textContent = `${m} นาที ${s} วินาที`;
        }, 1000);
    
</script>
@endpush
