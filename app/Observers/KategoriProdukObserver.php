<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\KategoriProduk;

class KategoriProdukObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the KategoriProduk "created" event.
     */
    public function created(KategoriProduk $kategoriProduk): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriProduk "updated" event.
     */
    public function updated(KategoriProduk $kategoriProduk): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the KategoriProduk "deleted" event.
     */
    public function deleted(KategoriProduk $kategoriProduk): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all produk category-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear product categories and products cache
        $this->cacheService->clearEndpointCache('api/produk');
    }
}
