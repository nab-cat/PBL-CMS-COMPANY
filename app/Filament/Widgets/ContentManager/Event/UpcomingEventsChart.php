<?php

namespace App\Filament\Widgets\ContentManager\Event;

use App\Models\Event;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UpcomingEventsChart extends ApexChartWidget
{
    protected static ?string $heading = 'Pendaftar Event yang akan datang';
    protected static ?int $sort = 8;
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
        $upcomingEvents = Event::query()
            ->where('waktu_start_event', '>', now())
            ->orderByDesc('jumlah_pendaftar')
            ->limit(5)
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Pendaftar',
                    'data' => $upcomingEvents->pluck('jumlah_pendaftar')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $upcomingEvents->map(function ($event) {
                    $words = explode(' ', $event->nama_event);
                    return implode(' ', array_slice($words, 0, 2));
                })->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
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
            'colors' => ['#f43f5e'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 1,
                    'horizontal' => false,
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->can('widget_UpcomingEventsChart');
    }
}
