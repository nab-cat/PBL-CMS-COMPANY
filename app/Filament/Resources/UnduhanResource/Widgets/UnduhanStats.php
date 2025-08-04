<?php

namespace App\Filament\Resources\UnduhanResource\Widgets;

use App\Enums\ContentStatus;
use App\Models\Unduhan;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\UnduhanResource\Pages\ListUnduhans;

class UnduhanStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListUnduhans::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Terpublikasi', Unduhan::query()->where('status_unduhan', ContentStatus::TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('File yang sudah dipublikasikan')
                ->descriptionIcon('heroicon-s-eye')
                ->color('success'),

            Stat::make('Tidak Terpublikasi', Unduhan::query()->where('status_unduhan', ContentStatus::TIDAK_TERPUBLIKASI)->whereNull('deleted_at')->count())
                ->description('File masih sebagai draft')
                ->descriptionIcon('heroicon-s-eye-slash')
                ->color('warning'),

            Stat::make('Total Download', Number::format(Unduhan::query()->sum('jumlah_unduhan')))
                ->description('Total file yang diunduh')
                ->descriptionIcon('heroicon-s-arrow-down-tray')
                ->color('primary'),

            Stat::make('Unduhan Bulan Ini', Unduhan::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->description('File yang ditambahkan bulan ini')
                ->descriptionIcon('heroicon-s-arrow-up-tray')
                ->color('info'),
        ];
    }
}
