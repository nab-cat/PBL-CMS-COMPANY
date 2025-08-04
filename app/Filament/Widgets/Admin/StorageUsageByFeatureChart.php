<?php

namespace App\Filament\Widgets\Admin;

use Illuminate\Support\Facades\Storage;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Support\RawJs;

class StorageUsageByFeatureChart extends ApexChartWidget
{
    protected static ?string $chartId = 'storageUsageByFeature';
    protected static ?string $heading = 'Penggunaan Storage per Fitur';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '900s'; // 15 minutes

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_StorageUsageByFeatureChart');
    }

    protected function getOptions(): array
    {
        $publicPath = Storage::disk('public')->path('');
        $featureUsage = [
            'Artikel' => ['path' => 'artikel-thumbnails', 'size' => 0],
            'Galeri' => ['path' => 'galeri-thumbnails', 'size' => 0],
            'Case Study' => ['path' => 'case-study-images', 'size' => 0],
            'Produk' => ['path' => 'produk-thumbnails', 'size' => 0],
            'Unduhan' => ['path' => 'unduhan-files', 'size' => 0],
        ];

        if (is_dir($publicPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicPath, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = str_replace($publicPath, '', $file->getPathname());
                    $relativePath = ltrim(str_replace(['\\', '//'], '/', $relativePath), '/');

                    foreach ($featureUsage as $key => $feature) {
                        if (str_starts_with($relativePath, $feature['path'])) {
                            $featureUsage[$key]['size'] += $file->getSize();
                            break;
                        }
                    }
                }
            }
        }

        $labels = [];
        $data = [];

        foreach ($featureUsage as $name => $info) {
            if ($info['size'] > 0) {
                $labels[] = $name;
                $data[] = round($info['size'] / 1024 / 1024, 2); // in MB
            }
        }

        if (empty($data)) {
            $labels = ['Tidak ada data'];
            $data = [1];
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $data,
            'labels' => $labels,
            'stroke' => [
                'show' => true,
                'width' => 2,
            ],
            'legend' => [
                'position' => 'bottom',
                'horizontalAlign' => 'center',
            ],
            'colors' => ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#6366f1'],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toFixed(2) + ' MB';
                    }
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        formatter: function (val, opts) {
                            return Math.round(val) + '%';
                        }
                    }
                }
            }
        }
        JS);
    }


}
