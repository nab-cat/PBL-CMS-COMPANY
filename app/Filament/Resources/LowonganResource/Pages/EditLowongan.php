<?php

namespace App\Filament\Resources\LowonganResource\Pages;

use App\Filament\Resources\LowonganResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLowongan extends EditRecord
{
    protected static string $resource = LowonganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_lowongan');
                }),
        ];
    }

    /**
     * Handle file deletion during update
     */
    public function beforeSave(): void
    {
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_lowongan');
    }

    /**
     * Format uploaded files during edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MultipleFileHandler::formatFileData($data, 'thumbnail_lowongan');
    }

    /**
     * Define globally searchable attributes
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['judul_lowongan'];
    }
}
