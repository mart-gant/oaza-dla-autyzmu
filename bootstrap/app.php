<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust proxies (required for Render.com and reverse proxies)
        $middleware->trustProxies(at: '*');
        
        // Globalne middleware bezpieczeństwa
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\ForceHttps::class);

        // Zablokowane konta tracą dostęp od razu, także z otwartą sesją lub tokenem JWT
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureNotSuspended::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\EnsureNotSuspended::class);
        
        // Rate limiting dla API
        $middleware->throttleApi();
        
        // Rate limiting aliasy
        $middleware->alias([
            'throttle.login' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,1',
            'throttle.register' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':3,60',
            'throttle.contact' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':3,60',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
