<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_event');
                }),
        ];
    }

    /**
     * Menangani penghapusan file yang dihapus saat update
     * Method ini dipanggil sebelum validasi sehingga kita bisa membandingkan data
     */
    public function beforeSave(): void
    {
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_event');
    }

    /**
     * Menangani file yang diunggah bersama form edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MultipleFileHandler::formatFileData($data, 'thumbnail_event');
    }

    /**
     * Menangani jika record dihapus dengan bulk action
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_event', 'slug'];
    }
}
