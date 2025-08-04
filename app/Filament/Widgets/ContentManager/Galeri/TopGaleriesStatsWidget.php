<?php

namespace App\Filament\Widgets\ContentManager\Galeri;

use App\Models\Galeri;
use Filament\Support\RawJs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopGaleriesStatsWidget extends BaseWidget
{
    protected ?string $heading = 'Widget Galeri';
    protected static ?int $sort = 10;
    protected static ?string $pollingInterval = '300s'; // 5 minutes
    protected string|int|array $columnSpan = 2;

    protected function getStats(): array
    {
        // Get top 3 galleries by downloads (since there's no view count)
        $topGaleries = Galeri::query()
            ->orderByDesc('jumlah_unduhan')
            ->limit(3)
            ->get(['judul_galeri', 'jumlah_unduhan', 'created_at']);

        $stats = [];

        foreach ($topGaleries as $galeri) {
            $stats[] = Stat::make($galeri->judul_galeri, $galeri->jumlah_unduhan . ' unduhan')
                ->description('Created: ' . $galeri->created_at->format('d M Y'))
                ->color('primary')
                ->chart([
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan,
                    $galeri->jumlah_unduhan
                ]);
        }

        // Fill remaining slots if we have fewer than 3 galleries
        for ($i = count($stats); $i < 3; $i++) {
            $stats[] = Stat::make('No Gallery', '0 unduhan')
                ->description('Tidak ada data')
                ->color('gray');
        }

        return $stats;
    }
    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TopGaleriesStatsWidget');
    }
}
