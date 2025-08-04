<?php

namespace App\Filament\Resources\KategoriGaleriResource\Pages;

use App\Filament\Resources\KategoriGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriGaleri extends EditRecord
{
    protected static string $resource = KategoriGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
