<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\TestimoniSlider;

class TestimoniSliderObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the TestimoniSlider "created" event.
     */
    public function created(TestimoniSlider $testimoni): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the TestimoniSlider "updated" event.
     */
    public function updated(TestimoniSlider $testimoni): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the TestimoniSlider "deleted" event.
     */
    public function deleted(TestimoniSlider $testimoni): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all testimoni related cache
     */
    private function clearRelatedCache(): void
    {
        // Clear general testimoni cache
        $this->cacheService->clearEndpointCache('testimoni');

        // Clear specific testimoni type caches
        $this->cacheService->clearEndpointCache('testimoni/produk');
        $this->cacheService->clearEndpointCache('testimoni/artikel');
        $this->cacheService->clearEndpointCache('testimoni/event');
    }
}
