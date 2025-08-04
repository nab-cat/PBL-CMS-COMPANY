<?php

namespace App\Filament\Resources\LamaranResource\Widgets;

use App\Models\Lamaran;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\LamaranResource\Pages\ListLamarans;

class LamaranStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListLamarans::class;
    }

    protected function getStats(): array
    {
        return [
            // Stat yang diminta
            Stat::make('Total Lamaran', $this->getPageTableQuery()->count())
                ->description('Total lamaran menurut filter')
                ->color('primary'),

            Stat::make('Lamaran Diproses', Lamaran::query()
                ->where('status_lamaran', 'Diproses')
                ->count())
                ->description('Lamaran dalam proses seleksi')
                ->color('warning'),

            Stat::make('Lamaran Masuk Bulan Ini', Lamaran::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->description('Jumlah lamaran yang masuk pada bulan ini')
                ->color('info'),

            Stat::make('Lamaran Diterima', Lamaran::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status_lamaran', 'Diterima')
                ->count())
                ->description('Jumlah lamaran yang berhasil diterima bulan ini')
                ->color('success'),
        ];
    }
}
