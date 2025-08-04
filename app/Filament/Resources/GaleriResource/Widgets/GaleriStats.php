<?php

namespace App\Filament\Resources\GaleriResource\Widgets;

use App\Filament\Resources\GaleriResource\Pages\ListGaleris;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Storage;

class GaleriStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListGaleris::class;
    }

    protected function getStats(): array
    {
        // Calculate total storage size in bytes
        $totalStorage = $this->getPageTableQuery()
            ->get()
            ->flatMap(fn($galeri) => $galeri->thumbnail_galeri ?? [])
            ->sum(fn($path) => Storage::disk('public')->size($path));

        return [
            Stat::make('Total Galeri', $this->getPageTableQuery()->count())
                ->description('Total semua galeri menurut filter')
                ->descriptionIcon('heroicon-s-photo')
                ->color('primary'),

            Stat::make('Total Unduhan', Number::format($this->getPageTableQuery()->sum('jumlah_unduhan')))
                ->description('Total semua unduhan menurut filter')
                ->descriptionIcon('heroicon-s-arrow-down-tray')
                ->color('success'),

            Stat::make('Rata-rata Unduhan', Number::format($this->getPageTableQuery()->avg('jumlah_unduhan') ?? 0, 0))
                ->description('Rata-rata unduhan menurut filter')
                ->descriptionIcon('heroicon-s-arrow-down-tray')
                ->color('warning'),

            Stat::make('Total Ukuran Storage', $this->formatBytes($totalStorage))
                ->description('Total ukuran file menurut filter')
                ->descriptionIcon('heroicon-s-chart-pie')
                ->color('info'),
        ];
    }

    private function formatBytes($bytes): string
    {
        if ($bytes <= 0)
            return '0 Bytes';

        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $decimals = 2;
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
    }
}
