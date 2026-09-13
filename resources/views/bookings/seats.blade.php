@extends('layouts.client')

@section('title', 'เลือกที่นั่ง')

@push('styles')
<style>

        :root {
            --seat-size: 26px;
            --seat-gap: 3px;
            --seat-blue-top: #38bdf8;
            --seat-blue-bottom: #0ea5e9;
        }

        .pc-container {
            position: relative;
            width: 820px;
            height: 480px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.02);
        }

        .screen {
            position: absolute;
            top: 12px;
            left: 55px;
            width: 710px;
            height: 6px;
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            clip-path: polygon(0 0, 100% 0, 99.5% 100%, 0.5% 100%);
        }

        .row-label {
            position: absolute;
            top: var(--top);
            z-index: 2;
            width: 20px;
            color: #64748b;
            font: bold 11px/14px Arial, sans-serif;
            text-align: center;
            user-select: none;
        }

        .row-label.left {
            left: 20px;
        }

        .row-label.right {
            right: 20px;
        }

        .seat-group {
            position: absolute;
            top: var(--top);
            left: var(--left);
            display: flex;
            gap: var(--seat-gap);
            height: var(--seat-size);
        }

        .seat {
            display: grid;
            flex: 0 0 var(--seat-size);
            width: var(--seat-size);
            height: var(--seat-size);
            padding: 2px 3px 0 0;
            place-items: start end;
            overflow: hidden;
            border: 1px solid #7dd3fc;
            border-radius: 3px;
            background: linear-gradient(to bottom, var(--seat-blue-top), var(--seat-blue-bottom));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 20%);
            color: #fff;
            font: bold 11px/12px Arial, sans-serif;
            text-align: center;
            text-shadow: 0 1px 1px rgb(0 0 0 / 15%);
            cursor: pointer;
            user-select: none;
            transition: transform 0.1s ease, filter 0.1s ease;
        }

        .seat:hover:not(.unavailable):not(.selected) {
            transform: scale(1.12);
            filter: brightness(1.05);
            border-color: #0284c7;
        }

        /* สถานะเลือกแล้ว (สีทองพรีเมียม) */
        .seat.selected {
            border-color: #d97706;
            background: linear-gradient(to bottom, #fbbf24, #f59e0b);
            text-shadow: 0 1px 1px rgb(146 64 14 / 30%);
        }

        /* สถานะไม่ว่าง */
        .seat.unavailable {
            border-color: #cbd5e1;
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            color: #475569;
            cursor: not-allowed;
            text-shadow: none;
        }
    
</style>
@endpush

@section('content')
<div class="flex-1 flex flex-col items-center p-4 my-4 w-full">

        <div class="max-w-5xl w-full bg-white p-4 sm:p-12 rounded-3xl shadow-sm border border-slate-200/80">

            <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-slate-900 tracking-tight">
                เลือกที่นั่งรับชมภาพยนตร์</h1>

            <!-- Step indicator -->
            <div class="flex items-center justify-center gap-2 sm:gap-6 mb-8 overflow-x-auto py-2">
                @php
                    $steps = [
                        ['n' => 1, 'label' => 'กรอกข้อมูล'],
                        ['n' => 2, 'label' => 'เลือกที่นั่ง'],
                        ['n' => 3, 'label' => 'ชำระเงิน'],
                        ['n' => 4, 'label' => 'เสร็จสมบูรณ์'],
                    ];
                    $currentStep = 2;
                @endphp
                @foreach ($steps as $step)
                    <div class="flex items-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold shadow-sm
                                    {{ $step['n'] <= $currentStep ? 'bg-cyan-600 text-white' : 'bg-slate-200 text-slate-400' }}">
                                {{ $step['n'] }}
                            </div>
                            <span
                                class="text-[10px] sm:text-[11px] mt-1.5 text-center max-w-[70px] {{ $step['n'] <= $currentStep ? 'text-cyan-700 font-semibold' : 'text-slate-400' }}">
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

            <!-- Showtime summary card -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
                <div class="bg-slate-100/80 px-6 py-3.5 border-b border-slate-200/60">
                    <p class="font-bold text-slate-800 text-base">{{ $showtime->movie->title_th ?? '-' }}</p>
                </div>
                <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs mb-1">วันที่รับชม</p>
                        <p class="font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($showtime->show_date)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs mb-1">รอบเวลา</p>
                        <p class="font-semibold text-slate-700">
                            {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }} น.</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs mb-1">ที่นั่งที่ระบุ</p>
                        <p class="font-semibold text-cyan-600 truncate max-w-[150px]" id="selectedSeatsLabel">
                            ยังไม่ได้เลือก</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs mb-1">ยอดรวมสุทธิ</p>
                        <p class="font-bold text-emerald-600" id="totalPriceLabel">0 บาท</p>
                    </div>
                </div>
            </div>

            <p class="text-center text-sm text-slate-600 mb-6 font-medium">
                กรุณาเลือกที่นั่งจำนวน <span class="text-cyan-600 font-bold text-base">{{ $quantity }}</span>
                ที่นั่ง
                (เลือกแล้ว <span id="selectedCount" class="font-bold text-cyan-600">0</span> / {{ $quantity }})
            </p>

            <!-- Seat map container รองรับมือถือ (เลื่อนซ้าย-ขวาได้เมื่อหน้าจอเล็ก) -->
            <div class="w-full overflow-x-auto pb-4 mb-6">
                <main class="pc-container" aria-label="ผังที่นั่ง">
                    <div class="screen" aria-hidden="true"></div>

                    <div class="row-label left" style="--top: 36px">A</div>
                    <div class="row-label right" style="--top: 36px">A</div>
                    <div class="row-label left" style="--top: 68px">B</div>
                    <div class="row-label right" style="--top: 68px">B</div>
                    <div class="row-label left" style="--top: 100px">C</div>
                    <div class="row-label right" style="--top: 100px">C</div>
                    <div class="row-label left" style="--top: 132px">D</div>
                    <div class="row-label right" style="--top: 132px">D</div>
                    <div class="row-label left" style="--top: 164px">E</div>
                    <div class="row-label right" style="--top: 164px">E</div>
                    <div class="row-label left" style="--top: 222px">F</div>
                    <div class="row-label right" style="--top: 222px">F</div>
                    <div class="row-label left" style="--top: 254px">G</div>
                    <div class="row-label right" style="--top: 254px">G</div>
                    <div class="row-label left" style="--top: 286px">H</div>
                    <div class="row-label right" style="--top: 286px">H</div>
                    <div class="row-label left" style="--top: 318px">I</div>
                    <div class="row-label right" style="--top: 318px">I</div>

                    <div id="seat-groups"></div>

                    <!-- Legend คำอธิบายสถานะ -->
                    <div
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-4 sm:gap-6 text-xs text-slate-600 bg-white/95 px-4 sm:px-5 py-2 rounded-full border border-slate-200 shadow-sm whitespace-nowrap">
                        <div class="flex items-center gap-1.5">
                            <span
                                class="w-3.5 h-3.5 rounded bg-sky-500 inline-block border border-sky-400 shadow-sm"></span>
                            ที่นั่งว่าง
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span
                                class="w-3.5 h-3.5 rounded bg-amber-400 inline-block border border-amber-300 shadow-sm"></span>
                            เลือกแล้ว
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span
                                class="w-3.5 h-3.5 rounded bg-slate-400 inline-block border border-slate-300 shadow-sm"></span>
                            ไม่ว่าง
                        </div>
                    </div>
                
</div>

<!-- Sticky bottom bar -->

    <form action="{{ route('bookings.store') }}" method="POST"
        class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg z-50">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
        <input type="hidden" name="booker_name" value="{{ $booker_name }}">
        <input type="hidden" name="booker_email" value="{{ $booker_email }}">
        <input type="hidden" name="booker_phone" value="{{ $booker_phone }}">
        <input type="hidden" name="visitor_type" value="{{ $visitor_type }}">
        <input type="hidden" name="quantity" value="{{ $quantity }}">
        @if ($returnToPos)
            <input type="hidden" name="return_to_pos" value="1">
        @endif
        <div id="seatInputs"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-4">
            <div>
                <p class="text-slate-400 text-[11px] sm:text-xs font-medium">ที่นั่งที่เลือก</p>
                <p id="selectedSeatsBottom"
                    class="font-bold text-slate-800 text-xs sm:text-sm max-w-[180px] sm:max-w-md truncate">-</p>
            </div>
            <button type="submit" id="confirmBtn" disabled
                class="bg-gradient-to-r from-cyan-500 to-blue-600 disabled:from-slate-200 disabled:to-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-2.5 sm:py-3 px-5 sm:px-8 rounded-xl shadow-md transition duration-200 text-xs sm:text-sm shrink-0">
                ยืนยันที่นั่ง (ชำระเงิน)
            </button>
        </div>
    </form>

    
@endsection

@push('scripts')
<script>

        document.addEventListener('DOMContentLoaded', function() {
            const seatGroups = [{
                    row: 'A',
                    top: 34,
                    left: 102,
                    from: 1,
                    to: 10
                },
                {
                    row: 'A',
                    top: 34,
                    left: 440,
                    from: 11,
                    to: 17
                },
                {
                    row: 'B',
                    top: 66,
                    left: 70,
                    from: 1,
                    to: 11
                },
                {
                    row: 'B',
                    top: 66,
                    left: 443,
                    from: 12,
                    to: 19
                },
                {
                    row: 'C',
                    top: 98,
                    left: 66,
                    from: 1,
                    to: 11
                },
                {
                    row: 'C',
                    top: 98,
                    left: 446,
                    from: 12,
                    to: 19
                },
                {
                    row: 'D',
                    top: 130,
                    left: 33,
                    from: 1,
                    to: 12
                },
                {
                    row: 'D',
                    top: 130,
                    left: 449,
                    from: 13,
                    to: 20
                },
                {
                    row: 'E',
                    top: 162,
                    left: 61,
                    from: 1,
                    to: 11
                },
                {
                    row: 'E',
                    top: 162,
                    left: 452,
                    from: 12,
                    to: 20
                },
                {
                    row: 'F',
                    top: 220,
                    left: 33,
                    from: 1,
                    to: 5
                },
                {
                    row: 'F',
                    top: 220,
                    left: 216,
                    from: 6,
                    to: 16
                },
                {
                    row: 'F',
                    top: 220,
                    left: 566,
                    from: 17,
                    to: 21
                },
                {
                    row: 'G',
                    top: 252,
                    left: 203,
                    from: 1,
                    to: 12
                },
                {
                    row: 'G',
                    top: 252,
                    left: 566,
                    from: 13,
                    to: 17
                },
                {
                    row: 'H',
                    top: 284,
                    left: 190,
                    from: 1,
                    to: 13
                },
                {
                    row: 'H',
                    top: 284,
                    left: 581,
                    from: 14,
                    to: 15
                },
                {
                    row: 'I',
                    top: 316,
                    left: 178,
                    from: 1,
                    to: 12
                }
            ];

            const bookedSeats = @json($bookedSeats);
            const requiredSeats = {{ $quantity }};
            const pricePerSeat = {{ $pricePerSeat }};
            const selected = new Set();

            const groupsContainer = document.querySelector('#seat-groups');
            const selectedCountEl = document.getElementById('selectedCount');
            const selectedSeatsLabel = document.getElementById('selectedSeatsLabel');
            const selectedSeatsBottom = document.getElementById('selectedSeatsBottom');
            const totalPriceLabel = document.getElementById('totalPriceLabel');
            const confirmBtn = document.getElementById('confirmBtn');
            const seatInputs = document.getElementById('seatInputs');

            function render() {
                const list = Array.from(selected).sort();
                selectedCountEl.textContent = selected.size;
                selectedSeatsLabel.textContent = list.length ? list.join(', ') : 'ยังไม่ได้เลือก';
                selectedSeatsBottom.textContent = list.length ? list.join(', ') : '-';
                totalPriceLabel.textContent = (selected.size * pricePerSeat).toLocaleString('th-TH') + ' บาท';
                confirmBtn.disabled = selected.size !== requiredSeats;

                seatInputs.innerHTML = '';
                list.forEach(function(seat) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'seats[]';
                    input.value = seat;
                    seatInputs.appendChild(input);
                });
            }

            seatGroups.forEach((group) => {
                const groupElement = document.createElement('div');
                groupElement.className = 'seat-group';
                groupElement.style.setProperty('--top', `${group.top}px`);
                groupElement.style.setProperty('--left', `${group.left}px`);

                for (let number = group.from; number <= group.to; number += 1) {
                    const seatCode = group.row + number;
                    const isBooked = bookedSeats.includes(seatCode);

                    const seat = document.createElement('span');
                    seat.className = `seat ${isBooked ? 'unavailable' : ''}`;
                    seat.textContent = number;
                    seat.dataset.seat = seatCode;

                    if (!isBooked) {
                        seat.addEventListener('click', function() {
                            if (selected.has(seatCode)) {
                                selected.delete(seatCode);
                                seat.classList.remove('selected');
                            } else {
                                if (selected.size >= requiredSeats) {
                                    return; // เลือกครบจำนวนแล้ว
                                }
                                selected.add(seatCode);
                                seat.classList.add('selected');
                            }
                            render();
                        });
                    }

                    groupElement.appendChild(seat);
                }

                groupsContainer.appendChild(groupElement);
            });

            render();
        });
    
</script>
@endpush
