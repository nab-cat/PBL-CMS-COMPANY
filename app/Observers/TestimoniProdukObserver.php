<?php

namespace App\Observers;

use App\Services\ApiCacheService;
use App\Models\TestimoniProduk;

class TestimoniProdukObserver
{
    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the TestimoniProduk "created" event.
     */
    public function created(TestimoniProduk $testimoniProduk): void
    {
        $this->clearRelatedCache($testimoniProduk);
    }

    /**
     * Handle the TestimoniProduk "updated" event.
     */
    public function updated(TestimoniProduk $testimoniProduk): void
    {
        $this->clearRelatedCache($testimoniProduk);
    }

    /**
     * Handle the TestimoniProduk "deleted" event.
     */
    public function deleted(TestimoniProduk $testimoniProduk): void
    {
        $this->clearRelatedCache($testimoniProduk);
    }

    /**
     * Clear all testimoni produk-related cache
     */
    protected function clearRelatedCache(TestimoniProduk $testimoniProduk): void
    {
        // Clear testimoni cache for all produk endpoints
        $this->cacheService->clearEndpointCache('testimoni/produk');

        // Also clear specific product cache if we can identify it
        if ($testimoniProduk->id_produk) {
            $this->cacheService->clearEndpointCache('testimoni/produk/' . $testimoniProduk->id_produk);
        }
    }
}
