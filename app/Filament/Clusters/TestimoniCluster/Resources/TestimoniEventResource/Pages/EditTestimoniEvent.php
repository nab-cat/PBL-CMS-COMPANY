<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimoniEvent extends EditRecord
{
    protected static string $resource = TestimoniEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
