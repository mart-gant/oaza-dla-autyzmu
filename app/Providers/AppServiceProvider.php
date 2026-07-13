<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

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
        // Log nieudanych prób logowania (zarówno Web jak i API)
        Event::listen(Failed::class, function (Failed $event) {
            Log::channel('security')->warning('Failed login attempt', [
                'email' => $event->credentials['email'] ?? null,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
                'user_id' => $event->user ? $event->user->id : null,
                'timestamp' => now()->toIso8601String(),
            ]);
        });

        // Log poprawnych logowań
        Event::listen(Login::class, function (Login $event) {
            Log::channel('security')->info('User logged in', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'guard' => $event->guard,
                'timestamp' => now()->toIso8601String(),
            ]);
        });
    }
}
