<?php

namespace App\Observers;

use App\Models\Lamaran;
use App\Models\User;
use App\Notifications\LamaranStatusNotification;
use App\Services\ApiCacheService;

class LamaranObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Lamaran "created" event.
     *
     * @param  \App\Models\Lamaran  $lamaran
     * @return void
     */
    public function created(Lamaran $lamaran)
    {
        $this->clearRelatedCache($lamaran);
    }

    /**
     * Handle the Lamaran "updated" event.
     *
     * @param  \App\Models\Lamaran  $lamaran
     * @return void
     */
    public function updated(Lamaran $lamaran)
    {
        // Clear cache when lamaran is updated
        $this->clearRelatedCache($lamaran);

        // Only send notification when status_lamaran changes
        if ($lamaran->isDirty('status_lamaran')) {
            $oldStatus = $lamaran->getOriginal('status_lamaran');
            $newStatus = $lamaran->status_lamaran;

            // Only notify when status changes from "Diproses" to either "Diterima" or "Ditolak"
            if ($oldStatus === 'Diproses' && ($newStatus === 'Diterima' || $newStatus === 'Ditolak')) {
                $lowongan = $lamaran->lowongan;
                $user = $lamaran->user;

                if ($user && $lowongan) {
                    $user->notify(new LamaranStatusNotification($lamaran, $lowongan, $oldStatus, $newStatus));
                }
            }
        }
    }

    /**
     * Handle the Lamaran "deleted" event.
     *
     * @param  \App\Models\Lamaran  $lamaran
     * @return void
     */
    public function deleted(Lamaran $lamaran)
    {
        $this->clearRelatedCache($lamaran);
    }

    /**
     * Clear all lamaran-related cache
     */
    protected function clearRelatedCache(Lamaran $lamaran): void
    {
        // Clear lamaran API endpoints
        $this->cacheService->clearEndpointCache('api/lamaran');

        // Clear specific user lamaran cache if user exists
        if ($lamaran->id_user) {
            $this->cacheService->clearEndpointCache('api/lamaran/user/' . $lamaran->id_user);
        }

        // Clear specific lamaran cache
        if ($lamaran->id_lamaran) {
            $this->cacheService->clearEndpointCache('api/lamaran/' . $lamaran->id_lamaran);
        }

        // Clear check application cache if both user and lowongan exist
        if ($lamaran->id_user && $lamaran->id_lowongan) {
            $this->cacheService->clearEndpointCache('api/lamaran/check/' . $lamaran->id_user . '/' . $lamaran->id_lowongan);
        }

        // Also clear lowongan cache since application status affects lowongan view
        if ($lamaran->id_lowongan) {
            $this->cacheService->clearEndpointCache('api/lowongan/' . $lamaran->id_lowongan);
            $this->cacheService->clearEndpointCache('api/lowongan');
        }
    }
}