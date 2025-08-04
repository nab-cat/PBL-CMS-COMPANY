<?php

namespace App\Filament\Widgets\ContentManager\General;

use App\Models\Artikel;
use App\Models\CaseStudy;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Produk;
use App\Models\Unduhan;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ContentTrendsChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Pembuatan Konten';
    protected static ?int $sort = 2;
    protected string|int|array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        // layar kecil bakal full, layar medium dan besar bakal 1 kolom
    ];
    protected static bool $deferLoading = true;

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

        $artikelTrend = $this->getContentTrend(Artikel::class, $dates, $format);
        $galeriTrend = $this->getContentTrend(Galeri::class, $dates, $format);
        $eventTrend = $this->getContentTrend(Event::class, $dates, $format);
        $produkTrend = $this->getContentTrend(Produk::class, $dates, $format);
        $caseStudyTrend = $this->getContentTrend(CaseStudy::class, $dates, $format);
        $unduhanTrend = $this->getContentTrend(Unduhan::class, $dates, $format);

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
                    'name' => 'Artikel',
                    'data' => $artikelTrend,
                ],
                [
                    'name' => 'Galeri',
                    'data' => $galeriTrend,
                ],
                [
                    'name' => 'Event',
                    'data' => $eventTrend,
                ],
                [
                    'name' => 'Produk',
                    'data' => $produkTrend,
                ],
                [
                    'name' => 'Case Study',
                    'data' => $caseStudyTrend,
                ],
                [
                    'name' => 'Unduhan',
                    'data' => $unduhanTrend,
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
            'colors' => ['#3b82f6', '#22c55e', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'markers' => [
                'size' => 4,
            ],
        ];
    }

    private function getContentTrend($model, $dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($model, $format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                return $model::whereDate('created_at', $dateObj)->count();
            } else {
                return $model::whereYear('created_at', $dateObj->year)
                    ->whereMonth('created_at', $dateObj->month)
                    ->count();
            }
        })->toArray();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ContentTrendsChart');
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
