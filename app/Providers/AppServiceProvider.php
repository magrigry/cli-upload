<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../vendor/picocss/pico/css/pico.min.css' => public_path('css/pico/pico.min.css'),
            __DIR__.'/../../vendor/picocss/pico/css/pico.classless.min.css' => public_path('css/pico/pico.classless.min.css'),
        ], 'assets');

        RateLimiter::for('upload', function (Request $request) {

            return [
                Limit::perMinute(config('upload.rate-limit.upload.per-ip-per-minute'))->by($request->ip()),
                Limit::perDay(config('upload.rate-limit.upload.per-ip-per-day'))->by($request->ip()),
                Limit::perDay(config('upload.rate-limit.upload.everyone-per-day')),
            ];
        });

        RateLimiter::for('download', function (Request $request) {
            return [
                Limit::perMinute(config('upload.rate-limit.download.per-ip-per-minute'))->by($request->ip()),
                Limit::perDay(config('upload.rate-limit.download.per-ip-per-day'))->by($request->ip()),
                Limit::perDay(config('upload.rate-limit.download.everyone-per-day')),
            ];
        });
    }
}
