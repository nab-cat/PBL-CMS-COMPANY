<?php

namespace App\Http\Middleware;

use App\Services\ApiCacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiCacheMiddleware
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Skip caching for authenticated routes (except specific ones)
        if ($request->user() && !$this->shouldCacheAuthenticatedRoute($request)) {
            return $next($request);
        }

        // Skip caching for routes with dynamic content that shouldn't be cached
        if ($this->shouldSkipCaching($request)) {
            return $next($request);
        }

        $cacheKey = $this->cacheService->generateCacheKey($request);
        $cacheDuration = $this->cacheService->getCacheDuration($request->path());

        // Try to get from cache first
        $cachedResponse = cache()->get($cacheKey);

        if ($cachedResponse) {
            $response = response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders($cachedResponse['headers']);

            // Add cache headers
            $response->headers->set('X-Cache-Status', 'HIT');
            $response->headers->set('X-Cache-Key', $cacheKey);

            return $response;
        }

        // Execute the request
        $response = $next($request);

        // Cache successful responses
        if ($response->getStatusCode() === 200) {
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->getHeadersToCache($response),
                'cached_at' => now()->toISOString(),
            ];

            cache()->put($cacheKey, $cacheData, $cacheDuration * 60);

            // Add cache headers
            $response->headers->set('X-Cache-Status', 'MISS');
            $response->headers->set('X-Cache-Key', $cacheKey);
            $response->headers->set('X-Cache-Duration', $cacheDuration . ' minutes');
        }

        return $response;
    }

    /**
     * Determine if authenticated routes should be cached
     */
    protected function shouldCacheAuthenticatedRoute(Request $request): bool
    {
        $cachableAuthRoutes = [
            'api/user', // User profile can be cached briefly
        ];

        return in_array($request->path(), $cachableAuthRoutes);
    }

    /**
     * Determine if request should skip caching
     */
    protected function shouldSkipCaching(Request $request): bool
    {
        $skipPatterns = [
            'api/*/download/*',     // Download endpoints
            'api/*/register',       // Registration endpoints
            'api/*/unregister',     // Unregistration endpoints
            'api/*/check-*',        // Check endpoints
            'api/feedback',         // Feedback submission
            'api/lamaran',          // Job application submission
            'api/lamaran/*',        // Specific lamaran endpoints
            'api/lamaran/user/*',   // User specific lamaran endpoints
            'api/lamaran/check/*',  // Check application endpoints
            'api/testimoni/*/store', // Testimoni submission
        ];

        $path = $request->path();

        foreach ($skipPatterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        // Skip if request has specific query parameters that indicate dynamic content
        $skipParams = ['_', 'timestamp', 'nocache'];
        foreach ($skipParams as $param) {
            if ($request->has($param)) {
                return true;
            }
        }

        return false;
    }    /**
         * Get headers that should be cached
         */
    protected function getHeadersToCache($response): array
    {
        $headersToCache = [
            'Content-Type',
            'Content-Length',
            'Last-Modified',
            'ETag',
        ];

        $headers = [];
        foreach ($headersToCache as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }
}
