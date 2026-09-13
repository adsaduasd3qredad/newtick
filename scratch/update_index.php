<?php
$file = __DIR__.'/../resources/views/pos/index.blade.php';
$content = file_get_contents($file);

$start = strpos($content, '    <!-- Main Container -->');
$end = strpos($content, '    <!-- Footer -->');

if ($start !== false && $end !== false) {
    $mainContent = substr($content, $start, $end - $start);
    $newContent = "@extends('pos.layout')\n@section('title', 'รอบฉาย')\n@section('content')\n" . $mainContent . "\n@endsection\n";
    file_put_contents($file, $newContent);
    echo "updated.\n";
} else {
    echo "failed.\n";
}

