<?php

namespace App\Filament\Resources\ProfilPerusahaanResource\Pages;

use App\Filament\Resources\ProfilPerusahaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfilPerusahaan extends ViewRecord
{
    protected static string $resource = ProfilPerusahaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Profil')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function getTitle(): string
    {
        return 'Profil Perusahaan';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Anda bisa menambahkan widgets di sini jika diperlukan
        ];
    }
}