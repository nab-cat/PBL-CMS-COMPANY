<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\StrukturOrganisasi;
use Illuminate\Support\Facades\Log;

class StrukturOrganisasiObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the StrukturOrganisasi "created" event.
     * This typically happens during user sync operations.
     */
    public function created(StrukturOrganisasi $strukturOrganisasi): void
    {
        Log::info('StrukturOrganisasi created', [
            'id' => $strukturOrganisasi->id_struktur_organisasi,
            'user_id' => $strukturOrganisasi->id_user,
            'jabatan' => $strukturOrganisasi->jabatan,
            'urutan' => $strukturOrganisasi->urutan
        ]);

        $this->clearRelatedCache();
    }

    /**
     * Handle the StrukturOrganisasi "updated" event.
     * This happens when admin edits position details or reorders structure.
     */
    public function updated(StrukturOrganisasi $strukturOrganisasi): void
    {
        $changes = $strukturOrganisasi->getChanges();

        Log::info('StrukturOrganisasi updated', [
            'id' => $strukturOrganisasi->id_struktur_organisasi,
            'user_id' => $strukturOrganisasi->id_user,
            'changes' => $changes
        ]);

        // Clear cache immediately after any update
        $this->clearRelatedCache();

        // If urutan (order) was changed, we might need to reorder other positions
        if (array_key_exists('urutan', $changes)) {
            $this->handleUrutanChange($strukturOrganisasi, $changes['urutan']);

            // Clear cache again after reordering
            $this->clearRelatedCache();
        }
    }

    /**
     * Handle the StrukturOrganisasi "saving" event.
     * Validate data before saving.
     */
    public function saving(StrukturOrganisasi $strukturOrganisasi): void
    {
        // Ensure urutan is always set
        if (empty($strukturOrganisasi->urutan)) {
            $maxUrutan = StrukturOrganisasi::max('urutan') ?? 0;
            $strukturOrganisasi->urutan = $maxUrutan + 1;
        }

        // Ensure tanggal_mulai is set
        if (empty($strukturOrganisasi->tanggal_mulai)) {
            $strukturOrganisasi->tanggal_mulai = now();
        }
    }

    /**
     * Handle urutan (order) changes to maintain consistency
     */
    protected function handleUrutanChange(StrukturOrganisasi $strukturOrganisasi, int $oldUrutan): void
    {
        $newUrutan = $strukturOrganisasi->urutan;

        // If moving to a higher position (lower number)
        if ($newUrutan < $oldUrutan) {
            StrukturOrganisasi::where('urutan', '>=', $newUrutan)
                ->where('urutan', '<', $oldUrutan)
                ->where('id_struktur_organisasi', '!=', $strukturOrganisasi->id_struktur_organisasi)
                ->increment('urutan');
        }
        // If moving to a lower position (higher number)
        elseif ($newUrutan > $oldUrutan) {
            StrukturOrganisasi::where('urutan', '>', $oldUrutan)
                ->where('urutan', '<=', $newUrutan)
                ->where('id_struktur_organisasi', '!=', $strukturOrganisasi->id_struktur_organisasi)
                ->decrement('urutan');
        }
    }

    /**
     * Clear all struktur organisasi-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear API endpoint caches
        $this->cacheService->clearEndpointCache('api/struktur-organisasi');
        $this->cacheService->clearEndpointCache('api/struktur-organisasi?status=active');
        $this->cacheService->clearEndpointCache('api/struktur-organisasi?status=inactive');

        // Clear all possible cache variations
        $this->cacheService->clearEndpointCache('struktur-organisasi');

        // Clear Laravel application cache that might interfere
        try {
            \Illuminate\Support\Facades\Cache::forget('struktur_organisasi_last_sync_check');
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Exception $e) {
            Log::warning('Failed to clear additional cache', ['error' => $e->getMessage()]);
        }

        Log::info('StrukturOrganisasi cache cleared completely');
    }

    /**
     * Static method to clear struktur organisasi cache from external observers
     */
    public static function clearCache(): void
    {
        $cacheService = app(ApiCacheService::class);

        // Clear API endpoint caches
        $cacheService->clearEndpointCache('api/struktur-organisasi');
        $cacheService->clearEndpointCache('api/struktur-organisasi?status=active');
        $cacheService->clearEndpointCache('api/struktur-organisasi?status=inactive');

        // Clear all possible cache variations
        $cacheService->clearEndpointCache('struktur-organisasi');

        // Clear Laravel application cache that might interfere
        try {
            \Illuminate\Support\Facades\Cache::forget('struktur_organisasi_last_sync_check');
        } catch (\Exception $e) {
            Log::warning('Failed to clear additional cache in static method', ['error' => $e->getMessage()]);
        }

        Log::info('StrukturOrganisasi cache cleared via static method');
    }
}
