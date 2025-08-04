<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     * 
     * Configures route groups with appropriate middleware and rate limiting:
     * - API routes: Apply 'api' middleware group with API-specific rate limiting
     * - Web routes: Apply 'web' middleware group with web-specific rate limiting  
     * - Installer routes: Apply custom middleware without rate limiting for smooth installation
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // API routes with API-specific rate limiting
            Route::prefix('api')
                ->middleware(['api', 'throttle:api'])
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // Web routes with web-specific rate limiting
            Route::middleware(['web', 'throttle:web'])
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // Installer routes without rate limiting
            Route::middleware(['installCheck'])
                ->namespace($this->namespace)
                ->group(base_path('routes/installer.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     * 
     * Sets up multiple rate limiting strategies:
     * - API routes: 1000 requests per minute for authenticated users, 100 for guests
     * - Web routes: 1000 requests per minute for authenticated users, 500 for guests
     * - Login attempts: 5 attempts per minute (handled in LoginRequest)
     * - Installer routes: No rate limiting for smooth installation process
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // API rate limiting - stricter limits for API endpoints
        RateLimiter::for('api', function (Request $request) {
            // Authenticated users get higher limits
            if ($request->user()) {
                return Limit::perMinute(1000)->by($request->user()->id);
            }

            // Guest users get lower limits based on IP
            return Limit::perMinute(100)->by($request->ip());
        });

        // Web rate limiting - more lenient for web interface
        RateLimiter::for('web', function (Request $request) {
            // Authenticated users get higher limits
            if ($request->user()) {
                return Limit::perMinute(1000)->by($request->user()->id);
            }

            // Guest users get moderate limits for web browsing
            return Limit::perMinute(500)->by($request->ip());
        });

        // Global rate limiting for all requests - prevents abuse
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(5000)->by($request->ip());
        });
    }
}
