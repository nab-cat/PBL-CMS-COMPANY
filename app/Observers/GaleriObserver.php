<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Galeri;

class GaleriObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Galeri "created" event.
     */
    public function created(Galeri $galeri): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Galeri "updated" event.
     */
    public function updated(Galeri $galeri): void
    {
        $this->clearRelatedCache();

        // Clear specific gallery cache if slug changed
        if ($galeri->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/galeri');
        }
    }

    /**
     * Handle the Galeri "deleted" event.
     */
    public function deleted(Galeri $galeri): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all galeri-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/galeri');
    }
}
