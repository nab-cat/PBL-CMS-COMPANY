<?php

namespace App\Filament\Widgets\ContentManager\CaseStudy;

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CaseStudyStatusChart extends ApexChartWidget
{
    protected static ?string $heading = 'Case Study berdasarkan status';
    protected static ?int $sort = 5;
    protected static bool $deferLoading = true;
    protected string|int|array $columnSpan = 2;
    protected static ?string $pollingInterval = '300s'; // 5 minutes

    protected function getOptions(): array
    {
        $terpublikasi = CaseStudy::query()
            ->where('status_case_study', ContentStatus::TERPUBLIKASI)
            ->count();

        $tidakTerpublikasi = CaseStudy::query()
            ->where('status_case_study', ContentStatus::TIDAK_TERPUBLIKASI)
            ->count();

        $diarsipkan = CaseStudy::onlyTrashed()->count();

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
        return auth()->user()?->can('widget_CaseStudyStatusChart');
    }
}
