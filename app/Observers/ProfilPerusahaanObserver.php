<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\ProfilPerusahaan;
use App\Helpers\ThemeHelper;

class ProfilPerusahaanObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the ProfilPerusahaan "created" event.
     */
    public function created(ProfilPerusahaan $profilPerusahaan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the ProfilPerusahaan "updated" event.
     */
    public function updated(ProfilPerusahaan $profilPerusahaan): void
    {
        $this->clearRelatedCache();

        // Clear theme cache when company profile is updated
        if ($profilPerusahaan->wasChanged('tema_perusahaan')) {
            ThemeHelper::clearThemeCache();
        }
    }

    /**
     * Handle the ProfilPerusahaan "deleted" event.
     */
    public function deleted(ProfilPerusahaan $profilPerusahaan): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the ProfilPerusahaan "saved" event.
     */
    public function saved(ProfilPerusahaan $profilPerusahaan): void
    {
        // Clear theme cache when company profile is saved
        if ($profilPerusahaan->wasChanged('tema_perusahaan')) {
            ThemeHelper::clearThemeCache();
        }
    }

    /**
     * Clear all profil perusahaan-related cache
     */
    protected function clearRelatedCache(): void
    {
        $this->cacheService->clearEndpointCache('api/profil-perusahaan');
    }
}
