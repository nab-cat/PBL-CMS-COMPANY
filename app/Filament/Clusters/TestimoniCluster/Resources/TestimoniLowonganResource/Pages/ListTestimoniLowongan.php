<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestimoniLowongan extends ListRecords
{
    protected static string $resource = TestimoniLowonganResource::class;

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
