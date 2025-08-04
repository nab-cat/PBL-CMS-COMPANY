<?php

namespace App\Filament\Widgets\ContentManager\Event;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EventTrendsChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Pembuatan Event';
    protected static ?int $sort = 7;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        // layar kecil bakal full, layar medium dan besar bakal 1 kolom
    ];

    protected static ?string $pollingInterval = '300s'; // 5 minutes
    public ?string $filter = 'last_6_months';

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

        $eventTrend = $this->getEventTrend($dates, $format);

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
                    'name' => 'Events',
                    'data' => $eventTrend,
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
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f43f5e'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'markers' => [
                'size' => 4,
            ],
        ];
    }

    private function getEventTrend($dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                return Event::whereDate('created_at', $dateObj)->count();
            } else {
                return Event::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->count();
            }
        })->toArray();
    }
    public static function canView(): bool
    {
        return auth()->user()?->can('widget_EventTrendsChart');
    }

    protected function extraJSOptions(): ?RawJs
    {
        return RawJs::make(<<<JS
        {
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return Math.floor(value);
                    }
                },
                forceNiceScale: true,
                decimalsInFloat: 0
            }
        }
        JS);
    }

    protected function getFilters(): ?array
    {
        return [
            'last_30_days' => 'Last 30 Days',
            'last_3_months' => 'Last 3 Months',
            'last_6_months' => 'Last 6 Months',
            'last_year' => 'Last Year',
        ];
    }
}
