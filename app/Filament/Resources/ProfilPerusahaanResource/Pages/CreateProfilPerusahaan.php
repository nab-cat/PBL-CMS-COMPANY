<?php

namespace App\Filament\Resources\ProfilPerusahaanResource\Pages;

use App\Filament\Resources\ProfilPerusahaanResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProfilPerusahaan extends CreateRecord
{
    protected static string $resource = ProfilPerusahaanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
