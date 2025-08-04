<?php

namespace App\Filament\Widgets\ContentManager\General;

use App\Models\Artikel;
use App\Models\CaseStudy;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Produk;
use App\Models\Unduhan;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ContentCountsChart extends ApexChartWidget
{
    protected static ?string $heading = 'Jumlah Konten per Fitur';
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    protected string|int|array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        // layar kecil bakal full, layar medium dan besar bakal 1 kolom
    ];

    protected static bool $deferLoading = true;
    protected function getOptions(): array
    {
        $features = [
            'Artikel' => Artikel::count(),
            'Case-Study' => CaseStudy::count(),
            'Event' => Event::count(),
            'Galeri' => Galeri::count(),
            'Produk' => Produk::count(),
            'Unduhan' => Unduhan::count(),
        ];

        // Sort features by count in descending order
        arsort($features);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Jumlah',
                    'data' => array_values($features),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($features),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#3b82f6'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ContentCountsChart');
    }
}
