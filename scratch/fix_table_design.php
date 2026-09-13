<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

// Replace the entire schedule section wrapper
$searchTable = '/<!-- Minimalist Timetable -->.*<\/table>\s*<\/div>/s';
$replaceTable = <<<HTML
<!-- Minimalist Timetable (Redesigned like Mockup 3) -->
            <div class="overflow-x-auto pb-4 shadow-2xl rounded-lg">
                <table class="w-full text-center border-collapse min-w-[800px] border border-gray-300 bg-white">
                    <thead class="bg-white">
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-4 px-4 font-bold text-gray-900 border border-gray-300 w-48">Date / Day</th>
                            @foreach (\$timeSlots as \$time)
                                <th class="py-4 px-2 font-bold text-gray-900 border border-gray-300">{{ \$time }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @foreach(\$weekDates as \$index => \$date)
                            <tr>
                                <!-- Day Column -->
                                <td class="py-4 px-4 border border-gray-300 bg-[#5482f5] text-white font-medium align-middle">
                                    <div class="font-bold text-lg">{{ \$date->format('l') }}</div>
                                    <div class="text-xs opacity-90">{{ \$date->format('d/m/y') }}</div>
                                </td>
                                
                                @foreach(\$timeSlots as \$time)
                                    @php
                                        \$cellShowtime = \$showtimes->first(function(\$st) use (\$date, \$time) {
                                            return \$st->show_date->toDateString() === \$date->toDateString() && \$st->show_time === \$time.':00';
                                        });
                                    @endphp

                                    @if(\$time === '12:00')
                                        @if(\$index === 0)
                                            <td rowspan="7" class="border border-gray-300 bg-gray-50 text-gray-800 font-bold text-lg align-middle w-24">
                                                <div class="flex items-center justify-center h-full">
                                                    พักเครื่อง
                                                </div>
                                            </td>
                                        @endif
                                    @else
                                        <td class="p-0 border border-gray-300 align-top w-[140px] relative {{ !\$cellShowtime ? 'bg-[#999999]' : 'bg-black' }}">
                                            @if(\$cellShowtime)
                                                <!-- Valid Showtime (Can Book) -->
                                                <a href="{{ route('bookings.create', \$cellShowtime->id) }}" class="block relative w-full h-full min-h-[110px] overflow-hidden group cursor-pointer">
                                                    @if(\$cellShowtime->movie->poster_path)
                                                        <img src="{{ Storage::url(\$cellShowtime->movie->poster_path) }}" alt="{{ \$cellShowtime->movie->title_th }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-70 group-hover:opacity-100">
                                                    @endif
                                                    
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/10"></div>
                                                    
                                                    <div class="absolute bottom-0 left-0 right-0 p-2 text-center flex flex-col justify-end h-full">
                                                        <div class="font-bold text-xs text-white leading-tight drop-shadow-md mb-1" title="{{ \$cellShowtime->movie->title_th }}">
                                                            {{ Str::limit(\$cellShowtime->movie->title_th, 20) }}
                                                        </div>
                                                        <div class="inline-flex mx-auto items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ \$cellShowtime->available_seats > 0 ? 'bg-cyan-500 text-white' : 'bg-red-500 text-white' }}">
                                                            {{ \$cellShowtime->available_seats > 0 ? \$cellShowtime->available_seats . ' Seats' : 'FULL' }}
                                                        </div>
                                                    </div>
                                                </a>
                                            @else
                                                <!-- Empty slot -->
                                                <div class="flex items-center justify-center h-full min-h-[110px] text-gray-200 font-bold text-xl">
                                                    -
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
HTML;
$content = preg_replace($searchTable, $replaceTable, $content);
file_put_contents($file, $content);
echo "Updated table\n";

