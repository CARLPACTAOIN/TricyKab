<?php

namespace App\Providers;

use App\Contracts\OtpSmsSender;
use App\Services\Otp\LogOtpSmsSender;
use App\Services\Otp\UniSmsOtpSender;
use App\Services\Sms\UniSmsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // PRD §5.2 — bind production SMS sender when UniSMS secret is configured.
        // Falls back to the log-only sender in dev/debug mode so no real SMS is sent.
        $this->app->singleton(UniSmsService::class);
        $this->app->singleton(OtpSmsSender::class, function () {
            if (! empty(config('services.unisms.api_secret_key'))) {
                return new UniSmsOtpSender(app(UniSmsService::class));
            }

            return new LogOtpSmsSender;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PRD §5.3 — named rate limiters for mobile API endpoints.
        $this->configureRateLimiting();

        if ($this->app->runningInConsole()) {
            return;
        }

        $host = request()->header('Host', '');
        $environment = config('app.env', 'production');

        \Illuminate\Support\Facades\Log::info('AppServiceProvider booting. Host: '.$host.', Env: '.$environment);

        if ($environment === 'production' || str_contains($host, 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    /**
     * PRD §5.3 OTP Business Rules:
     *  - Max 5 OTP requests per phone per hour.
     *  - Max 10 verify attempts per hour per phone.
     *  - General API: 120 requests/minute per authenticated user.
     */
    protected function configureRateLimiting(): void
    {
        // OTP request: keyed on phone_number from request body (5/hour per phone)
        RateLimiter::for('otp-request', function (Request $request) {
            $phone = (string) $request->input('phone_number', $request->ip());

            return Limit::perHour(5)->by('otp-req:'.$phone)->response(function () {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'OTP_RATE_LIMIT_EXCEEDED',
                        'message' => 'Too many OTP requests. Try again in an hour.',
                    ],
                ], 429);
            });
        });

        // OTP verify: keyed on phone_number (10/hour per phone)
        RateLimiter::for('otp-verify', function (Request $request) {
            $phone = (string) $request->input('phone_number', $request->ip());

            return Limit::perHour(10)->by('otp-ver:'.$phone)->response(function () {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'OTP_VERIFY_RATE_LIMIT',
                        'message' => 'Too many verification attempts. Try again later.',
                    ],
                ], 429);
            });
        });

        // General mobile API: 120 requests/minute per authenticated user (falls back to IP)
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?->id ?? $request->ip();

            return Limit::perMinute(120)->by('api:'.$key);
        });
    }
}
