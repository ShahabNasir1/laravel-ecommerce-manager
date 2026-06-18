<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ageCheck;
use App\Http\Middleware\isAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // ADD THIS: Explicitly tells Laravel where to send guests
        $middleware->redirectTo(
            guests: '/login',
            users: '/'
        );

        $middleware->alias([
            'checkAge' => ageCheck::class,
            'isAdmin'  => isAdmin::class, // Keep your admin check
            // REMOVE 'customauth' from here entirely
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();