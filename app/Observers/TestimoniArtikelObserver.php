<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\TestimoniArtikel;

class TestimoniArtikelObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the TestimoniArtikel "created" event.
     */
    public function created(TestimoniArtikel $testimoniArtikel): void
    {
        $this->clearRelatedCache($testimoniArtikel);
    }

    /**
     * Handle the TestimoniArtikel "updated" event.
     */
    public function updated(TestimoniArtikel $testimoniArtikel): void
    {
        $this->clearRelatedCache($testimoniArtikel);
    }

    /**
     * Handle the TestimoniArtikel "deleted" event.
     */
    public function deleted(TestimoniArtikel $testimoniArtikel): void
    {
        $this->clearRelatedCache($testimoniArtikel);
    }

    /**
     * Clear all testimoni artikel-related cache
     */
    protected function clearRelatedCache(TestimoniArtikel $testimoniArtikel): void
    {
        // Clear testimoni cache for all artikel endpoints
        $this->cacheService->clearEndpointCache('testimoni/artikel');

        // Also clear specific artikel cache if we can identify it
        if ($testimoniArtikel->id_artikel) {
            $this->cacheService->clearEndpointCache('testimoni/artikel/' . $testimoniArtikel->id_artikel);
        }
    }
}
