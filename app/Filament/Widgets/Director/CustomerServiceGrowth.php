<?php

namespace App\Filament\Widgets\Director;

use App\Models\Feedback;
use App\Models\TestimoniSlider;
use App\Models\Lamaran;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomerServiceGrowth extends ApexChartWidget
{
    protected static ?string $chartId = 'customerServiceGrowth';
    protected static ?string $heading = 'Tren Pertumbuhan data customer service';
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = '6_months';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_CustomerServiceGrowth');
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
        }
        // Ambil cumulative growth untuk setiap tipe layanan pelanggan
        $feedbackGrowth = $this->getContentCumulativeGrowth(Feedback::class, $dates, $format);
        $testimoniGrowth = $this->getContentCumulativeGrowth(TestimoniSlider::class, $dates, $format);
        $lamaranGrowth = $this->getContentCumulativeGrowth(Lamaran::class, $dates, $format);

        // Jika data kosong, isi dengan 1 agar chart tetap tampil
        if (empty(array_filter($feedbackGrowth)))
            $feedbackGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($testimoniGrowth)))
            $testimoniGrowth = array_fill(0, count($dates), 1);
        if (empty(array_filter($lamaranGrowth)))
            $lamaranGrowth = array_fill(0, count($dates), 1);

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
                    'name' => 'Feedback',
                    'data' => $feedbackGrowth,
                ],
                [
                    'name' => 'Testimoni',
                    'data' => $testimoniGrowth,
                ],
                [
                    'name' => 'Lamaran',
                    'data' => $lamaranGrowth,
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
                'tickAmount' => count($dates) > 6 ? 6 : count($dates),
            ],
            'colors' => ['#4ade80', '#f472b6', '#facc15', '#3b82f6', '#a855f7'],
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
            'legend' => [
                'position' => 'top',
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
