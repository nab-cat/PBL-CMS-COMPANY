<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestimoniProduk extends ListRecords
{
    protected static string $resource = TestimoniProdukResource::class;

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
