<?php

namespace App\Filament\Resources\ProdukResource\Widgets;

use App\Enums\ContentStatus;
use App\Filament\Resources\ProdukResource\Pages\ListProduks;
use App\Models\Produk;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProdukStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListProduks::class;
    }

    protected function getStats(): array
    {

        return [
            Stat::make('Total Produk', $this->getPageTableQuery()->count())
                ->description('Total produk menurut filter')
                ->descriptionIcon('heroicon-s-shopping-bag')
                ->color('primary'),

            Stat::make('Terpublikasi', Produk::query()->where('status_produk', ContentStatus::TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('Produk yang sudah dipublikasikan')
                ->descriptionIcon("heroicon-s-eye")
                ->color('success'),

            Stat::make('Tidak Terpublikasi', Produk::query()->where('status_produk', ContentStatus::TIDAK_TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('Produk masih sebagai draft')
                ->descriptionIcon("heroicon-s-eye-slash")
                ->color('warning'),

            Stat::make('Diarsipkan', Produk::query()->onlyTrashed()->count())
                ->description('Produk yang diarsipkan')
                ->descriptionIcon("heroicon-s-archive-box")
                ->color('danger'),
        ];
    }
}
