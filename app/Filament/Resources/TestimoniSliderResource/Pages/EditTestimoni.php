<?php

namespace App\Filament\Resources\TestimoniSliderResource\Pages;

use App\Filament\Resources\TestimoniSliderResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditTestimoni extends EditRecord
{
    protected static string $resource = TestimoniSliderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
