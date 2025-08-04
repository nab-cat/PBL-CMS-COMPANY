<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimoniArtikel extends EditRecord
{
    protected static string $resource = TestimoniArtikelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
