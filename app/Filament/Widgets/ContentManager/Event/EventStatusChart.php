<?php

namespace App\Filament\Widgets\ContentManager\Event;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EventStatusChart extends ApexChartWidget
{
    protected static ?string $heading = 'Event berdasarkan Status';
    protected static ?int $sort = 9;
    protected string|int|array $columnSpan = 2;
    protected static bool $deferLoading = true;

    protected static ?string $pollingInterval = '300s'; // 5 minutes

    protected function getOptions(): array
    {
        // Event model might not use ContentStatus enum like CaseStudy
        // We'll categorize by date instead (upcoming, ongoing, finished)
        $now = Carbon::now();

        $upcoming = Event::where('waktu_start_event', '>', $now)->count();
        $ongoing = Event::where('waktu_start_event', '<=', $now)
            ->where('waktu_end_event', '>=', $now)
            ->count();
        $finished = Event::where('waktu_end_event', '<', $now)->count();
        $archived = Event::onlyTrashed()->count();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$upcoming, $ongoing, $finished, $archived],
            'labels' => ['Akan Datang', 'Sedang Berlangsung', 'Selesai', 'Diarsipkan'],
            'colors' => ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444'],
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
        return auth()->user()?->can('widget_EventStatusChart');
    }
}
