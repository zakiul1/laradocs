<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register your custom middleware here
        /*    $middleware->append(\App\Http\Middleware\EnsureRole::class); */
        // OR give it a short alias (recommended)
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, $request) {
            $limit = ini_get('post_max_size') ?: 'server limit';
            return back()
                ->withErrors(['documents' => "Your upload exceeded the server limit ($limit). Try smaller files or contact admin."])
                ->withInput();
        });
    })->create();