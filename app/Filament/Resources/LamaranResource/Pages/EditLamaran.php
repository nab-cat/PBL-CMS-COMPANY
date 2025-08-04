<?php

namespace App\Filament\Resources\LamaranResource\Pages;

use App\Filament\Resources\LamaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLamaran extends EditRecord
{
    protected static string $resource = LamaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
