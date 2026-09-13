<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

// Replace "SHOWTIMES SCHEDULE" header and the PREV/NEXT buttons
$searchHeader = '/<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">.*?<\/div>/s';
$replaceHeader = <<<HTML
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-xl font-bold text-gray-900 uppercase tracking-widest">ตารางรอบฉายภาพยนตร์</h2>
        <p class="text-sm text-gray-500 mt-1">ตารางปกติ (อิงข้อมูลจากระบบตั้งค่ารอบฉาย)</p>
    </div>
</div>
HTML;
$content = preg_replace($searchHeader, $replaceHeader, $content);

// Replace the table iteration from weekDates to generic days
$searchTableBody = '/<tbody>.*?<\/tbody>/s';
$replaceTableBody = <<<HTML
                    <tbody class="divide-y divide-gray-200">
                        @php
                            \$days = [
                                7 => ['name' => 'Sunday', 'th' => 'วันอาทิตย์', 'color' => 'text-red-500'],
                                1 => ['name' => 'Monday', 'th' => 'วันจันทร์', 'color' => 'text-yellow-500'],
                                2 => ['name' => 'Tuesday', 'th' => 'วันอังคาร', 'color' => 'text-pink-500'],
                                3 => ['name' => 'Wednesday', 'th' => 'วันพุธ', 'color' => 'text-green-500'],
                                4 => ['name' => 'Thursday', 'th' => 'วันพฤหัสบดี', 'color' => 'text-orange-500'],
                                5 => ['name' => 'Friday', 'th' => 'วันศุกร์', 'color' => 'text-blue-500'],
                                6 => ['name' => 'Saturday', 'th' => 'วันเสาร์', 'color' => 'text-purple-500'],
                            ];
                        @endphp
                        @foreach(\$days as \$isoDay => \$dayInfo)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- Day Column -->
                                <td class="py-4 pl-0 pr-4 whitespace-nowrap bg-white sticky left-0 z-10 border-r border-gray-100">
                                    <div class="font-bold text-gray-900 {{\$dayInfo['color']}}">{{ \$dayInfo['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ \$dayInfo['th'] }}</div>
                                </td>
                                
                                <!-- Time Slots Columns -->
                                @foreach(\$timeSlots as \$time)
                                    <td class="px-2 py-3 border-x border-gray-100 min-w-[140px] align-top">
                                        @php
                                            \$cellWeekly = \$weeklySchedules->where('day_of_week', \$isoDay)->where('show_time', \$time.':00')->first();
                                        @endphp

                                        @if(\$cellWeekly)
                                            <!-- Standard Template Display -->
                                            <div class="block px-3 py-2 bg-white border border-gray-200 rounded shadow-sm hover:border-cyan-400 hover:shadow transition group text-center cursor-default">
                                                <div class="font-semibold text-sm text-gray-800 truncate max-w-[120px] mx-auto" title="{{ \$cellWeekly->movie->title_th }}">{{ Str::limit(\$cellWeekly->movie->title_th, 15) }}</div>
                                                <div class="text-[10px] mt-1 text-gray-400 group-hover:text-cyan-500">
                                                    {{ \$cellWeekly->total_seats }} Seats
                                                </div>
                                            </div>
                                        @else
                                            <!-- Break / Empty -->
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
$content = preg_replace($searchTableBody, $replaceTableBody, $content);

// Remove Legend (since there's no "Available" vs "Template" distinction anymore, just the template)
$searchLegend = '/<div class="mt-6 flex flex-wrap items-center justify-end gap-6 text-xs text-gray-500 uppercase tracking-wide">.*?<\/div>/s';
$content = preg_replace($searchLegend, '', $content);

file_put_contents($file, $content);
echo "Updated showtimes/index.blade.php\n";

