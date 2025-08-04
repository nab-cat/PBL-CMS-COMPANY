<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Unduhan;

class UnduhanObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Unduhan "created" event.
     */
    public function created(Unduhan $unduhan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Unduhan "updated" event.
     */
    public function updated(Unduhan $unduhan): void
    {
        $this->clearRelatedCache();

        // Clear specific unduhan cache if slug changed
        if ($unduhan->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/unduhan');
        }
    }

    /**
     * Handle the Unduhan "deleted" event.
     */
    public function deleted(Unduhan $unduhan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all unduhan-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/unduhan');
    }
}
