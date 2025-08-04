<?php

namespace App\Filament\Resources\KategoriUnduhanResource\Pages;

use App\Filament\Resources\KategoriUnduhanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriUnduhans extends ListRecords
{
    protected static string $resource = KategoriUnduhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
