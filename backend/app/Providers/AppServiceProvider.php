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
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('reads', fn (Request $request) => Limit::perMinute(300)->by($request->user()?->id ?? $request->ip()));
        RateLimiter::for('writes', fn (Request $request) => Limit::perMinute(100)->by($request->user()?->id ?? $request->ip()));
        RateLimiter::for('comments', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?? $request->ip()));
        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinutes(15, 8)->by($request->ip()));
        RateLimiter::for('tracking', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }
}
