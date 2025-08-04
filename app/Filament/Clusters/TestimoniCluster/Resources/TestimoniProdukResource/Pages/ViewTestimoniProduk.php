<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimoniProduk extends ViewRecord
{
    protected static string $resource = TestimoniProdukResource::class;

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
