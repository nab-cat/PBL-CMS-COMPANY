<?php

namespace App\Filament\Widgets\Director;

use App\Models\Artikel;
use App\Models\CaseStudy;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Produk;
use App\Models\Unduhan;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ContentGrowthTrend extends ApexChartWidget
{
    protected static ?string $chartId = 'contentGrowthTrend';
    protected static ?string $heading = 'Tren Pertumbuhan Konten';
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '300s'; // 3 minutes

    public ?string $filter = '6_months';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ContentGrowthTrend');
    }

    protected function getContentCumulativeGrowth($modelClass, $dates, $format)
    {
        $result = [];
        $total = 0;

        foreach ($dates as $date) {
            try {
                if ($format === 'Y-m-d') {
                    // Daily format
                    $count = $modelClass::whereDate('created_at', $date)->count();
                } else {
                    // Monthly format
                    $month = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
                    $count = $modelClass::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
                }

                $total += $count;
                $result[] = (int) $total; // Pastikan nilai integer
            } catch (\Exception $e) {
                // Jika terjadi error, tambahkan nilai sebelumnya (atau 0 jika belum ada nilai)
                $result[] = empty($result) ? 0 : end($result);
            }
        }

        return $result;
    }

    protected function getOptions(): array
    {
        if (!$this->readyToLoad) {
            return [];
        }

        $filter = $this->filter ?? '6_months';
        $dates = collect();
        $format = '';

        switch ($filter) {
            case '30_days':
                $format = 'Y-m-d';
                for ($i = 29; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subDays($i)->format($format));
                }
                break;
            case '3_months':
                $format = 'Y-m';
                for ($i = 2; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
                break;
            case '1_year':
                $format = 'Y-m';
                for ($i = 11; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
                break;
            case '6_months':
            default:
                $format = 'Y-m';
                for ($i = 5; $i >= 0; $i--) {
                    $dates->push(Carbon::now()->subMonths($i)->format($format));
                }
                break;
        }        // Get cumulative growth for each content type
        $artikelGrowth = $this->getContentCumulativeGrowth(Artikel::class, $dates, $format);
        $galeriGrowth = $this->getContentCumulativeGrowth(Galeri::class, $dates, $format);
        $eventGrowth = $this->getContentCumulativeGrowth(Event::class, $dates, $format);
        $produkGrowth = $this->getContentCumulativeGrowth(Produk::class, $dates, $format);
        $caseStudyGrowth = $this->getContentCumulativeGrowth(CaseStudy::class, $dates, $format);
        $unduhanGrowth = $this->getContentCumulativeGrowth(Unduhan::class, $dates, $format);

        // Debugging - Tambahkan data minimal jika kosong
        if (empty(array_filter($artikelGrowth)))
            $artikelGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($galeriGrowth)))
            $galeriGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($eventGrowth)))
            $eventGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($produkGrowth)))
            $produkGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($caseStudyGrowth)))
            $caseStudyGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($unduhanGrowth)))
            $unduhanGrowth = array_fill(0, count($dates), 1);
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'stacked' => false,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Artikel',
                    'data' => $artikelGrowth,
                ],
                [
                    'name' => 'Galeri',
                    'data' => $galeriGrowth,
                ],
                [
                    'name' => 'Event',
                    'data' => $eventGrowth,
                ],
                [
                    'name' => 'Produk',
                    'data' => $produkGrowth,
                ],
                [
                    'name' => 'Case Study',
                    'data' => $caseStudyGrowth,
                ],
                [
                    'name' => 'Unduhan',
                    'data' => $unduhanGrowth,
                ],
            ],
            'xaxis' => [
                'categories' => $dates->toArray(),
                'labels' => [
                    'rotate' => -45,
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
                'type' => 'category',
                'tickAmount' => 6,
            ],
            'colors' => ['#4ade80', '#f472b6', '#facc15', '#3b82f6', '#a855f7', '#ef4444'],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.2,
                ],
            ],
            'dataLabels' => ['enabled' => false],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'yaxis' => [
                'min' => 0,
                'forceNiceScale' => true,
            ],
        ];
    }
    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            xaxis: {
                labels: {
                    rotate: -45,
                    formatter: function (val) {
                        if (typeof val === 'string' && val.includes('-')) {
                            try {
                                if (val.split('-').length > 2) {
                                    // Daily format - Menggunakan format yang lebih sederhana
                                    const parts = val.split('-');
                                    return parts[2] + '/' + parts[1];
                                } else {
                                    // Monthly format
                                    const parts = val.split('-');
                                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                                    return monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0];
                                }
                            } catch(e) {
                                return val;
                            }
                        }
                        return val;
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return Math.floor(val);
                    }
                }
            },
            legend: {
                position: 'top'
            }
        }
        JS);
    }

    protected function getFilters(): ?array
    {
        return [
            '30_days' => 'Periode 30 Hari',
            '3_months' => 'Periode 3 Bulan',
            '6_months' => 'Periode 6 Bulan',
            '1_year' => 'Periode 1 Tahun',
        ];
    }
}