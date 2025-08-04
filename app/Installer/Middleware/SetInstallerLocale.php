<?php

namespace App\Installer\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetInstallerLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session, cookie, or default to 'en'
        $locale = session('installer_locale')
            ?? $request->cookie('installer_locale')
            ?? 'id';

        // Validate locale
        $supportedLocales = ['en', 'id'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'id';
        }

        // Set locale for the application
        app()->setLocale($locale);

        // Store in session for consistency
        session(['installer_locale' => $locale]);

        return $next($request);
    }
}
