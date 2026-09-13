<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

        public function boot(): void
    {
        $this->app->bind(\Filament\Http\Responses\Auth\Contracts\LogoutResponse::class, function () {
            return new class implements \Filament\Http\Responses\Auth\Contracts\LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/');
                }
            };
        });
    }
}