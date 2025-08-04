<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ApiCacheService
{
    /**
     * Default cache duration in minutes
     */
    const DEFAULT_CACHE_DURATION = 60; // 1 hour

    /**
     * Cache durations for different endpoints (in minutes)
     */
    protected array $cacheDurations = [
        'artikel' => 30,           // Articles cache for 30 minutes
        'artikel.categories' => 120, // Categories cache for 2 hours
        'event' => 15,             // Events cache for 15 minutes
        'produk' => 45,            // Products cache for 45 minutes
        'profil-perusahaan' => 240, // Company profile cache for 4 hours
        'media-sosial' => 180,     // Media social cache for 3 hours
        'struktur-organisasi' => 360, // Organization structure cache for 6 hours
        'konten-slider' => 60,     // Slider content cache for 1 hour
        'mitra' => 120,            // Partners cache for 2 hours
        'feature-toggles' => 300,  // Feature toggles cache for 5 hours
        'galeri' => 30,            // Gallery cache for 30 minutes
        'lowongan' => 60,          // Job openings cache for 1 hour
        'lamaran' => 5,            // Job applications cache for 5 minutes (short cache for dynamic data)
        'case-study' => 90,        // Case studies cache for 1.5 hours
        'unduhan' => 45,           // Downloads cache for 45 minutes
        'feedback' => 30,          // Feedback cache for 30 minutes
        'testimoni' => 30,         // Testimoni cache for 30 minutes
        'testimoni.produk' => 30,  // Product testimoni cache for 30 minutes
        'testimoni.artikel' => 30, // Article testimoni cache for 30 minutes
        'testimoni.event' => 30,   // Event testimoni cache for 30 minutes
    ];

    /**
     * Generate cache key for API request
     */
    public function generateCacheKey(Request $request): string
    {
        $path = $request->path();
        $queryParams = $request->query();

        // Sort query parameters for consistent cache keys
        ksort($queryParams);

        $cacheKey = 'api_cache:' . $path;

        if (!empty($queryParams)) {
            $cacheKey .= ':' . md5(http_build_query($queryParams));
        }

        return $cacheKey;
    }

    /**
     * Get cache duration for specific endpoint
     */
    public function getCacheDuration(string $endpoint): int
    {
        // Extract the main endpoint from path
        $endpointParts = explode('/', $endpoint);
        $mainEndpoint = $endpointParts[1] ?? '';

        // Check for specific sub-endpoints
        if (count($endpointParts) >= 3) {
            $subEndpoint = $mainEndpoint . '.' . $endpointParts[2];
            if (isset($this->cacheDurations[$subEndpoint])) {
                return $this->cacheDurations[$subEndpoint];
            }
        }

        return $this->cacheDurations[$mainEndpoint] ?? self::DEFAULT_CACHE_DURATION;
    }

    /**
     * Get cached response or execute callback and cache result
     */
    public function remember(string $cacheKey, int $duration, callable $callback)
    {
        return Cache::remember($cacheKey, $duration * 60, $callback); // Convert minutes to seconds
    }    /**
         * Clear cache for specific endpoint
         */
    public function clearEndpointCache(string $endpoint): void
    {
        $cacheDriver = config('cache.default');

        // Remove leading 'api/' if present to avoid duplication
        $cleanEndpoint = str_replace('api/', '', $endpoint);

        if ($cacheDriver === 'database') {
            $table = config('cache.stores.database.table', 'cache');
            // Laravel prefixes cache keys, so we need to account for that
            \DB::table($table)->where('key', 'like', '%api_cache:api/' . $cleanEndpoint . '%')->delete();
        } else {
            // For non-database drivers, just clear all cache as fallback
            Cache::flush();
        }
    }

    /**
     * Clear all API cache
     */
    public function clearAllApiCache(): void
    {
        $cacheDriver = config('cache.default');

        if ($cacheDriver === 'database') {
            $table = config('cache.stores.database.table', 'cache');
            \DB::table($table)->where('key', 'like', '%api_cache:%')->delete();
        } else {
            // For non-database drivers, just clear all cache as fallback
            Cache::flush();
        }
    }/**
     * Clear cache by pattern (works with Redis and File cache drivers)
     */
    protected function clearCacheByPattern(string $pattern): void
    {
        $cacheDriver = config('cache.default');

        if ($cacheDriver === 'redis') {
            $this->clearRedisCacheByPattern($pattern);
        } elseif ($cacheDriver === 'file') {
            $this->clearFileCacheByPattern($pattern);
        } elseif ($cacheDriver === 'database') {
            $this->clearDatabaseCacheByPattern($pattern);
        } else {
            // For array driver and others, clear all cache as fallback
            Cache::flush();
        }
    }

    /**
     * Clear Redis cache by pattern
     */
    protected function clearRedisCacheByPattern(string $pattern): void
    {
        $redis = Cache::getRedis();
        $keys = $redis->keys($pattern);

        if (!empty($keys)) {
            $redis->del($keys);
        }
    }

    /**
     * Clear file cache by pattern
     */
    protected function clearFileCacheByPattern(string $pattern): void
    {
        $cacheDir = storage_path('framework/cache/data');
        $pattern = str_replace(['api_cache:', '*'], ['', ''], $pattern);

        $files = glob($cacheDir . '/*' . $pattern . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }    /**
         * Clear database cache by pattern
         */
    protected function clearDatabaseCacheByPattern(string $pattern): void
    {
        $table = config('cache.stores.database.table', 'cache');
        $pattern = str_replace(['api_cache:', '*'], ['laravel_cache:api_cache:', '%'], $pattern);

        \DB::table($table)->where('key', 'like', $pattern)->delete();
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $cacheDriver = config('cache.default');
        $stats = [
            'driver' => $cacheDriver,
            'total_keys' => 0,
            'api_cache_keys' => 0,
        ];

        if ($cacheDriver === 'redis') {
            $redis = Cache::getRedis();
            $allKeys = $redis->keys('*');
            $apiKeys = $redis->keys('api_cache:*');

            $stats['total_keys'] = count($allKeys);
            $stats['api_cache_keys'] = count($apiKeys);
        } elseif ($cacheDriver === 'database') {
            $table = config('cache.stores.database.table', 'cache');
            $stats['total_keys'] = \DB::table($table)->count();
            $stats['api_cache_keys'] = \DB::table($table)->where('key', 'like', 'api_cache:%')->count();
        }

        return $stats;
    }
}
