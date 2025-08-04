<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\KategoriArtikel;

class KategoriArtikelObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the KategoriArtikel "created" event.
     */
    public function created(KategoriArtikel $kategoriArtikel): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriArtikel "updated" event.
     */
    public function updated(KategoriArtikel $kategoriArtikel): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriArtikel "deleted" event.
     */
    public function deleted(KategoriArtikel $kategoriArtikel): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all artikel category-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear article categories and articles cache
        $this->cacheService->clearEndpointCache('api/artikel');
    }
}
