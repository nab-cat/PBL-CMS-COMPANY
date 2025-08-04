<?php

namespace App\Filament\Resources\ArtikelResource\Widgets;

use App\Models\Artikel;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\ArtikelResource\Pages\ListArtikels;

class ArtikelStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListArtikels::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Artikel', $this->getPageTableQuery()->count())
                ->description('Jumlah artikel menurut filter')
                ->descriptionIcon('heroicon-s-document-text')
                ->color('primary'),
            Stat::make('Total View', Number::format($this->getPageTableQuery()->sum('jumlah_view')))
                ->description('Total view menurut filter')
                ->descriptionIcon('heroicon-s-eye')
                ->color('success'),
            Stat::make('Rata-rata View', number_format($this->getPageTableQuery()->avg('jumlah_view'), 0))
                ->description('Rata-rata view menurut filter')
                ->descriptionIcon('heroicon-s-eye')
                ->color('warning'),
            Stat::make('Jumlah Artikel Bulan Ini', Artikel::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count())
                ->description('Artikel yang dibuat bulan ini')
                ->descriptionIcon('heroicon-s-document-text')
                ->color('info'),
        ];
    }
}
