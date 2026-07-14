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
        // Add CORS middleware globally
        $middleware->append(HandleCors::class);

        // Replace the default VerifyCsrfToken with our custom one
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Add Sanctum middleware for API requests
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Trust all proxies for local development (XAMPP, Docker, etc.)
        // In production, set this to your actual proxy IPs
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withSchedule(function ($schedule) : void {
        $cfg = config('backup.schedule');
        $backupTime = config('backup.time', '00:00');

        if ($cfg === 'daily') {
            $schedule->command('backup:run')->dailyAt($backupTime);
        } elseif ($cfg === 'weekly') {
            // run weekly on Sunday at the configured time
            $schedule->command('backup:run')->weeklyOn(0, $backupTime);
        } elseif ($cfg === 'monthly') {
            // run on the first day of every month at the configured time
            $schedule->command('backup:run')->monthlyOn(1, $backupTime);
        }
    })->create();
