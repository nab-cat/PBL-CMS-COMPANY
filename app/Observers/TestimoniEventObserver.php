<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\TestimoniEvent;

class TestimoniEventObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the TestimoniEvent "created" event.
     */
    public function created(TestimoniEvent $testimoniEvent): void
    {
        $this->clearRelatedCache($testimoniEvent);
    }

    /**
     * Handle the TestimoniEvent "updated" event.
     */
    public function updated(TestimoniEvent $testimoniEvent): void
    {
        $this->clearRelatedCache($testimoniEvent);
    }

    /**
     * Handle the TestimoniEvent "deleted" event.
     */
    public function deleted(TestimoniEvent $testimoniEvent): void
    {
        $this->clearRelatedCache($testimoniEvent);
    }

    /**
     * Clear all testimoni event-related cache
     */
    protected function clearRelatedCache(TestimoniEvent $testimoniEvent): void
    {
        // Clear testimoni cache for all event endpoints
        $this->cacheService->clearEndpointCache('testimoni/event');

        // Also clear specific event cache if we can identify it
        if ($testimoniEvent->id_event) {
            $this->cacheService->clearEndpointCache('testimoni/event/' . $testimoniEvent->id_event);
        }
    }
}
