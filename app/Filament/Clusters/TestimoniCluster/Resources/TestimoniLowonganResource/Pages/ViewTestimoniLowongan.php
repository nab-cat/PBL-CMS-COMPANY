<?php

namespace App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource\Pages;

use App\Filament\Clusters\TestimoniCluster\Resources\TestimoniLowonganResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimoniLowongan extends ViewRecord
{
    protected static string $resource = TestimoniLowonganResource::class;

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
