<?php
$file = 'resources/views/bookings/seats.blade.php';
$content = file_get_contents($file);

// Extract main content
preg_match('/<main[^>]*>(.*?)<\/main>/s', $content, $mainMatches);
$mainContent = $mainMatches[1] ?? '';

// Extract the sticky bottom form block
preg_match('/<!-- Sticky bottom bar -->(.*?)<!-- Footer -->/s', $content, $formMatches);
$formContent = $formMatches[1] ?? '';

// Extract script
preg_match('/<script>(.*?)<\/script>/s', $content, $scriptMatches);
$scriptContent = $scriptMatches[1] ?? '';

// Extract style
preg_match('/<style>(.*?)<\/style>/s', $content, $styleMatches);
$styleContent = $styleMatches[1] ?? '';

if ($mainContent && $formContent) {
    // Replace $requiredSeats with $quantity in HTML text and in JS
    // We already passed $quantity instead of $requiredSeats in BookingController!
    // But wait, the original file uses $requiredSeats!
    $mainContent = str_replace('$requiredSeats', '$quantity', $mainContent);
    $formContent = str_replace('$requiredSeats', '$quantity', $formContent);
    $scriptContent = str_replace('$requiredSeats', '$quantity', $scriptContent);

    // Reconstruct the file
    $newContent = "@extends('layouts.client')\n\n@section('title', 'เลือกที่นั่ง')\n\n";
    if ($styleContent) {
        $newContent .= "@push('styles')\n<style>\n$styleContent\n</style>\n@endpush\n\n";
    }
    
    // Put them in a div for layout
    $newContent .= "@section('content')\n";
    $newContent .= '<div class="flex-1 flex flex-col items-center p-4 my-4 w-full">';
    $newContent .= "\n" . $mainContent . "\n";
    $newContent .= "</div>\n";
    
    // Put the form/sticky bar at the bottom
    $newContent .= "\n<!-- Sticky bottom bar -->\n" . $formContent . "\n";
    
    $newContent .= "@endsection\n\n";
    
    if ($scriptContent) {
        $newContent .= "@push('scripts')\n<script>\n$scriptContent\n</script>\n@endpush\n";
    }

    file_put_contents($file, $newContent);
    echo "Wrapped seats.blade.php correctly!\n";
} else {
    echo "Failed to extract parts.\n";
}

