<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniArtikelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimoniArtikel extends ViewRecord
{
    protected static string $resource = TestimoniArtikelResource::class;

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
