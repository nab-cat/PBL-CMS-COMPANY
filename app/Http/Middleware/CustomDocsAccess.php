<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomDocsAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika dalam mode development, izinkan akses
        if (app()->environment('local')) {
            return $next($request);
        }

        // Jika user tidak login, block akses
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Jika tidak memenuhi kriteria, tampilkan error 403
        abort(403, 'Akses ke dokumentasi API hanya diizinkan untuk Super admin');
    }
}
