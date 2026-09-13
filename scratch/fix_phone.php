<?php
$file = 'app/Http/Controllers/BookingController.php';
$content = file_get_contents($file);
$content = preg_replace("/^\xEF\xBB\xBF/", '', $content);
$content = str_replace("'booker_phone' => 'nullable|string|max:20'", "'booker_phone' => 'required|string|regex:/^[0-9]{10}$/'", $content);
file_put_contents($file, $content);
echo "Fixed validation in BookingController.php\n";

