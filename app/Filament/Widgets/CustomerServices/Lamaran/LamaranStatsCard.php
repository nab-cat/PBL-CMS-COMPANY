<?php

namespace App\Filament\Widgets\CustomerServices\Lamaran;

use App\Models\Lamaran;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LamaranStatsCard extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Lamaran';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_LamaranStatsCard');
    }
    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return [
            Stat::make('Total Lamaran', Lamaran::query()->count())
                ->description('Jumlah keseluruhan lamaran')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Menunggu Review', Lamaran::query()->where('status_lamaran', 'Diproses')->count())
                ->description('Total lamaran yang belum direview')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Lamaran Diterima (Bulan Ini)', Lamaran::query()
                ->where('status_lamaran', 'Diterima')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->count())
                ->description('Lamaran yang diterima bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Lamaran Ditolak (Bulan Ini)', Lamaran::query()
                ->where('status_lamaran', 'Ditolak')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->count())
                ->description('Lamaran yang ditolak bulan ini')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
