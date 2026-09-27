<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน #{{ $booking->id }} - ท้องฟ้าจำลองรังสิต</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }

        .font-display {
            font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                width: 76mm !important;
                margin: 0 auto !important;
                padding: 4mm !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen py-8 flex flex-col items-center justify-start">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md transition text-sm font-display">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>พิมพ์ใบเสร็จรับเงิน (Print)</span>
        </button>

        <a href="{{ route('pos.index') }}"
            class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold py-2.5 px-5 rounded-xl border border-slate-300 shadow-sm transition text-sm">
            <span>← กลับหน้า POS</span>
        </a>
    </div>

    <!-- Thermal Receipt Slip (80mm Width) -->
    <div class="receipt-container w-[340px] bg-white p-6 rounded-2xl border border-slate-300 shadow-xl text-slate-900 text-xs">
        
        <!-- Header -->
        <div class="text-center pb-3 border-b border-dashed border-slate-400">
            <img src="{{ asset('images/logo.jpg') }}" alt="โลโก้" class="h-10 w-auto object-contain mx-auto mb-1">
            <h1 class="font-display font-bold text-sm tracking-wide">ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต</h1>
            <p class="text-[11px] text-slate-600">ท้องฟ้าจำลองรังสิต (Planetarium)</p>
            <p class="font-display font-bold text-xs mt-1.5 text-slate-800 uppercase tracking-widest">
                ** ใบเสร็จรับเงิน (RECEIPT) **
            </p>
        </div>

        <!-- Meta info -->
        <div class="py-2.5 border-b border-dashed border-slate-400 space-y-1 font-mono text-[11px]">
            <div class="flex justify-between">
                <span>เลขที่ใบเสร็จ:</span>
                <span class="font-bold text-slate-900">#{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>วันที่ออกใบเสร็จ:</span>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>จุดขาย:</span>
                <span>เคาน์เตอร์ POS</span>
            </div>
            <div class="flex justify-between">
                <span>ผู้ซื้อ:</span>
                <span class="font-sans font-medium truncate max-w-[170px] text-right">{{ $booking->booker_name }}</span>
            </div>
        </div>

        <!-- Movie & Showtime Info -->
        <div class="py-3 border-b border-dashed border-slate-400 space-y-1.5">
            <p class="text-[10px] text-slate-500 font-semibold uppercase">เรื่องที่เข้าชม:</p>
            <p class="font-display font-bold text-sm leading-snug">
                {{ $booking->showtime->movie->title_th ?? '-' }}
            </p>
            @if ($booking->showtime->movie && $booking->showtime->movie->title_en)
                <p class="text-[10px] text-slate-500">{{ $booking->showtime->movie->title_en }}</p>
            @endif

            <div class="grid grid-cols-2 gap-2 pt-1 font-display">
                <div class="bg-slate-50 p-1.5 rounded border border-slate-200 text-center">
                    <span class="text-[10px] text-slate-500 block">วันที่รอบฉาย</span>
                    <strong class="text-xs">{{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</strong>
                </div>
                <div class="bg-slate-50 p-1.5 rounded border border-slate-200 text-center">
                    <span class="text-[10px] text-slate-500 block">รอบเวลา</span>
                    <strong class="text-xs text-cyan-800">{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }} น.</strong>
                </div>
            </div>
        </div>

        <!-- Seats list -->
        <div class="py-2.5 border-b border-dashed border-slate-400">
            <div class="flex justify-between items-center mb-1">
                <span class="font-bold text-[11px]">หมายเลขที่นั่ง:</span>
                <span class="font-bold text-xs">{{ $booking->quantity }} ที่นั่ง</span>
            </div>
            <div class="flex flex-wrap gap-1 font-mono font-bold text-xs">
                @if (!empty($booking->seats))
                    @foreach ($booking->seats as $seat)
                        <span class="px-2 py-0.5 bg-slate-100 border border-slate-300 rounded text-slate-900">
                            {{ $seat }}
                        </span>
                    @endforeach
                @else
                    <span>จำนวน {{ $booking->quantity }} ที่นั่ง</span>
                @endif
            </div>
        </div>

        <!-- Financial Breakdown -->
        <div class="py-2.5 border-b border-dashed border-slate-400 space-y-1 text-xs">
            @php($unitPrice = $booking->quantity > 0 ? ($booking->total_amount / $booking->quantity) : (int) config('ticketing.price_per_seat', 50))
            <div class="flex justify-between">
                <span>ราคาตั๋ว ({{ number_format($unitPrice, 0) }} ฿ x {{ $booking->quantity }}):</span>
                <span>฿ {{ number_format($booking->total_amount, 2) }}</span>
            </div>
            @if ($booking->payment?->transaction_fee)
                <div class="flex justify-between">
                    <span>ค่าธรรมเนียม:</span>
                    <span>฿ {{ number_format($booking->payment->transaction_fee, 2) }}</span>
                </div>
            @endif
            @if ($booking->total_amount > (($booking->amount_paid ?? 0) - ($booking->payment?->transaction_fee ?? 0)))
                @php($discount = max(0, $booking->total_amount - (($booking->amount_paid ?? 0) - ($booking->payment?->transaction_fee ?? 0))))
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>ส่วนลดโปรโมชั่น (เข้าชมฟรี):</span>
                    <span>- ฿ {{ number_format($discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-sm pt-1 border-t border-slate-200">
                <span>ยอดชำระสุทธิ:</span>
                <span class="font-display">฿ {{ number_format($booking->amount_paid ?? $booking->total_amount, 2) }}</span>
            </div>
            @if ($booking->notes)
                <div class="pt-1.5 text-[10px] text-slate-700 bg-amber-50 p-1.5 rounded border border-amber-200 font-medium">
                    <strong>หมายเหตุ:</strong> {{ $booking->notes }}
                </div>
            @endif
            <div class="flex justify-between text-[10px] text-slate-500 pt-0.5">
                <span>วิธีชำระเงิน:</span>
                <span class="font-bold text-emerald-700">{{ $booking->payment_method === 'qr_code' ? 'PromptPay QR' : 'เงินสด (Cash)' }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-3 text-[10px] text-slate-500 space-y-0.5">
            <p>กรุณาเก็บใบเสร็จนี้ไว้เป็นหลักฐาน</p>
            <p>กรุณาเข้าห้องฉายก่อนเวลาเริ่ม 15 นาที</p>
            <p>งดนำอาหารและเครื่องดื่มเข้าห้องฉาย</p>
            <p class="font-medium text-slate-700 pt-1.5">ขอบคุณที่ใช้บริการ</p>
        </div>

    </div>

</body>

</html>