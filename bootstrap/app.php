<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Inregistram alias-ul is_admin
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\isAdmin::class,
        ]);
    })    
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
