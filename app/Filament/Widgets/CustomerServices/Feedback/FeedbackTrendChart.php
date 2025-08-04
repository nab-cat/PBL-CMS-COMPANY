<?php

namespace App\Filament\Widgets\CustomerServices\Feedback;

use App\Models\Feedback;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FeedbackTrendChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Feedback';
    protected static ?int $sort = 1;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = 'last_6_months';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_FeedbackTrendChart');
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
        $feedbackTrend = $this->getFeedbackTrend($dates, $format);
        $responseTrend = $this->getResponseTrend($dates, $format);

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
                    'name' => 'Total Feedback',
                    'data' => $feedbackTrend,
                ],
                [
                    'name' => 'Feedback Ditanggapi',
                    'data' => $responseTrend,
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
            'colors' => ['#3b82f6', '#10b981'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'markers' => [
                'size' => 4,
            ],
        ];
    }

    private function getFeedbackTrend($dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                // Daily count
                return Feedback::whereDate('created_at', $dateObj)->count();
            } else {
                // Monthly count
                return Feedback::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->count();
            }
        })->toArray();
    }

    private function getResponseTrend($dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                // Daily count
                return Feedback::whereDate('created_at', $dateObj)
                    ->whereNotNull('tanggapan_feedback')
                    ->count();
            } else {
                // Monthly count
                return Feedback::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->whereNotNull('tanggapan_feedback')
                    ->count();
            }
        })->toArray();
    }
}