<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\KategoriUnduhan;

class KategoriUnduhanObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the KategoriUnduhan "created" event.
     */
    public function created(KategoriUnduhan $kategoriUnduhan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriUnduhan "updated" event.
     */
    public function updated(KategoriUnduhan $kategoriUnduhan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriUnduhan "deleted" event.
     */
    public function deleted(KategoriUnduhan $kategoriUnduhan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all unduhan category-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear download categories and downloads cache
        $this->cacheService->clearEndpointCache('api/unduhan');
    }
}
