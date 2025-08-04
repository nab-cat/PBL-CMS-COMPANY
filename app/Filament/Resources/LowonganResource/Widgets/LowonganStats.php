<?php

namespace App\Filament\Resources\LowonganResource\Widgets;

use App\Enums\ContentStatus;
use App\Filament\Resources\LowonganResource\Pages\ListLowongans;
use App\Models\Lowongan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LowonganStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListLowongans::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Lowongan', $this->getPageTableQuery()->count())
                ->description('Total lowongan menurut filter')
                ->descriptionIcon('heroicon-s-briefcase')
                ->color('primary'),

            Stat::make('Terpublikasi', Lowongan::query()
                ->where('status_lowongan', ContentStatus::TERPUBLIKASI->value)
                ->whereNull('deleted_at')
                ->count())
                ->description('Lowongan yang dipublikasi')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),


            Stat::make('Periode Dibuka', Lowongan::query()
                ->where('tanggal_dibuka', '<=', now())
                ->where('tanggal_ditutup', '>=', now())
                ->whereNull('deleted_at')
                ->count())
                ->description('Lowongan yang sedang dalam periode dibuka')
                ->descriptionIcon('heroicon-s-envelope-open')
                ->color('info'),
        ];
    }
}
