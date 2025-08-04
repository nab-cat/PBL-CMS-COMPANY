<?php

namespace App\Filament\Widgets\ContentManager\Produk;

use App\Models\Produk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopProducts extends BaseWidget
{
    protected ?string $heading = 'Latest Products';
    protected static ?int $sort = 14;
    protected string|int|array $columnSpan = 2;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $latestProducts = Produk::query()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        return $latestProducts->map(function ($product) {
            // Clean the price string by removing 'Rp ', dots, and any other non-numeric characters
            $cleanedPrice = $product->harga_produk;
            if (is_string($cleanedPrice)) {
                // Remove 'Rp ' prefix and any non-numeric characters except dots
                $cleanedPrice = preg_replace('/[^0-9.]/', '', str_replace('Rp ', '', $cleanedPrice));
                // Replace dots with nothing (since dots are thousand separators)
                $cleanedPrice = str_replace('.', '', $cleanedPrice);
            }

            // Convert to float
            $harga = is_numeric($cleanedPrice) ? (float) $cleanedPrice : 0;

            return Stat::make(
                label: $product->nama_produk,
                value: number_format($harga, 0, ',', '.') . ' IDR'
            )
                ->description('Produk terbaru')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info');
        })->toArray();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TopProducts');
    }
}
