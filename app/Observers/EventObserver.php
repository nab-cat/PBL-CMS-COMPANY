<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Event;

class EventObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        $this->clearRelatedCache();

        // Clear specific event cache if slug changed
        if ($event->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/event');
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all event-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear event-related endpoints
        $this->cacheService->clearEndpointCache('api/event');
    }
}
