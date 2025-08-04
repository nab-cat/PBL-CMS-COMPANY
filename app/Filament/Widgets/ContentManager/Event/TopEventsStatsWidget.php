<?php

namespace App\Filament\Widgets\ContentManager\Event;

use App\Models\Event;
use Filament\Support\RawJs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopEventsStatsWidget extends BaseWidget
{
    protected ?string $heading = 'Widget Event';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '300s'; // 5 minutes
    protected string|int|array $columnSpan = 2;

    protected function getStats(): array
    {
        // Get top 3 events by registrants
        $topEvents = Event::query()
            ->orderByDesc('jumlah_pendaftar')
            ->limit(3)
            ->get(['nama_event', 'jumlah_pendaftar', 'waktu_start_event']);

        $stats = [];

        foreach ($topEvents as $event) {
            $stats[] = Stat::make($event->nama_event, $event->jumlah_pendaftar . ' pendaftar')
                ->description('Mulai: ' . $event->waktu_start_event->format('d M Y'))
                ->color('primary')
                ->chart([
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar,
                    $event->jumlah_pendaftar
                ]);
        }

        // Fill remaining slots if we have fewer than 3 events
        for ($i = count($stats); $i < 3; $i++) {
            $stats[] = Stat::make('No Event', '0 pendaftar')
                ->description('Tidak ada data')
                ->color('gray');
        }

        return $stats;
    }
    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TopEventsStatsWidget');
    }
}
