<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\MediaSosial;

class MediaSosialObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the MediaSosial "created" event.
     */
    public function created(MediaSosial $mediaSosial): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the MediaSosial "updated" event.
     */
    public function updated(MediaSosial $mediaSosial): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the MediaSosial "deleted" event.
     */
    public function deleted(MediaSosial $mediaSosial): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all media sosial-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/media-sosial');
    }
}
