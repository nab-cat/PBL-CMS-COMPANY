<?php

namespace App\Filament\Widgets\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Support\RawJs;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class StorageUsageChart extends ApexChartWidget
{
    protected static ?string $chartId = 'storageUsageChart';
    protected static ?string $heading = 'Arus Penggunaan Storage';
    protected static ?int $sort = 4;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'full';
    public ?string $filter = 'week';
    protected static ?string $pollingInterval = '900s'; // 15 minutes

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_StorageUsageChart');
    }

    private function calculateStorageSize(): array
    {
        $cacheKey = 'storage_size_data';
        $cacheTime = 3600; // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheTime, function () {
            $publicPath = Storage::disk('public')->path('');
            $fileData = [];

            if (is_dir($publicPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($publicPath, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $date = \Carbon\Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d');
                        if (!isset($fileData[$date])) {
                            $fileData[$date] = 0;
                        }
                        $fileData[$date] += $file->getSize();
                    }
                }
            }

            return $fileData;
        });
    }

    protected function getOptions(): array
    {
        if (!$this->readyToLoad) {
            return [];
        }

        $filter = $this->filter ?? '3_month';


        $days = match ($filter) {
            'week' => 6,
            'month' => 29,
            '3_month' => 89,
            '6_month' => 179,
            default => 89,
        };

        // Get cached storage data
        $cacheKey = "storage_chart_{$filter}";
        $storageData = Cache::remember($cacheKey, 3600, function () use ($days) {
            $fileData = $this->calculateStorageSize();
            $data = collect();

            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $size = isset($fileData[$date]) ? round($fileData[$date] / 1024 / 1024, 2) : 0;

                $data->push([
                    'date' => $date,
                    'size' => $size
                ]);
            }

            return $data;
        });

        $storageData = collect($storageData);

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'zoom' => ['enabled' => true],
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Storage (MB)',
                    'data' => $storageData->pluck('size')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $storageData->pluck('date')->toArray(),
                'type' => 'category',
                'labels' => [
                    'rotate' => -45,
                ],
            ],
            'colors' => ['#0ea5e9'],
            'stroke' => ['curve' => 'smooth'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.2,
                ],
            ],
            'dataLabels' => ['enabled' => false],
        ];

    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    formatter: function (val, timestamp) {
                        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
                        return new Date(val).toLocaleDateString('id-ID', options);
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val.toFixed(2) + ' MB';
                    }
                }
            },
        }
        JS);
    }



    protected function getFilters(): ?array
    {
        return [
            'week' => 'Minggu ini',
            'month' => 'Bulan ini',
            '3_month' => '3 bulan terakhir',
            '6_month' => '6 bulan terakhir',
        ];
    }
}