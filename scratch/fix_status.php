<?php
$files = [
    'app/Http/Controllers/ShowtimeController.php',
    'app/Filament/Resources/Showtimes/Pages/ListShowtimes.php',
    'app/Filament/Widgets/StatsOverview.php',
    'app/Filament/Widgets/TopMoviesChart.php',
    'app/Filament/Pages/SaleReport.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Remove BOM
        $content = preg_replace("/^\xEF\xBB\xBF/", '', $content);
        // Replace status
        $content = str_replace("'status', 'publish'", "'is_active', true", $content);
        $content = str_replace("\$movie->status !== 'publish'", "!\$movie->is_active", $content);
        file_put_contents($file, $content);
        echo "Fixed $file\n";
    }
}

