<?php

namespace App\Filament\Resources\LamaranResource\Pages;

use App\Filament\Resources\LamaranResource;
use App\Services\FileHandlers\SingleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLamaran extends CreateRecord
{
    protected static string $resource = LamaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
