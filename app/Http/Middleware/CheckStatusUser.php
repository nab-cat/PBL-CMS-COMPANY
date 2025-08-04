<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStatusUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->status === 'nonaktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Akun Anda sudah tidak aktif. Silakan hubungi administrator.');
        }

        $allowedRoutes = [
            'filament.admin.auth.login',
            'filament.admin.auth.profile',
            'filament.admin.auth.register',
            'filament.admin.auth.email-verification.prompt',
            'filament.admin.auth.email-verification.verify'
        ];

        if (
            $request->routeIs('filament.admin.*') &&
            !in_array($request->route()->getName(), $allowedRoutes)
        ) {
            if (in_array($user->status_kepegawaian, ['Tetap', 'Magang', 'Kontrak'])) {
                return $next($request); // izinkan lanjut
            } else {
                return redirect('/')
                    ->with('error', 'Anda tidak memiliki akses ke panel admin.');
            }
        }

        return $next($request); // jika bukan route admin, izinkan lanjut
    }
}

