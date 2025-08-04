<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\KontenSlider;

class KontenSliderObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the KontenSlider "created" event.
     */
    public function created(KontenSlider $kontenSlider): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KontenSlider "updated" event.
     */
    public function updated(KontenSlider $kontenSlider): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KontenSlider "deleted" event.
     */
    public function deleted(KontenSlider $kontenSlider): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all konten slider-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/konten-slider');
    }
}
