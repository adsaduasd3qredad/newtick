<?php

$dir = __DIR__.'/resources/views/pos';
if (!is_dir($dir)) mkdir($dir, 0755, true);

// 1. Scan
file_put_contents("$dir/scan.blade.php", <<<HTML
@extends('pos.layout')
@section('title', 'เช็คคิวอาร์ (Scan QR)')
@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-xl font-bold mb-4 text-center">สแกนรหัสการจองออนไลน์</h2>
        
        @if(\$errors->any())
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-4 text-sm font-semibold text-center border border-rose-200">
                {{\$errors->first()}}
            </div>
        @endif

        <form action="{{ route('pos.verify') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">รหัสการจอง (QR Ref)</label>
                <input type="text" name="ref" id="refInput" autofocus required autocomplete="off"
                    class="w-full text-center py-4 px-4 border-2 border-cyan-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-xl text-lg font-bold"
                    placeholder="สแกนหรือพิมพ์รหัสที่นี่...">
            </div>
            <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-xl transition">
                ตรวจสอบข้อมูล
            </button>
        </form>
    </div>
</div>
@endsection
HTML
);

// 2. Verify
file_put_contents("$dir/verify.blade.php", <<<HTML
@extends('pos.layout')
@section('title', 'ตรวจสอบการจอง')
@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">ข้อมูลการจอง</h2>
            <span class="px-3 py-1 rounded-full text-xs font-bold 
                {{ \$booking->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : (\$booking->status === 'redeemed' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') }}">
                {{ strtoupper(\$booking->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <p class="text-slate-500 mb-1">ชื่อผู้จอง</p>
                <p class="font-semibold">{{ \$booking->booker_name }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">จำนวนตั๋ว</p>
                <p class="font-semibold">{{ \$booking->quantity }} ใบ</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">ภาพยนตร์</p>
                <p class="font-semibold">{{ \$booking->showtime->movie->title_th ?? '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 mb-1">รอบฉาย</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse(\$booking->showtime->show_date)->format('d/m/Y') }} {{ \Carbon\Carbon::parse(\$booking->showtime->show_time)->format('H:i') }} น.</p>
            </div>
            <div class="col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <span class="font-semibold text-slate-700">ยอดชำระเงินรวม</span>
                <span class="text-xl font-bold text-emerald-600">{{ number_format(\$booking->total_amount, 2) }} ฿</span>
            </div>
        </div>

        @if(\$booking->status === 'paid')
        <form action="{{ route('pos.confirm-checkin', \$booking->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-xl shadow-md transition text-lg">
                ยืนยันการรับตั๋ว / เช็คอิน (Check-in)
            </button>
        </form>
        @else
        <div class="text-center p-4 bg-slate-100 rounded-xl text-slate-500 text-sm font-semibold">
            ออเดอร์นี้รับตั๋วไปแล้ว หรือยังไม่ชำระเงิน
        </div>
        @endif
        
        <div class="mt-4 text-center">
            <a href="{{ route('pos.scan') }}" class="text-slate-500 hover:text-cyan-600 text-sm font-medium underline">กลับไปหน้าสแกน</a>
        </div>
    </div>
</div>
@endsection
HTML
);

// 3. Orders
file_put_contents("$dir/orders.blade.php", <<<HTML
@extends('pos.layout')
@section('title', 'รายการจอง (Orders)')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h2 class="font-bold text-lg">ประวัติการจองทั้งหมด</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 bg-slate-50 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">ผู้จอง</th>
                        <th class="px-6 py-3">ภาพยนตร์ / รอบฉาย</th>
                        <th class="px-6 py-3">ช่องทางชำระ</th>
                        <th class="px-6 py-3 text-right">ยอดรวม</th>
                        <th class="px-6 py-3 text-center">สถานะ</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(\$bookings as \$b)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-semibold">#{{ \$b->id }}</td>
                        <td class="px-6 py-4">{{ \$b->booker_name }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ \$b->showtime->movie->title_th ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ \$b->showtime->show_date }} {{ \$b->showtime->show_time }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if(\$b->payment_method == 'counter')
                                <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs">เงินสดหน้าเคาน์เตอร์</span>
                            @else
                                <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded text-xs">ออนไลน์ (QR)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-bold">{{ number_format(\$b->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                                {{ \$b->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : (\$b->status === 'redeemed' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ \$b->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('pos.receipt', \$b->id) }}" target="_blank" class="text-cyan-600 hover:underline text-xs">พิมพ์ตั๋ว</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-8 text-slate-500">ไม่มีข้อมูลการจอง</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ \$bookings->links() }}
        </div>
    </div>
</div>
@endsection
HTML
);

// 4. Reports
file_put_contents("$dir/reports.blade.php", <<<HTML
@extends('pos.layout')
@section('title', 'รายงาน (Reports)')
@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">รายงานยอดขายหน้าเคาน์เตอร์</h2>
        <div class="flex gap-2">
            <a href="{{ route('pos.reports.pdf') }}" target="_blank" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold border border-red-200">Export PDF</a>
            <a href="{{ route('pos.reports.csv') }}" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-100 px-4 py-2 rounded-lg text-sm font-semibold border border-emerald-200">Export CSV (Excel)</a>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h3 class="font-bold mb-4 text-slate-700">รายได้แยกตามช่องทาง</h3>
            <ul class="space-y-3">
                @foreach(\$paymentMethods as \$pm)
                <li class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-sm">{{ \$pm->payment_method == 'counter' ? 'เงินสด (Counter)' : 'ออนไลน์ (QR)' }}</span>
                    <span class="font-bold text-emerald-600">{{ number_format(\$pm->total, 2) }} ฿</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-slate-700">รายงานรายวัน (Daily Report - 30 วันล่าสุด)</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 bg-slate-50 uppercase border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3">วันที่</th>
                    <th class="px-6 py-3 text-right">จำนวนตั๋ว</th>
                    <th class="px-6 py-3 text-right">ยอดรวม (฿)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach(\$dailySales as \$ds)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3">{{ \Carbon\Carbon::parse(\$ds->date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 text-right">{{ \$ds->tickets }}</td>
                    <td class="px-6 py-3 text-right font-bold text-emerald-600">{{ number_format(\$ds->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
HTML
);

// 5. PDF Report
file_put_contents("$dir/pdf_report.blade.php", <<<HTML
<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Report</title>
    <style>
        body { font-family: 'freeserif', sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <p>Printed on: {{ now()->format('Y-m-d H:i:s') }}</p>

    <h3>Revenue by Payment Method</h3>
    <table>
        <tr>
            <th>Method</th>
            <th>Tickets</th>
            <th>Total Revenue</th>
        </tr>
        @foreach(\$paymentMethods as \$pm)
        <tr>
            <td>{{ \$pm->payment_method }}</td>
            <td>{{ \$pm->tickets }}</td>
            <td>{{ number_format(\$pm->total, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <h3>Daily Sales (Last 30 Days)</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Tickets</th>
            <th>Total Revenue</th>
        </tr>
        @foreach(\$dailySales as \$ds)
        <tr>
            <td>{{ \$ds->date }}</td>
            <td>{{ \$ds->tickets }}</td>
            <td>{{ number_format(\$ds->total, 2) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
HTML
);
echo "View files created successfully.\n";

