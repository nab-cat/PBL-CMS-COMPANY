<?php

namespace App\Filament\Widgets\ContentManager\Galeri;

use App\Enums\ContentStatus;
use App\Models\Galeri;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class GaleriStatusChart extends ApexChartWidget
{
    protected static ?string $heading = 'Gallery Items by Status';
    protected static ?int $sort = 13;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;
    protected static ?string $pollingInterval = '300s'; // 5 minutes

    protected function getOptions(): array
    {
        $terpublikasi = Galeri::query()
            ->where('status_galeri', ContentStatus::TERPUBLIKASI)
            ->count();

        $tidakTerpublikasi = Galeri::query()
            ->where('status_galeri', ContentStatus::TIDAK_TERPUBLIKASI)
            ->count();

        $diarsipkan = Galeri::onlyTrashed()->count();

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
        return auth()->user()?->can('widget_GaleriStatusChart');
    }
}
