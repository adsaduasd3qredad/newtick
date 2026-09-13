<?php
$file = 'resources/views/auth/login.blade.php';
$content = file_get_contents($file);

// Replace the Title section
$content = preg_replace(
    '/<h1 class="text-2xl font-bold font-display text-white">.*?<\/h1>\s*<p.*?>.*?<\/p>/s',
    '<h1 class="text-2xl font-bold font-display text-white">Login</h1>',
    $content
);

// Replace Email label
$content = preg_replace(
    '/<label.*?\(Email\)<\/label>/',
    '<label class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>',
    $content
);

// Replace Password label
$content = preg_replace(
    '/<label.*?\(Password\)<\/label>/',
    '<label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>',
    $content
);

// Replace Login button text
$content = preg_replace(
    '/<button type="submit"(.*?)>.*?<\/button>/s',
    '<button type="submit"$1>Login</button>',
    $content
);

file_put_contents($file, $content);
echo "Updated login.blade.php\n";

