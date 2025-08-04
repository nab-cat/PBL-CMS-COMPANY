<?php

namespace App\Filament\Widgets\CustomerServices\Lamaran;

use App\Models\Lamaran;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class LamaranTrendChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Lamaran';
    protected static ?int $sort = 2;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = 'last_6_months';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_LamaranTrendChart');
    }

    protected function getFilters(): ?array
    {
        return [
            'last_30_days' => '30 Hari Terakhir',
            'last_3_months' => '3 Bulan Terakhir',
            'last_6_months' => '6 Bulan Terakhir',
            'last_year' => '1 Tahun Terakhir',
        ];
    }

    protected function getOptions(): array
    {
        $filter = $this->filter ?? 'last_6_months';
        $dates = collect();
        $format = '';

        switch ($filter) {
            case 'last_30_days':
                $format = 'Y-m-d';
                for ($i = 29; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subDays($i)->format($format));
                }
                break;
            case 'last_3_months':
                $format = 'Y-m';
                for ($i = 2; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
                break;
            case 'last_year':
                $format = 'Y-m';
                for ($i = 11; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
                break;
            case 'last_6_months':
            default:
                $format = 'Y-m';
                for ($i = 5; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
        }

        // Get data for trends
        $totalLamaranTrend = $this->getTotalLamaranTrend($dates, $format);
        $lamaranDiterimaTrend = $this->getStatusLamaranTrend($dates, $format, 'Diterima');
        $lamaranDitolakTrend = $this->getStatusLamaranTrend($dates, $format, 'Ditolak');

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'zoom' => [
                    'enabled' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Total Lamaran',
                    'data' => $totalLamaranTrend,
                ],
                [
                    'name' => 'Lamaran Diterima',
                    'data' => $lamaranDiterimaTrend,
                ],
                [
                    'name' => 'Lamaran Ditolak',
                    'data' => $lamaranDitolakTrend,
                ]
            ],
            'xaxis' => [
                'categories' => $dates->toArray(),
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
            'colors' => ['#3b82f6', '#10b981', '#f43f5e'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'markers' => [
                'size' => 4,
            ],
        ];
    }

    private function getTotalLamaranTrend($dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                // Daily count
                return Lamaran::whereDate('created_at', $dateObj)->count();
            } else {
                // Monthly count
                return Lamaran::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->count();
            }
        })->toArray();
    }

    private function getStatusLamaranTrend($dates, $format = 'Y-m', $status = 'Diterima')
    {
        return $dates->map(function ($date) use ($format, $status) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                // Daily count
                return Lamaran::whereDate('created_at', $dateObj)
                    ->where('status_lamaran', $status)
                    ->count();
            } else {
                // Monthly count
                return Lamaran::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->where('status_lamaran', $status)
                    ->count();
            }
        })->toArray();
    }
}