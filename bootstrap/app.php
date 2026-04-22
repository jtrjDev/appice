<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Roda em todo request web (inclusive Livewire Ajax)
    $middleware->web(append: [
        \App\Http\Middleware\InitializeTenancyByAuthUser::class,
    ]); 
    $middleware->alias([
            'tenant.auth' => \App\Http\Middleware\InitializeTenancyByAuthUser::class,
            'superadmin'  => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
