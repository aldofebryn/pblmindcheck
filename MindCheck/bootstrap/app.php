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
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // ⚠️ TEMPORARY — Bypass CSRF for JMeter load testing
        // TODO: Remove this after testing is complete!
        // $middleware->validateCsrfTokens(except: [
            //'/screening/autosave',
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
