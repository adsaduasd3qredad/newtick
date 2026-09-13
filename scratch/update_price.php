<?php
$file = 'app/Http/Controllers/BookingController.php';
$content = file_get_contents($file);
$content = preg_replace("/^\xEF\xBB\xBF/", '', $content);
$content = preg_replace('/protected int \$pricePerSeat = \d+;/', 'protected int $pricePerSeat = 50;', $content);
file_put_contents($file, $content);
echo "Updated pricePerSeat to 50\n";

