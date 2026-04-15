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
        if ($this->app->runningInConsole()) {
            return;
        }

        $host = request()->header('Host', '');
        $environment = config('app.env', 'production');

        \Illuminate\Support\Facades\Log::info('AppServiceProvider booting. Host: ' . $host . ', Env: ' . $environment);

        if ($environment === 'production' || str_contains($host, 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
