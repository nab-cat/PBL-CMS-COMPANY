<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Artikel;

class ArtikelObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Artikel "created" event.
     */
    public function created(Artikel $artikel): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Artikel "updated" event.
     */
    public function updated(Artikel $artikel): void
    {
        $this->clearRelatedCache();

        // Also clear specific article cache if slug changed
        if ($artikel->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('artikel');
        }
    }

    /**
     * Handle the Artikel "deleted" event.
     */
    public function deleted(Artikel $artikel): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all artikel-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('artikel');
    }
}
