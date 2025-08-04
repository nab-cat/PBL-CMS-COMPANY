<?php

namespace App\Filament\Resources\LamaranResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LamaranResource;
use App\Filament\Resources\LamaranResource\Widgets\LamaranStats;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListLamarans extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = LamaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LamaranStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->query(fn($query) => $query
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),
            'Diproses' => Tab::make()
                ->query(fn($query) => $query
                    ->whereNull('deleted_at')
                    ->where('status_lamaran', 'Diproses')
                    ->orderBy('created_at', 'desc')),
            'Diterima' => Tab::make()
                ->query(fn($query) => $query->where('status_lamaran', 'Diterima')
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),
            'Ditolak' => Tab::make()
                ->query(fn($query) => $query->where('status_lamaran', 'Ditolak')
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')),
            'Diarsipkan' => Tab::make()
                ->query(fn($query) => $query->onlyTrashed()),
        ];
    }
}
