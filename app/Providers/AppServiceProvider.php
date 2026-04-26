<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // 🔥 FORCE HTTPS on production (IMPORTANT for Render/Vite)
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
