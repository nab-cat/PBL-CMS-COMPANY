<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Feedback;
use App\Notifications\FeedbackResponseNotification;

class FeedbackObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Feedback "created" event.
     */
    public function created(Feedback $feedback): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Feedback "updated" event.
     */
    public function updated(Feedback $feedback): void
    {
        // Check if tanggapan_feedback was added (response was given)
        if (
            $feedback->isDirty('tanggapan_feedback') &&
            !empty($feedback->tanggapan_feedback) &&
            empty($feedback->getOriginal('tanggapan_feedback'))
        ) {

            // Send notification to the user who submitted the feedback
            if ($feedback->user && $feedback->user->email) {
                try {
                    $feedback->user->notify(new FeedbackResponseNotification($feedback));
                } catch (\Exception $e) {
                    // Log the error but don't break the update process
                    \Log::error('Failed to send feedback response notification', [
                        'feedback_id' => $feedback->id_feedback,
                        'user_id' => $feedback->id_user,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->clearRelatedCache();
    }

    /**
     * Handle the Feedback "deleted" event.
     */
    public function deleted(Feedback $feedback): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all feedback-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/feedback');
    }
}
