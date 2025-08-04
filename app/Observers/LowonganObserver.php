<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Lowongan;

class LowonganObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Lowongan "created" event.
     */
    public function created(Lowongan $lowongan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Lowongan "updated" event.
     */
    public function updated(Lowongan $lowongan): void
    {
        $this->clearRelatedCache();

        // Clear specific lowongan cache if slug changed
        if ($lowongan->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/lowongan');
        }
    }

    /**
     * Handle the Lowongan "deleted" event.
     */
    public function deleted(Lowongan $lowongan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all lowongan-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/lowongan');

        // Also clear lamaran cache since lowongan changes might affect lamaran display
        $this->cacheService->clearEndpointCache('api/lamaran');
    }
}
