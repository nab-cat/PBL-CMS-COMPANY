<?php

namespace App\Filament\Resources\KategoriGaleriResource\Pages;

use App\Filament\Resources\KategoriGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriGaleris extends ListRecords
{
    protected static string $resource = KategoriGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
