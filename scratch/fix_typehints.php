<?php
$files = [
    __DIR__.'/../app/Filament/Resources/Users/UserResource.php',
    __DIR__.'/../app/Filament/Resources/Movies/MovieResource.php',
    __DIR__.'/../app/Filament/Resources/Showtimes/ShowtimeResource.php',
    __DIR__.'/../app/Filament/Resources/WeeklySchedules/WeeklyScheduleResource.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = preg_replace(
            '/protected static \?string \$navigationGroup = (.*?);/',
            'public static function getNavigationGroup(): ?string { return $1; }',
            $content
        );
        file_put_contents($file, $content);
        echo "Fixed $file\n";
    } else {
        echo "Missing $file\n";
    }
}

