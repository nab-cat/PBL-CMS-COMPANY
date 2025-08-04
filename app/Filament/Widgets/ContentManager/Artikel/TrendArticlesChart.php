<?php

namespace App\Filament\Widgets\ContentManager\Artikel;

use App\Models\Artikel;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TrendArticlesChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Artikel (Views & Publikasi)';
    protected static ?int $sort = 4;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 2;
    protected static ?string $pollingInterval = '120s'; // 2 minutes
    public ?string $filter = 'last_6_months';

    protected function getOptions(): array
    {
        // Get data based on the selected filter
        $filter = $this->filter;
        $dates = collect();

        if ($filter === 'last_3_months') {
            for ($i = 2; $i >= 0; $i--) {
                $dates->push(Carbon::now()->subMonths($i)->format('Y-m'));
            }
        } elseif ($filter === 'last_year') {
            for ($i = 11; $i >= 0; $i--) {
                $dates->push(Carbon::now()->subMonths($i)->format('Y-m'));
            }
        } else {
            // Default: last_6_months
            for ($i = 5; $i >= 0; $i--) {
                $dates->push(Carbon::now()->subMonths($i)->format('Y-m'));
            }
        }

        // Get monthly view trends
        $viewTrends = $this->getMonthlyViewTrends($dates);
        // Get monthly article creation trends
        $creationTrends = $this->getMonthlyCreationTrends($dates);

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
                'stacked' => false,
            ],
            'series' => [
                [
                    'name' => 'Total Views',
                    'data' => $viewTrends,
                ],
                [
                    'name' => 'New Articles',
                    'data' => $creationTrends,
                    'yaxis' => 1, // This series will use the second y-axis
                ],
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
                [
                    // First y-axis for Total Views
                    'seriesName' => 'Total Views',
                    'title' => [
                        // 'text' => 'Total Views',
                        'style' => [
                            'color' => '#3b82f6',
                            'fontFamily' => 'inherit',
                        ],
                    ],
                    'labels' => [
                        'style' => [
                            'fontFamily' => 'inherit',
                            'colors' => '#3b82f6',
                        ],
                    ],
                    'axisBorder' => [
                        'show' => true,
                        'color' => '#3b82f6',
                    ],
                ],
                [
                    // Second y-axis for New Articles
                    'opposite' => true,
                    'seriesName' => 'New Articles',
                    'title' => [
                        // 'text' => 'New Articles',
                        'style' => [
                            'color' => '#22c55e',
                            'fontFamily' => 'inherit',
                        ],
                    ],
                    'labels' => [
                        'style' => [
                            'fontFamily' => 'inherit',
                            'colors' => '#22c55e',
                        ],
                    ],
                    'axisBorder' => [
                        'show' => true,
                        'color' => '#22c55e',
                    ],
                ],
            ],
            'colors' => ['#3b82f6', '#22c55e'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'markers' => [
                'size' => 4,
                'hover' => [
                    'size' => 6,
                ],
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'right',
            ],
            'grid' => [
                'borderColor' => '#e0e0e0',
                'strokeDashArray' => 4,
                'xaxis' => [
                    'lines' => [
                        'show' => true,
                    ],
                ],
            ],
            'tooltip' => [
                'theme' => 'light',
                'shared' => true,
                'intersect' => false,
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TrendArticlesChart');
    }

    private function getMonthlyViewTrends($dates)
    {
        return $dates->map(function ($date) {
            $month = Carbon::createFromFormat('Y-m', $date);

            // Sum the views of all articles created in this month
            return Artikel::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('jumlah_view');
        })->toArray();
    }

    private function getMonthlyCreationTrends($dates)
    {
        return $dates->map(function ($date) {
            $month = Carbon::createFromFormat('Y-m', $date);

            // Count the number of articles created in this month
            return Artikel::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->toArray();
    }

    protected function getFilters(): ?array
    {
        return [
            'last_3_months' => 'Last 3 Months',
            'last_6_months' => 'Last 6 Months',
            'last_year' => 'Last Year',
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
    {
        yaxis: [
            {
                labels: {
                    formatter: function (val) {
                        return val.toLocaleString('id-ID');
                    }
                }
            },
        ]
    }
    JS);
    }
}
