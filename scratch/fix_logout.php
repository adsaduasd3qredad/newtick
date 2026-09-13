<?php
$file = 'app/Providers/AppServiceProvider.php';
$content = file_get_contents($file);

$binding = <<<PHP
    public function boot(): void
    {
        \$this->app->bind(\Filament\Http\Responses\Auth\Contracts\LogoutResponse::class, function () {
            return new class implements \Filament\Http\Responses\Auth\Contracts\LogoutResponse {
                public function toResponse(\$request)
                {
                    return redirect('/');
                }
            };
        });
    }
PHP;

$content = preg_replace('/public function boot\(\): void\s*\{\s*\/\/\s*\}/', $binding, $content);

file_put_contents($file, $content);
echo "Updated AppServiceProvider to bind custom LogoutResponse\n";

