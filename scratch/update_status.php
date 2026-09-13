<?php
$file = 'app/Http/Controllers/BookingController.php';
$content = file_get_contents($file);
$content = preg_replace("/^\xEF\xBB\xBF/", '', $content);

$content = str_replace(
    "'status' => 'paid',",
    "'status' => 'awaiting_payment',",
    $content
);

file_put_contents($file, $content);
echo "Updated status to awaiting_payment\n";

