<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Semua')
                ->query(fn($query) => $query->orderBy('name', 'asc')),
            'Aktif' => Tab::make()
                ->query(fn($query) => $query->where('status', 'aktif')
                    ->orderByRaw("CASE 
                        WHEN status_kepegawaian = 'Tetap' THEN 1 
                        WHEN status_kepegawaian = 'Kontrak' THEN 2
                        WHEN status_kepegawaian = 'Percobaan' THEN 3
                        WHEN status_kepegawaian = 'Magang' THEN 4
                        ELSE 5 END")
                    ->orderBy('created_at', 'desc')),
            'Nonaktif' => Tab::make()
                ->query(fn($query) => $query->where('status', 'nonaktif')
                    ->orderBy('created_at', 'desc')),
            'Pegawai Tetap' => Tab::make()
                ->query(fn($query) => $query->where('status_kepegawaian', 'Tetap')
                    ->orderBy('name', 'asc')),
            'Pegawai Kontrak' => Tab::make()
                ->query(fn($query) => $query->where('status_kepegawaian', 'Kontrak')
                    ->orderBy('name', 'asc')),
            'Pegawai Magang' => Tab::make()
                ->query(fn($query) => $query->where('status_kepegawaian', 'Magang')
                    ->orderBy('name', 'asc')),
        ];
    }
}
