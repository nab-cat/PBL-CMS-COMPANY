<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\Produk;

class ProdukObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        $this->clearRelatedCache();

        // Clear specific product cache if slug changed
        if ($produk->wasChanged('slug')) {
            $this->cacheService->clearEndpointCache('api/produk');
        }
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        $this->clearRelatedCache();
    }

    /**
     * Clear all produk-related cache
     */
    protected function clearRelatedCache(): void
    {
        // Clear product-related endpoints
        $this->cacheService->clearEndpointCache('api/produk');

        // Also clear testimonials for this product
        $this->cacheService->clearEndpointCache('api/testimoni');
    }
}
