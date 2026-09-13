<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

// 1. Remove the "Template (Not Open)" text at the bottom if it still exists.
$searchLegend = '/<div class="flex items-center gap-2"><span class="w-3 h-3 bg-gray-50 border border-dashed border-gray-200 block"><\/span> Template \(Not Open\)<\/div>/s';
$content = preg_replace($searchLegend, '', $content);

// 2. Replace the Showtime Display block
$searchShowtime = '/<!-- Valid Showtime \(Can Book\) -->.*?<\/a>/s';
$replaceShowtime = <<<HTML
<!-- Valid Showtime (Can Book) -->
                                            <a href="{{ route('bookings.create', \$cellShowtime->id) }}" class="block relative w-full h-24 sm:h-32 rounded-lg overflow-hidden shadow-sm hover:shadow-lg hover:ring-2 hover:ring-cyan-500 transition-all duration-300 group cursor-pointer border border-gray-100">
                                                @if(\$cellShowtime->movie->poster_path)
                                                    <img src="{{ Storage::url(\$cellShowtime->movie->poster_path) }}" alt="{{ \$cellShowtime->movie->title_th }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                @else
                                                    <div class="absolute inset-0 bg-gray-800 w-full h-full"></div>
                                                @endif
                                                
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/10 group-hover:from-black/80 transition-colors"></div>
                                                
                                                <div class="absolute bottom-0 left-0 right-0 p-2 sm:p-3 text-center flex flex-col justify-end h-full">
                                                    <div class="font-bold text-[11px] sm:text-xs text-white leading-tight drop-shadow-md mb-1" title="{{ \$cellShowtime->movie->title_th }}">
                                                        {{ Str::limit(\$cellShowtime->movie->title_th, 20) }}
                                                    </div>
                                                    <div class="inline-flex mx-auto items-center justify-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-semibold {{ \$cellShowtime->available_seats > 0 ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }} backdrop-blur-sm">
                                                        {{ \$cellShowtime->available_seats > 0 ? \$cellShowtime->available_seats . ' Seats' : 'FULL' }}
                                                    </div>
                                                </div>
                                            </a>
HTML;
$content = preg_replace($searchShowtime, $replaceShowtime, $content);

// 3. Replace the Break block
$searchBreak = '/<div class="flex items-center justify-center h-full min-h-\[48px\] text-gray-200 text-\[10px\] font-medium tracking-widest uppercase">\s*Break\s*<\/div>/s';
$replaceBreak = <<<HTML
<div class="h-24 sm:h-32 flex items-center justify-center">
                                                <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                            </div>
HTML;
$content = preg_replace($searchBreak, $replaceBreak, $content);

// Ensure the legend container is removed entirely if empty
$searchLegendContainer = '/<div class="mt-6 flex flex-wrap items-center justify-end gap-6 text-xs text-gray-500 uppercase tracking-wide">\s*<\/div>/s';
$content = preg_replace($searchLegendContainer, '', $content);

file_put_contents($file, $content);
echo "Updated showtimes/index.blade.php with images\n";

