@extends('pos.layout')
@section('title', 'ตรวจสอบการจอง')
@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">ข้อมูลการจอง</h2>
            <span class="px-3 py-1 rounded-full text-xs font-bold 
                {{ $booking->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($booking->status === 'redeemed' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') }}">
                {{ strtoupper($booking->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <p class="text-slate-500 mb-1">ชื่อผู้จอง</p>
                <p class="font-semibold">{{ $booking->booker_name }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">จำนวนตั๋ว</p>
                <p class="font-semibold">{{ $booking->quantity }} ใบ</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">ภาพยนตร์</p>
                <p class="font-semibold">{{ $booking->showtime->movie->title_th ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">รอบฉาย</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }} น.</p>
            </div>
            <div class="col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <span class="font-semibold text-slate-700">ยอดชำระเงินรวม</span>
                <span class="text-xl font-bold text-emerald-600">{{ number_format($booking->total_amount, 2) }} ฿</span>
            </div>
        </div>

                @if($booking->status === 'paid')
        <form action="{{ route('pos.confirm-checkin', $booking->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-xl shadow-md transition text-lg flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                รับตั๋ว / เช็คอิน (Check-in)
            </button>
        </form>
        @elseif(in_array($booking->status, ['pending', 'awaiting_payment']))
        <div class="flex flex-col gap-4">
            <!-- Cash Payment -->
            <form action="{{ route('pos.confirm-checkin', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="payment_method" value="cash">
                <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-4 rounded-xl shadow-md transition text-lg flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    รับเงินสด & ออกตั๋ว
                </button>
            </form>

            <!-- QR Payment Modal Trigger -->
            <button type="button" onclick="document.getElementById('qr-modal').classList.remove('hidden')" class="w-full bg-white border-2 border-cyan-600 text-cyan-600 hover:bg-cyan-50 font-bold py-3.5 rounded-xl shadow-sm transition text-lg flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                แสดง QR Code โอนเงิน
            </button>
        </div>

        <!-- QR Modal -->
        <div id="qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center relative shadow-2xl">
                <button type="button" onclick="document.getElementById('qr-modal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-full p-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="text-xl font-bold text-slate-800 mb-2 mt-2">สแกนจ่ายเงิน</h3>
                <p class="text-slate-500 text-sm mb-6">ยอดชำระ <strong class="text-emerald-600 text-lg">{{ number_format($booking->total_amount, 2) }} ฿</strong></p>
                
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 flex justify-center">
                    {!! \QrCode::size(220)->generate($qrPayload ?? '') !!}
                </div>

                <form action="{{ route('pos.confirm-checkin', $booking->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="qr_code">
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-md transition text-lg flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        ยืนยันรับเงิน & ออกตั๋ว
                    </button>
                </form>
            </div>
        </div>

        @elseif($booking->status === 'redeemed')
        <div class="text-center p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-600 text-sm font-bold flex flex-col items-center justify-center gap-2">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            ออเดอร์นี้รับตั๋วไปแล้ว
        </div>
        @else
        <div class="text-center p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-600 text-sm font-bold flex flex-col items-center justify-center gap-2">
            <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            สถานะออเดอร์ไม่พร้อมสำหรับการออกตั๋ว ({{$booking->status}})
        </div>
        @endif
        
        <div class="mt-4 text-center">
            <a href="{{ route('pos.scan') }}" class="text-slate-500 hover:text-cyan-600 text-sm font-medium underline">กลับไปหน้าสแกน</a>
        </div>
    </div>
</div>
@endsection