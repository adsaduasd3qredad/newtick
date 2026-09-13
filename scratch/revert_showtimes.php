<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

$searchPattern = '/<tbody class="divide-y divide-gray-200">.*?<\/tbody>/s';

$replacePattern = <<<HTML
                    <tbody class="divide-y divide-gray-200">
                        @foreach(\$weekDates as \$date)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 pl-0 pr-4 whitespace-nowrap bg-white sticky left-0 z-10 border-r border-gray-100">
                                    <div class="font-bold text-gray-900">{{ \$date->format('l') }}</div>
                                    <div class="text-xs text-gray-500">{{ \$date->format('d F Y') }}</div>
                                </td>
                                
                                @foreach(\$timeSlots as \$time)
                                    <td class="px-2 py-3 border-x border-gray-100 min-w-[140px] align-top">
                                        @php
                                            \$cellShowtime = \$showtimes->where('show_date', \$date->toDateString())->where('show_time', \$time.':00')->first();
                                        @endphp
                                        
                                        @if(\$cellShowtime)
                                            <!-- Valid Showtime (Can Book) -->
                                            <a href="{{ route('bookings.create', \$cellShowtime->id) }}" class="block px-3 py-2 bg-white border border-gray-200 rounded shadow-sm hover:border-cyan-400 hover:shadow transition group text-center">
                                                <div class="font-semibold text-sm text-gray-800 truncate max-w-[120px] mx-auto" title="{{ \$cellShowtime->movie->title_th }}">{{ Str::limit(\$cellShowtime->movie->title_th, 15) }}</div>
                                                <div class="text-[10px] mt-1 {{ \$cellShowtime->available_seats > 0 ? 'text-green-600 group-hover:text-green-500' : 'text-red-500' }}">
                                                    {{ \$cellShowtime->available_seats > 0 ? \$cellShowtime->available_seats . ' Seats' : 'FULL' }}
                                                </div>
                                            </a>
                                        @else
                                            <div class="flex items-center justify-center h-full min-h-[48px] text-gray-200 text-[10px] font-medium tracking-widest uppercase">
                                                Break
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
HTML;

$content = preg_replace($searchPattern, $replacePattern, $content);
file_put_contents($file, $content);
echo "Reverted showtimes table to use specific dates and \$showtimes\n";

