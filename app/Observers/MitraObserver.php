<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Mitra;

class MitraObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Mitra "created" event.
     */
    public function created(Mitra $mitra): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Mitra "updated" event.
     */
    public function updated(Mitra $mitra): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Mitra "deleted" event.
     */
    public function deleted(Mitra $mitra): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all mitra-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/mitra');
    }
}
