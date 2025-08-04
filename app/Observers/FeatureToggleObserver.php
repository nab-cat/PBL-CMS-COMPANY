<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\FeatureToggle;

class FeatureToggleObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the FeatureToggle "created" event.
     */
    public function created(FeatureToggle $featureToggle): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the FeatureToggle "updated" event.
     */
    public function updated(FeatureToggle $featureToggle): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the FeatureToggle "deleted" event.
     */
    public function deleted(FeatureToggle $featureToggle): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all feature toggle-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/feature-toggles');
    }
}
