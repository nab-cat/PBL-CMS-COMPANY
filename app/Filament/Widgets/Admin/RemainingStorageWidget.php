<?php

namespace App\Filament\Widgets\Admin;

use Filament\Support\RawJs;
use Illuminate\Support\Facades\Storage;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RemainingStorageWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'remainingStorage';
    protected static ?string $heading = 'Sisa Storage';
    protected static ?int $sort = 5;
    protected static bool $deferLoading = true;

    protected static ?string $pollingInterval = '900s'; // 15 minutes

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_RemainingStorageWidget');
    }

    protected function getDiskSpace(): array
    {
        $path = dirname(Storage::disk('public')->path(''));

        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;

        return [
            'total' => $total,
            'free' => $free,
            'used' => $used,
        ];
    }

    protected function formatSize(float $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, 2) . ' ' . $units[$index];
    }

    protected function getOptions(): array
    {
        $space = $this->getDiskSpace();

        $usedGB = $space['used'] / 1_073_741_824;
        $freeGB = $space['free'] / 1_073_741_824;

        $usedPct = round(($space['used'] / $space['total']) * 100, 2);
        $freePct = 100 - $usedPct;

        $formattedTotal = $this->formatSize($space['total']);

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => [$usedGB, $freeGB],
            'labels' => ['Terpakai', 'Tersisa'],
            'legend' => [
                'position' => 'bottom',
                'horizontalAlign' => 'center',
            ],
            'colors' => ['#ef4444', '#22c55e'],
            'stroke' => ['show' => false],
            'dataLabels' => ['enabled' => true],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        $formattedTotal = $this->formatSize($this->getDiskSpace()['total']);

        return new RawJs(<<<JS
    {
        plotOptions: {
            pie: {
                dataLabels: {
                    formatter: function (val, opts) {
                        return Math.round(val) + '%';
                    }
                },
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                        },
                        value: {
                            show: true,
                            formatter: function (val) {
                                return val.toFixed(2) + ' GB';
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Storage',
                            formatter: function () {
                                return '{$formattedTotal}';
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2) + ' GB';
                }
            }
        }
    }
    JS);
    }

}
