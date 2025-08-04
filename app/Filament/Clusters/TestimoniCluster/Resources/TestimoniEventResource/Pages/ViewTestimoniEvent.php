<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimoniEvent extends ViewRecord
{
    protected static string $resource = TestimoniEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => static::getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
}
