<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\CaseStudy;

class CaseStudyObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the CaseStudy "created" event.
     */
    public function created(CaseStudy $caseStudy): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the CaseStudy "updated" event.
     */
    public function updated(CaseStudy $caseStudy): void
    {
        $this->clearRelatedCache();

        // Clear specific case study cache if slug changed
        if ($caseStudy->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/case-study');
        }
    }

    /**
     * Handle the CaseStudy "deleted" event.
     */
    public function deleted(CaseStudy $caseStudy): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all case study-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/case-study');
    }
}
