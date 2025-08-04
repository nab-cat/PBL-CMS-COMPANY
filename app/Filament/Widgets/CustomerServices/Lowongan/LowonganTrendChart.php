<?php

namespace App\Filament\Widgets\CustomerServices\Lowongan;

use App\Models\Lowongan;
use App\Enums\ContentStatus;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class LowonganTrendChart extends ApexChartWidget
{
    protected static ?string $heading = 'Trend Lowongan Aktif';
    protected static ?int $sort = 3;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;
    protected static ?string $pollingInterval = '180s'; // 3 minutes

    public ?string $filter = 'last_6_months';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_LowonganTrendChart');
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
        }        // Get data for trends
        $lowonganAktifTrend = $this->getLowonganAktifTrend($dates, $format);

        return [
            'chart' => [
                'type' => 'area',
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
                    'name' => 'Jumlah Lowongan Aktif',
                    'data' => $lowonganAktifTrend,
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
            'colors' => ['#10b981'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.3,
                ],
            ],
            'markers' => [
                'size' => 5,
                'colors' => ['#10b981'],
                'strokeColors' => '#fff',
                'strokeWidth' => 2,
            ],
        ];
    }
    private function getLowonganAktifTrend($dates, $format = 'Y-m')
    {
        return $dates->map(function ($date) use ($format) {
            $dateObj = Carbon::createFromFormat($format, $date);

            if ($format === 'Y-m-d') {
                // Daily count - menghitung lowongan aktif pada tanggal tertentu
                return Lowongan::where('status_lowongan', ContentStatus::TERPUBLIKASI)
                    ->where('tanggal_dibuka', '<=', $dateObj)
                    ->where('tanggal_ditutup', '>=', $dateObj)
                    ->count();
            } else {
                // Monthly count - menghitung lowongan aktif pada bulan tertentu
                $startOfMonth = $dateObj->copy()->startOfMonth();
                $endOfMonth = $dateObj->copy()->endOfMonth();

                return Lowongan::where('status_lowongan', ContentStatus::TERPUBLIKASI)
                    ->where('tanggal_dibuka', '<=', $endOfMonth)
                    ->where('tanggal_ditutup', '>=', $startOfMonth)
                    ->count();
            }
        })->toArray();
    }
}