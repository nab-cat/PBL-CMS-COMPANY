<?php

namespace App\Filament\Widgets\ContentManager\Unduhan;

use App\Enums\ContentStatus;
use App\Models\Unduhan;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UnduhanStatusChart extends ApexChartWidget
{
    protected static ?string $heading = 'Downloads by Status';
    protected static ?int $sort = 18;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = [
        'default' => 2,
        'sm' => 2,
        'md' => 1,
        // layar kecil bakal full, layar medium dan besar bakal 1 kolom
    ];
    protected static ?string $pollingInterval = '300s'; // 5 minutes

    protected function getOptions(): array
    {
        $terpublikasi = Unduhan::query()
            ->where('status_unduhan', ContentStatus::TERPUBLIKASI)
            ->count();

        $tidakTerpublikasi = Unduhan::query()
            ->where('status_unduhan', ContentStatus::TIDAK_TERPUBLIKASI)
            ->count();

        $diarsipkan = Unduhan::onlyTrashed()->count();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$terpublikasi, $tidakTerpublikasi, $diarsipkan],
            'labels' => ['Terpublikasi', 'Draft', 'Diarsipkan'],
            'colors' => ['#22c55e', '#f59e0b', '#ef4444'],
            'legend' => [
                'position' => 'bottom',
                'fontFamily' => 'inherit',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '50%',
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_UnduhanStatusChart');
    }
}
