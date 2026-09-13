<?php
$file = 'resources/views/bookings/create.blade.php';
$content = file_get_contents($file);

// Extract the main content
preg_match('/<main[^>]*>(.*?)<\/main>/s', $content, $mainMatches);
$mainContent = $mainMatches[0];

// Extract script
preg_match('/<script>(.*?)<\/script>/s', $content, $scriptMatches);
$scriptContent = $scriptMatches[0] ?? '';

// Replace background color of main if there is any hardcoded classes that we don't want
// Actually, let's keep the mainContent mostly as is, just wrap it.

$newContent = "@extends('layouts.client')\n\n@section('title', 'เลือกประเภทผู้เข้าชม')\n\n@section('content')\n" .
    $mainContent . "\n" .
    "@endsection\n\n" .
    "@push('scripts')\n" .
    $scriptContent . "\n" .
    "@endpush\n";

file_put_contents($file, $newContent);

$file2 = 'resources/views/bookings/seats.blade.php';
$content2 = file_get_contents($file2);
preg_match('/<main[^>]*>(.*?)<\/main>/s', $content2, $mainMatches2);
$mainContent2 = $mainMatches2[0] ?? '';
preg_match('/<script>(.*?)<\/script>/s', $content2, $scriptMatches2);
$scriptContent2 = $scriptMatches2[0] ?? '';
preg_match('/<style>(.*?)<\/style>/s', $content2, $styleMatches2);
$styleContent2 = $styleMatches2[0] ?? '';

if ($mainContent2) {
    $newContent2 = "@extends('layouts.client')\n\n@section('title', 'เลือกที่นั่ง')\n\n@push('styles')\n$styleContent2\n@endpush\n\n@section('content')\n" .
        $mainContent2 . "\n" .
        "@endsection\n\n" .
        "@push('scripts')\n" .
        $scriptContent2 . "\n" .
        "@endpush\n";
    file_put_contents($file2, $newContent2);
}

$file3 = 'resources/views/bookings/payment.blade.php';
$content3 = file_get_contents($file3);
preg_match('/<main[^>]*>(.*?)<\/main>/s', $content3, $mainMatches3);
$mainContent3 = $mainMatches3[0] ?? '';
preg_match('/<script>(.*?)<\/script>/s', $content3, $scriptMatches3);
$scriptContent3 = $scriptMatches3[0] ?? '';

if ($mainContent3) {
    $newContent3 = "@extends('layouts.client')\n\n@section('title', 'ชำระเงิน')\n\n@section('content')\n" .
        $mainContent3 . "\n" .
        "@endsection\n\n" .
        "@push('scripts')\n" .
        $scriptContent3 . "\n" .
        "@endpush\n";
    file_put_contents($file3, $newContent3);
}

echo "Views updated to use layouts.client";

