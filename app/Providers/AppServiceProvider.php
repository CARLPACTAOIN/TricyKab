<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Log::info('AppServiceProvider booting. Host: ' . request()->header('Host') . ', Env: ' . $this->app->environment());
        if ($this->app->environment('production') || str_contains(request()->header('Host'), 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
