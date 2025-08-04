<?php

namespace App\Filament\Widgets\ContentManager\Unduhan;

use App\Models\Unduhan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopDownloads extends BaseWidget
{
    protected ?string $heading = 'Widget Unduhan';
    protected static ?int $sort = 16;
    protected string|int|array $columnSpan = 2;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $topDownloads = Unduhan::query()
            ->orderByDesc('jumlah_unduhan')
            ->limit(value: 3)
            ->get();

        return $topDownloads->map(function ($unduhan) {
            return Stat::make(
                label: $unduhan->nama_unduhan,
                value: number_format($unduhan->jumlah_unduhan) . ' downloads'
            )
                ->description('File paling banyak diunduh')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success');
        })->toArray();
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_TopDownloads');
    }
}
