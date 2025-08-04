<?php

namespace App\Filament\Resources\KontenSliderResource\Pages;

use App\Filament\Resources\KontenSliderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKontenSlider extends CreateRecord
{
    protected static string $resource = KontenSliderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
