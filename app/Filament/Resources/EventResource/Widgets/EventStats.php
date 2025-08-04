<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\EventResource\Pages\ListEvents;

class EventStats extends BaseWidget
{
    use InteractsWithPageTable;
    protected static ?string $pollingInterval = '15s';

    protected function getTablePage(): string
    {
        return ListEvents::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Event', $this->getPageTableQuery()->count())
                ->description('Total semua event menurut filter')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Event Aktif', Event::query()
                ->where('waktu_start_event', '<=', now())
                ->where('waktu_end_event', '>=', now())
                ->count())
                ->description('Event yang sedang berlangsung')
                ->descriptionIcon('heroicon-o-play')
                ->color('success'),

            Stat::make('Event Akan Datang', Event::query()
                ->where('waktu_start_event', '>', now())
                ->count())
                ->description('Event yang belum dimulai')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Rata-rata Pendaftar', Number::format((float) $this->getPageTableQuery()->avg('jumlah_pendaftar'), 0))
                ->description('Rata-rata pendaftar per event menurut filter')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('info'),
        ];
    }
}
