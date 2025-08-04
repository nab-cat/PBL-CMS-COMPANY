<?php

namespace App\Installer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class InstallMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->alreadyInstalled()) {
            return redirect(URL::to('/'))->with('error', 'Application has already been installed.');
        }

        // Check if the .env file exists
        if (file_exists(base_path('.env')) && env('APP_INSTALLED') === true) {
            return redirect(URL::to('/'))->with('error', 'Application has already been installed.');
        }

        return $next($request);
    }

    public function alreadyInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
