<?php

namespace App\Filament\Resources\MitraResource\Pages;

use App\Filament\Resources\MitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListMitras extends ListRecords
{
    protected static string $resource = MitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Mitra Kerjasama')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->query(fn($query) => $query->orderBy('nama', 'asc')),
            'Aktif' => Tab::make()
                ->icon('heroicon-o-check-circle')
                ->query(fn($query) => $query->where('status', 'aktif')
                    ->orderBy('tanggal_kemitraan', 'desc')),
            'Nonaktif' => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->query(fn($query) => $query->where('status', 'nonaktif')
                    ->orderBy('tanggal_kemitraan', 'desc')),
        ];
    }
}
