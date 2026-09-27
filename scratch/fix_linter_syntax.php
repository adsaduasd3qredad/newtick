<?php
// 1. Fix confirmed.blade.php
$fileConfirmed = 'resources/views/bookings/confirmed.blade.php';
$contentConfirmed = file_get_contents($fileConfirmed);

$oldConfirmed = <<<'JS'
            const counterWindowStartsAt = new Date(@json($counterWindowStartsAt->toIso8601String())).getTime();
            const counterWindowEndsAt = new Date(@json($booking->expires_at->toIso8601String())).getTime();
JS;

$newConfirmed = <<<'JS'
            const counterWindowStartsAt = new Date("{{ $counterWindowStartsAt->toIso8601String() }}").getTime();
            const counterWindowEndsAt = new Date("{{ $booking->expires_at->toIso8601String() }}").getTime();
JS;

if (strpos($contentConfirmed, $oldConfirmed) !== false) {
    $contentConfirmed = str_replace($oldConfirmed, $newConfirmed, $contentConfirmed);
    file_put_contents($fileConfirmed, $contentConfirmed);
    echo "SUCCESS: Updated confirmed.blade.php\n";
} else {
    echo "NOTE: Pattern in confirmed.blade.php not matched or already fixed\n";
}

// 2. Fix pos/index.blade.php
$filePos = 'resources/views/pos/index.blade.php';
$contentPos = file_get_contents($filePos);

// Fix style="width: {{ $percentBooked }}%"
$oldWidth = 'style="width: {{ $percentBooked }}%"';
$newWidth = 'style="<?php echo \'width: \' . $percentBooked . \'%\'; ?>"';
$contentPos = str_replace($oldWidth, $newWidth, $contentPos);

// Fix button onclick
$oldBtnPattern = '/<button type="button"\s+onclick="openQuickSellModal\(.*?\)"/s';
$newBtn = <<<'HTML'
<button type="button"
                                    data-id="{{ $showtime->id }}"
                                    data-title="{{ $movieTitle }}"
                                    data-time="{{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}"
                                    data-avail="{{ $availableSeats }}"
                                    onclick="handleQuickSellClick(this)"
HTML;
$contentPos = preg_replace($oldBtnPattern, $newBtn, $contentPos);

// Fix quantity buttons onclick
$oldQtyBtn = 'onclick="setQuantity({{ $q }})"';
$newQtyBtn = 'onclick="setQuantity(Number(this.textContent.trim()))"';
$contentPos = str_replace($oldQtyBtn, $newQtyBtn, $contentPos);

// Fix pricePerSeat in JS
$oldPriceJs = "const pricePerSeat = {{ (int) config('ticketing.price_per_seat', 50) }};";
$newPriceJs = "const pricePerSeat = Number(\"{{ (int) config('ticketing.price_per_seat', 50) }}\");";
$contentPos = str_replace($oldPriceJs, $newPriceJs, $contentPos);

// Add handleQuickSellClick to JS
$oldScriptStart = "function openQuickSellModal(id, title, time, availSeats) {";
$newScriptStart = <<<'JS'
function handleQuickSellClick(btn) {
            openQuickSellModal(
                btn.getAttribute('data-id'),
                btn.getAttribute('data-title'),
                btn.getAttribute('data-time'),
                parseInt(btn.getAttribute('data-avail'), 10)
            );
        }

        function openQuickSellModal(id, title, time, availSeats) {
JS;
if (strpos($contentPos, 'function handleQuickSellClick') === false) {
    $contentPos = str_replace($oldScriptStart, $newScriptStart, $contentPos);
}

file_put_contents($filePos, $contentPos);
echo "SUCCESS: Updated pos/index.blade.php\n";

// Syntax check both files
exec('php -l ' . escapeshellarg($fileConfirmed), $out1, $r1);
echo "confirmed.blade.php: " . implode(" ", $out1) . "\n";

exec('php -l ' . escapeshellarg($filePos), $out2, $r2);
echo "pos/index.blade.php: " . implode(" ", $out2) . "\n";
