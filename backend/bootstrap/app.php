<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(HandleCors::class);

        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SanitizeInput::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SanitizeInput::class,
        ]);

        // Role alias
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('sanctum/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
    })
    ->withSchedule(function ($schedule): void {
        $cfg = config('backup.schedule');
        $backupTime = config('backup.time', '00:00');

        if ($cfg === 'daily') {
            $schedule->command('backup:run')->dailyAt($backupTime);
        } elseif ($cfg === 'weekly') {
            $schedule->command('backup:run')->weeklyOn(0, $backupTime);
        } elseif ($cfg === 'monthly') {
            $schedule->command('backup:run')->monthlyOn(1, $backupTime);
        }
    })->create();