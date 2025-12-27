<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        // Enforce production to use S3 if FILESYSTEM_STRICT_S3=true; otherwise log a critical warning
        try {
            if (app()->environment('production') && config('filesystems.default') !== 's3') {
                Log::critical('Production environment detected but FILESYSTEM_DRIVER is not set to s3. Please ensure FILESYSTEM_DRIVER=s3 in production.');
                if (env('FILESYSTEM_STRICT_S3', false)) {
                    throw new \RuntimeException('FILESYSTEM_DRIVER must be s3 in production');
                }
            }
        } catch (\Exception $e) {
            Log::error('Storage check failed: ' . $e->getMessage());
        }
    }
}
