<?php

namespace App\Filament\Resources\MitraResource\Pages;

use App\Filament\Resources\MitraResource;
use App\Services\FileHandlers\SingleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMitra extends EditRecord
{
    protected static string $resource = MitraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    SingleFileHandler::deleteFile($record, 'logo');
                    SingleFileHandler::deleteFile($record, 'dok_siup');
                    SingleFileHandler::deleteFile($record, 'dok_npwp');
                }),
        ];
    }

    /**
     * Menangani penghapusan file yang dihapus saat update
     * Method ini dipanggil sebelum validasi sehingga kita bisa membandingkan data
     */
    public function beforeSave(): void
    {
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'logo');
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'dok_siup');
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'dok_npwp');
    }

    /**
     * Menangani file yang diunggah bersama form edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = SingleFileHandler::formatFileData($data, 'logo');
        $data = SingleFileHandler::formatFileData($data, 'dok_siup');
        $data = SingleFileHandler::formatFileData($data, 'dok_npwp');
        return $data;
    }

    /**
     * Menangani jika record dihapus dengan bulk action
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama', 'alamat_mitra'];
    }
}
