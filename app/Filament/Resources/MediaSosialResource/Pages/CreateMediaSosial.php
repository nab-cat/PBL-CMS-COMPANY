<?php

namespace App\Filament\Resources\MediaSosialResource\Pages;

use App\Filament\Resources\MediaSosialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaSosial extends CreateRecord
{
    protected static string $resource = MediaSosialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
