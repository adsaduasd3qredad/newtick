<?php
$file = 'resources/views/bookings/confirmed.blade.php';
$content = file_get_contents($file);

// 1. Replace the <style> block
$oldStyleRegex = '/<style>.*?<\/style>/s';
$newStyle = <<<'CSS'
<style>
        body {
            font-family: 'IBM Plex Sans Thai', sans-serif;
        }

        .font-display {
            font-family: 'Chakra Petch', 'IBM Plex Sans Thai', sans-serif;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: auto !important;
                min-height: 0 !important;
                display: block !important;
            }
            main {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: 0 !important;
            }
            main > div {
                max-width: 100% !important;
                margin: 0 auto !important;
            }
            .no-print {
                display: none !important;
            }
            .ticket-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                max-width: 720px !important;
                margin: 0 auto !important;
                border-radius: 1.25rem !important;
                overflow: hidden !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .ticket-body-grid {
                display: grid !important;
                grid-template-columns: 1.8fr 1fr !important;
                gap: 1.25rem !important;
                padding: 1.25rem 1.5rem !important;
                align-items: center !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .ticket-details-col {
                grid-column: span 1 !important;
            }
            .ticket-qr-col {
                grid-column: span 1 !important;
                padding: 1rem !important;
            }
            .ticket-header-print {
                padding: 1.25rem 1.5rem !important;
            }
        }
    </style>
CSS;

$content = preg_replace($oldStyleRegex, $newStyle, $content);

// 2. Add ticket-header-print class to header div
$content = str_replace(
    'text-white p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-center gap-4',
    'ticket-header-print text-white p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-center gap-4',
    $content
);

// 3. Add classes to grid
$content = str_replace(
    '<div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">',
    '<div class="ticket-body-grid p-6 sm:p-8 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">',
    $content
);

$content = str_replace(
    '<div class="md:col-span-2 space-y-5">',
    '<div class="ticket-details-col md:col-span-2 space-y-5">',
    $content
);

$content = str_replace(
    'class="text-center flex flex-col items-center justify-center p-6',
    'class="ticket-qr-col text-center flex flex-col items-center justify-center p-6',
    $content
);

file_put_contents($file, $content);
echo "SUCCESS: Updated print styles and grid classes!\n";

$verify = file_get_contents($file);
echo (strpos($verify, 'ticket-body-grid') !== false) ? "VERIFIED: ticket-body-grid found\n" : "ERROR: not found\n";
echo (strpos($verify, 'print-color-adjust') !== false) ? "VERIFIED: print-color-adjust found\n" : "ERROR: not found\n";
