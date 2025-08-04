<?php

namespace App\Filament\Resources\LowonganResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LowonganResource;
use App\Services\FileHandlers\MultipleFileHandler;

class CreateLowongan extends CreateRecord
{
    protected static string $resource = LowonganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

