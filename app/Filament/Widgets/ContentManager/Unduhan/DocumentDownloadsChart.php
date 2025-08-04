<?php

namespace App\Filament\Widgets\ContentManager\Unduhan;

use App\Models\Unduhan;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DocumentDownloadsChart extends ApexChartWidget
{
    protected static ?string $heading = 'Jumlah Unduhan File Dokumen';
    protected static ?int $sort = 17;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        // layar kecil bakal full, layar medium dan besar bakal 1 kolom
    ];
    protected static ?string $pollingInterval = '120s'; // 2 minutes

    protected function getOptions(): array
    {
        $downloads = Unduhan::query()
            ->orderByDesc('jumlah_unduhan')
            ->limit(5)
            ->get();

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
                    'name' => 'Downloads',
                    'data' => $downloads->pluck('jumlah_unduhan')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $downloads->pluck('nama_unduhan')->toArray(),
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
            'colors' => ['#6366f1'],
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
        return auth()->user()?->can('widget_DocumentDownloadsChart');
    }
}
