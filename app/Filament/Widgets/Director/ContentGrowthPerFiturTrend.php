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

class ContentGrowthPerFiturTrend extends ApexChartWidget
{
    protected static ?string $chartId = 'contentGrowthPerFiturTrend';
    protected static ?string $heading = 'Tren Pertumbuhan Konten Per Fitur';
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = '6_months';
    public ?string $contentType = 'all';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_ContentGrowthPerFiturTrend');
    }

    public function filterContentType(string $type): void
    {
        $this->contentType = $type;
        $this->updateOptions();
    }
    public function mount(): void
    {
        parent::mount();
        $this->contentType = 'all';
    }

    // Tampilkan selector tipe konten
    protected function getFooter(): string
    {
        $options = $this->getContentFilters();

        // Pilih warna untuk setiap tipe konten
        $typeColors = [
            'all' => 'bg-gray-100 hover:bg-gray-200',
            'artikel' => 'bg-green-100 hover:bg-green-200',
            'galeri' => 'bg-pink-100 hover:bg-pink-200',
            'event' => 'bg-yellow-100 hover:bg-yellow-200',
            'produk' => 'bg-blue-100 hover:bg-blue-200',
            'casestudy' => 'bg-purple-100 hover:bg-purple-200',
            'unduhan' => 'bg-red-100 hover:bg-red-200',
        ];

        $html = '<div class="flex flex-col md:flex-row justify-between items-center mt-4 text-xs gap-2">
                    <div class="text-gray-500 font-medium">Filter konten:</div>
                    <div class="flex flex-wrap gap-2">';

        foreach ($options as $key => $label) {
            $activeClass = ($this->contentType === $key)
                ? 'ring-2 ring-primary-500 font-medium ' . $typeColors[$key]
                : $typeColors[$key] . ' text-gray-700';

            $html .= '<button type="button" 
                      wire:click="filterContentType(\'' . $key . '\')" 
                      class="px-3 py-1.5 rounded-md ' . $activeClass . ' transition-all">
                        ' . $label . '
                     </button>';
        }

        $html .= '</div></div>';
        return $html;
    }
    protected function getContentGrowthPerPeriod($modelClass, $dates, $format)
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
        $contentFilter = $this->contentType ?? 'all';
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

        // Get growth data for each content type (non-cumulative)
        $artikelGrowth = $this->getContentGrowthPerPeriod(Artikel::class, $dates, $format);
        $galeriGrowth = $this->getContentGrowthPerPeriod(Galeri::class, $dates, $format);
        $eventGrowth = $this->getContentGrowthPerPeriod(Event::class, $dates, $format);
        $produkGrowth = $this->getContentGrowthPerPeriod(Produk::class, $dates, $format);
        $caseStudyGrowth = $this->getContentGrowthPerPeriod(CaseStudy::class, $dates, $format);
        $unduhanGrowth = $this->getContentGrowthPerPeriod(Unduhan::class, $dates, $format);

        // Prepare series data based on content filter
        $series = [];

        if ($contentFilter === 'all' || $contentFilter === 'artikel') {
            $series[] = [
                'name' => 'Artikel',
                'data' => $artikelGrowth,
            ];
        }

        if ($contentFilter === 'all' || $contentFilter === 'galeri') {
            $series[] = [
                'name' => 'Galeri',
                'data' => $galeriGrowth,
            ];
        }

        if ($contentFilter === 'all' || $contentFilter === 'event') {
            $series[] = [
                'name' => 'Event',
                'data' => $eventGrowth,
            ];
        }

        if ($contentFilter === 'all' || $contentFilter === 'produk') {
            $series[] = [
                'name' => 'Produk',
                'data' => $produkGrowth,
            ];
        }

        if ($contentFilter === 'all' || $contentFilter === 'casestudy') {
            $series[] = [
                'name' => 'Case Study',
                'data' => $caseStudyGrowth,
            ];
        }

        if ($contentFilter === 'all' || $contentFilter === 'unduhan') {
            $series[] = [
                'name' => 'Unduhan',
                'data' => $unduhanGrowth,
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
            'colors' => ['#4ade80', '#f472b6', '#facc15', '#3b82f6', '#a855f7', '#ef4444'],
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
                    text: 'Jumlah Konten Baru'
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
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    columnWidth: '60%'
                }
            }
        }
        JS);
    }    // Definisikan properti untuk filter
    protected function getTimeFilters(): array
    {
        return [
            '30_days' => 'Periode 30 Hari',
            '3_months' => 'Periode 3 Bulan',
            '6_months' => 'Periode 6 Bulan',
            '1_year' => 'Periode 1 Tahun',
        ];
    }

    protected function getContentFilters(): array
    {
        return [
            'all' => 'Semua Konten',
            'artikel' => 'Artikel',
            'galeri' => 'Galeri',
            'event' => 'Event',
            'produk' => 'Produk',
            'casestudy' => 'Case Study',
            'unduhan' => 'Unduhan',
        ];
    }

    // Gunakan filter tunggal dan buat dua selektor terpisah
    protected function getFilters(): ?array
    {
        return $this->getTimeFilters();
    }
}