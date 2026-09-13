<?php
$file = 'resources/views/showtimes/index.blade.php';
$content = file_get_contents($file);

$searchPattern = '/\$cellShowtime = \$showtimes->where\(\'show_date\', \$date->toDateString\(\)\)->where\(\'show_time\', \$time\.\':00\'\)->first\(\);/';

$replacePattern = <<<HTML
\$cellShowtime = \$showtimes->first(function(\$st) use (\$date, \$time) {
                                                return \$st->show_date->toDateString() === \$date->toDateString() && \$st->show_time === \$time.':00';
                                            });
HTML;

$content = preg_replace($searchPattern, $replacePattern, $content);
file_put_contents($file, $content);
echo "Fixed collection filtering in showtimes/index.blade.php\n";

