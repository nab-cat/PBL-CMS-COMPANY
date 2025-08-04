<?php

namespace App\Filament\Widgets\CustomerServices\Lowongan;

use App\Models\Lowongan;
use App\Enums\ContentStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LowonganStatsCard extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Lowongan';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->can('widget_LowonganStatsCard');
    }
    protected function getStats(): array
    {
        $now = now();

        return [
            Stat::make('Total Lowongan', Lowongan::query()->count())
                ->description('Jumlah keseluruhan lowongan')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('Lowongan Aktif', Lowongan::query()
                ->where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->where('tanggal_dibuka', '<=', $now)
                ->where('tanggal_ditutup', '>=', $now)
                ->count())
                ->description('Lowongan yang masih terbuka')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Lowongan Tidak Terpublikasi', Lowongan::query()
                ->where('status_lowongan', ContentStatus::TIDAK_TERPUBLIKASI)
                ->count())
                ->description('Lowongan yang belum dipublikasi')
                ->descriptionIcon('heroicon-m-document')
                ->color('gray'),
        ];
    }
}
