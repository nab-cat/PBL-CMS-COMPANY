<?php

namespace App\Filament\Resources\EventResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\EventResource\Widgets\EventStats;

class ListEvents extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Event')
                ->icon('heroicon-o-calendar'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EventStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->orderBy('waktu_start_event', 'asc')),
            'Akan Datang' => Tab::make()
                ->icon('heroicon-o-clock')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('waktu_start_event', '>', now())
                    ->orderBy('waktu_start_event', 'asc')),
            'Sedang Berlangsung' => Tab::make()
                ->icon('heroicon-o-play')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('waktu_start_event', '<=', now())
                    ->where('waktu_end_event', '>=', now())
                    ->orderBy('waktu_start_event', 'asc')),
            'Selesai' => Tab::make()
                ->icon('heroicon-o-check-badge')
                ->query(fn($query) => $query->whereNull('deleted_at')
                    ->where('waktu_end_event', '<', now())
                    ->orderBy('waktu_start_event', 'desc')),
            'Diarsipkan' => Tab::make()
                ->icon('heroicon-o-archive-box')
                ->query(fn($query) => $query->onlyTrashed()),
        ];
    }
}
