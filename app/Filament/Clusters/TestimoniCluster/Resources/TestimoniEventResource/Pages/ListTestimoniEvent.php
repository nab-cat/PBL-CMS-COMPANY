<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestimoniEvent extends ListRecords
{
    protected static string $resource = TestimoniEventResource::class;

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
