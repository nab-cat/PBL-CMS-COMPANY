<?php

namespace App\Filament\Resources\KategoriUnduhanResource\Pages;

use App\Filament\Resources\KategoriUnduhanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriUnduhan extends EditRecord
{
    protected static string $resource = KategoriUnduhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
