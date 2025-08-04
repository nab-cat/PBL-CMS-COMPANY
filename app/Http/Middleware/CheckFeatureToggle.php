<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\FeatureToggle;

class CheckFeatureToggle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $featureKey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        try {
            $feature = FeatureToggle::where('key', $featureKey)->first();

            if (!$feature || !$feature->status_aktif) {
                abort(404); 
            }
        } catch (\Throwable $e) {
            abort(404); 
        }

        return $next($request);
    }
}
