<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimoniProduk extends EditRecord
{
    protected static string $resource = TestimoniProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
