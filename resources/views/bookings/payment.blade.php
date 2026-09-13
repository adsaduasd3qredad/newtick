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
                        class="text-emerald-600 font-bold text-xl sm:text-2xl">{{ number_format($booking->total_amount, 2) }}
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

            <!-- กล่องแสดง QR Code -->
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-6 rounded-2xl mb-8 max-w-md mx-auto text-center shadow-sm">
                <svg class="w-10 h-10 mx-auto text-blue-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <h3 class="font-bold text-lg mb-2">ขั้นตอนต่อไป</h3>
                <p class="text-sm">
                    กรุณากดยืนยันเพื่อรับ <strong>รหัสตั๋ว (E-Ticket)</strong><br>
                    นำรหัสนี้ไปแสดงที่จุดจำหน่ายตั๋วเพื่อชำระเงิน<br>หรือยืนยันการโอนเงิน (แสดงสลิป) กับเจ้าหน้าที่
                </p>
            </div>

            <div class="flex justify-center max-w-md mx-auto">
                <form method="POST" action="{{ route('bookings.confirm', $booking->qr_ticket_ref) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="payment_method" value="counter">
                    @if ($returnToPos)
                        <input type="hidden" name="return_to_pos" value="1">
                    @endif
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex items-center justify-center gap-2 text-lg">
                        <span>ยืนยันรับรหัสตั๋ว (E-Ticket)</span>
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
