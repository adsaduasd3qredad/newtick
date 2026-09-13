<?php
$file = 'resources/views/layouts/client.blade.php';
$content = file_get_contents($file);

// Replace the iframe src
$content = preg_replace(
    '/src="https:\/\/www\.google\.com\/maps\/embed\?pb=.*?"/',
    'src="https://maps.google.com/maps?q=Rangsit+Science+Centre+for+Education&t=&z=15&ie=UTF8&iwloc=&output=embed"',
    $content
);

// Replace the a href link
$content = str_replace(
    'href="https://maps.app.goo.gl/wY2DDEt9k1B33G6v8"',
    'href="https://www.google.com/maps/search/?api=1&query=ศูนย์วิทยาศาสตร์เพื่อการศึกษารังสิต"',
    $content
);

file_put_contents($file, $content);
echo "Fixed maps links in client.blade.php\n";

