<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\KategoriGaleri;

class KategoriGaleriObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the KategoriGaleri "created" event.
     */
    public function created(KategoriGaleri $kategoriGaleri): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriGaleri "updated" event.
     */
    public function updated(KategoriGaleri $kategoriGaleri): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriGaleri "deleted" event.
     */
    public function deleted(KategoriGaleri $kategoriGaleri): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all galeri category-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear gallery categories and gallery cache
        $this->cacheService->clearEndpointCache('api/galeri');
    }
}
