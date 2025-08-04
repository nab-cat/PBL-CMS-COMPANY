<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimoniLowongan extends EditRecord
{
    protected static string $resource = TestimoniLowonganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
