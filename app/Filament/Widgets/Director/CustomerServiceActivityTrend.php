<?php

namespace App\Filament\Widgets\Director;

use App\Models\Feedback;
use App\Models\Lamaran;
use App\Models\TestimoniSlider;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CustomerServiceActivityTrend extends ApexChartWidget
{
    protected static ?string $chartId = 'customerServiceActivityTrend';
    protected static ?string $heading = 'Tren Data Customer Service';
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = '6_months';
    public ?string $activityType = 'all';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_CustomerServiceActivityTrend');
    }

    public function filterActivityType(string $type): void
    {
        $this->activityType = $type;
        $this->updateOptions();
    }

    public function mount(): void
    {
        parent::mount();
    }

    // Tampilkan selector tipe aktivitas
    protected function getFooter(): string
    {
        $options = $this->getActivityFilters();

        // Pilih warna untuk setiap tipe aktivitas
        $typeColors = [
            'all' => 'bg-gray-100 hover:bg-gray-200',
            'feedback' => 'bg-blue-100 hover:bg-blue-200',
            'lamaran' => 'bg-green-100 hover:bg-green-200',
            'testimoni' => 'bg-purple-100 hover:bg-purple-200',
        ];

        $html = '<div class="flex flex-col md:flex-row justify-between items-center mt-4 text-xs gap-2">
                    <div class="text-gray-500 font-medium">Filter aktivitas:</div>
                    <div class="flex flex-wrap gap-2">';

        foreach ($options as $key => $label) {
            $activeClass = ($this->activityType === $key)
                ? 'ring-2 ring-primary-500 font-medium ' . $typeColors[$key]
                : $typeColors[$key] . ' text-gray-700';

            $html .= '<button type="button" 
                      wire:click="filterActivityType(\'' . $key . '\')" 
                      class="px-3 py-1.5 rounded-md ' . $activeClass . ' transition-all">
                        ' . $label . '
                     </button>';
        }

        $html .= '</div></div>';
        return $html;
    }

    protected function getActivityGrowthPerPeriod($modelClass, $dates, $format)
    {
        $result = [];

        foreach ($dates as $date) {
            try {
                if ($format === 'Y-m-d') {
                    // Daily format - data untuk hari tertentu
                    $count = $modelClass::whereDate('created_at', $date)->count();
                } else {
                    // Monthly format - data untuk bulan tertentu
                    $month = Carbon::createFromFormat('Y-m', $date)->startOfMonth();
                    $count = $modelClass::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count();
                }

                $result[] = (int) $count;
            } catch (\Exception $e) {
                $result[] = 0;
            }
        }

        return $result;
    }

    protected function getOptions(): array
    {
        if (!$this->readyToLoad) {
            return [];
        }

        $timeFilter = $this->filter ?? '6_months';
        $activityFilter = $this->activityType ?? 'all';
        $dates = collect();
        $format = '';

        // Generate dates based on time filter
        switch ($timeFilter) {
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

        // Get growth data for each activity type (non-cumulative)
        $feedbackGrowth = $this->getActivityGrowthPerPeriod(Feedback::class, $dates, $format);
        $lamaranGrowth = $this->getActivityGrowthPerPeriod(Lamaran::class, $dates, $format);
        $testimoniGrowth = $this->getActivityGrowthPerPeriod(TestimoniSlider::class, $dates, $format);

        // Prepare series data based on activity filter
        $series = [];

        if ($activityFilter === 'all' || $activityFilter === 'feedback') {
            $series[] = [
                'name' => 'Feedback',
                'data' => $feedbackGrowth,
            ];
        }

        if ($activityFilter === 'all' || $activityFilter === 'lamaran') {
            $series[] = [
                'name' => 'Lamaran',
                'data' => $lamaranGrowth,
            ];
        }

        if ($activityFilter === 'all' || $activityFilter === 'testimoni') {
            $series[] = [
                'name' => 'Testimoni',
                'data' => $testimoniGrowth,
            ];
        }
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'stacked' => false,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => $series,
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
            'colors' => ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
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
                },
                title: {
                    text: 'Jumlah Aktivitas'
                }
            },
            legend: {
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' item';
                    }
                }            },
                
                plotOptions: {
                line: {
                    curve: 'smooth'
                }
            },
            chart: {
                toolbar: {
                    show: false
                },
            }
        }
        JS);
    }

    // Definisikan properti untuk filter periode
    protected function getTimeFilters(): array
    {
        return [
            '30_days' => 'Periode 30 Hari',
            '3_months' => 'Periode 3 Bulan',
            '6_months' => 'Periode 6 Bulan',
            '1_year' => 'Periode 1 Tahun',
        ];
    }

    // Definisikan properti untuk filter aktivitas
    protected function getActivityFilters(): array
    {
        return [
            'all' => 'Semua Aktivitas',
            'feedback' => 'Feedback',
            'lamaran' => 'Lamaran',
            'testimoni' => 'Testimoni',
        ];
    }

    // Gunakan filter tunggal untuk periode waktu
    protected function getFilters(): ?array
    {
        return $this->getTimeFilters();
    }
}
