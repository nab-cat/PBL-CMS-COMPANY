<?php

namespace App\Installer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the app is not installed yet, redirect to installation
        if (!$this->isInstalled()) {
            return redirect('install-app');
        }

        return $next($request);
    }

    /**
     * Check if application is installed
     * 
     * @return bool
     */
    private function isInstalled(): bool
    {
        return file_exists(storage_path('installed')) ||
            (file_exists(base_path('.env')) && env('APP_INSTALLED') === true);
    }
}
