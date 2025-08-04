<?php

namespace App\Filament\Resources\UnduhanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UnduhanResource;
use App\Services\FileHandlers\MultipleFileHandler;
use App\Services\FileHandlers\SingleFileHandler;

class EditUnduhan extends EditRecord
{
    protected static string $resource = UnduhanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    SingleFileHandler::deleteFile($record, 'lokasi_file');
                }),
        ];
    }

    /**
     * Handle file deletion during update
     */
    public function beforeSave(): void
    {
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'lokasi_file');
    }

    /**
     * Format uploaded files during edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return SingleFileHandler::formatFileData($data, 'lokasi_file');
    }

    /**
     * Define globally searchable attributes
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_unduhan', 'slug'];
    }
}
