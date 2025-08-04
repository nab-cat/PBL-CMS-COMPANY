<?php

namespace App\Filament\Resources\ArtikelResource\Pages;

use App\Filament\Resources\ArtikelResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditArtikel extends EditRecord
{
    protected static string $resource = ArtikelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_artikel');
                }),
        ];
    }

    /**
     * Handle file deletion during update
     */
    public function beforeSave(): void
    {
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_artikel');
    }

    /**
     * Format uploaded files during edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MultipleFileHandler::formatFileData($data, 'thumbnail_artikel');
    }

    /**
     * Define globally searchable attributes
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['judul_artikel', 'slug'];
    }
}