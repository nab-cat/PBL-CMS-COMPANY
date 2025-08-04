<?php

namespace App\Filament\Resources\FeatureToggleResource\Pages;

use App\Filament\Resources\FeatureToggleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeatureToggle extends CreateRecord
{
    protected static string $resource = FeatureToggleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
