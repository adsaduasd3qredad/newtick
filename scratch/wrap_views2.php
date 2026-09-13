<?php
function wrapView($file, $title) {
    if (!file_exists($file)) return;
    $content = file_get_contents($file);

    // Extract main
    if (preg_match('/<main[^>]*>(.*?)<\/main>/s', $content, $mainMatches)) {
        $mainContent = $mainMatches[1];
        // Strip inner class="flex-1 ..." from main if any, we just want the inner HTML.
        // Actually, just keep <main> as a div or just keep its inner content.
        $mainContent = '<div class="flex-1 flex items-center justify-center p-6 my-6">' . $mainContent . '</div>';
    } else {
        return;
    }

    // Extract script
    preg_match('/<script>(.*?)<\/script>/s', $content, $scriptMatches);
    $scriptContent = $scriptMatches[1] ?? '';

    // Extract style
    preg_match('/<style>(.*?)<\/style>/s', $content, $styleMatches);
    $styleContent = $styleMatches[1] ?? '';

    $newContent = "@extends('layouts.client')\n\n@section('title', '$title')\n\n";
    if ($styleContent) {
        $newContent .= "@push('styles')\n<style>\n$styleContent\n</style>\n@endpush\n\n";
    }
    $newContent .= "@section('content')\n" . $mainContent . "\n@endsection\n\n";
    if ($scriptContent) {
        $newContent .= "@push('scripts')\n<script>\n$scriptContent\n</script>\n@endpush\n";
    }

    file_put_contents($file, $newContent);
    echo "Wrapped $file\n";
}

wrapView('resources/views/bookings/create.blade.php', 'กรอกข้อมูลการจองตั๋ว');
wrapView('resources/views/bookings/seats.blade.php', 'เลือกที่นั่ง');
wrapView('resources/views/bookings/payment.blade.php', 'ชำระเงิน');

