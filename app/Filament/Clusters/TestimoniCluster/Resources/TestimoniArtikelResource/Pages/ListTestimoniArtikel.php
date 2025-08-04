<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestimoniArtikel extends ListRecords
{
    protected static string $resource = TestimoniArtikelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return $this->getResource()::getTabs();
    }
}
