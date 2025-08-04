<?php

namespace App\Filament\Widgets\ContentManager\Produk;

use App\Enums\ContentStatus;
use App\Models\Produk;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ProductsByStatusChart extends ApexChartWidget
{
    protected static ?string $heading = 'Products by Status';
    protected static ?int $sort = 15;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;

    protected function getOptions(): array
    {
        $terpublikasi = Produk::query()
            ->where('status_produk', ContentStatus::TERPUBLIKASI)
            ->count();

        $tidakTerpublikasi = Produk::query()
            ->where('status_produk', ContentStatus::TIDAK_TERPUBLIKASI)
            ->count();

        $diarsipkan = Produk::onlyTrashed()->count();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$terpublikasi, $tidakTerpublikasi, $diarsipkan],
            'labels' => ['Terpublikasi', 'Draft', 'Diarsipkan'],
            'colors' => ['#22c55e', '#f59e0b', '#ef4444'],
            'legend' => [
                'position' => 'bottom',
                'fontFamily' => 'inherit',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '50%',
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ProductsByStatusChart');
    }
}
