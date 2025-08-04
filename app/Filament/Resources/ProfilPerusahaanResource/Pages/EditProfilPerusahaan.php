<?php

namespace App\Filament\Resources\ProfilPerusahaanResource\Pages;

use App\Filament\Resources\ProfilPerusahaanResource;
use App\Services\FileHandlers\SingleFileHandler;
use App\Services\FileHandlers\MultipleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProfilPerusahaan extends EditRecord
{
    protected static string $resource = ProfilPerusahaanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    SingleFileHandler::deleteFile($record, 'logo_perusahaan');
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_perusahaan');
                }),
        ];
    }

    /**
     * Menangani penghapusan file yang dihapus saat update
     * Method ini dipanggil sebelum validasi sehingga kita bisa membandingkan data
     */
    public function beforeSave(): void
    {
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'logo_perusahaan');
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_perusahaan');
    }

    /**
     * Menangani file yang diunggah bersama form edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = SingleFileHandler::formatFileData($data, 'logo_perusahaan');
        $data = MultipleFileHandler::formatFileData($data, 'thumbnail_perusahaan');
        return $data;
    }

    /**
     * Menangani jika record dihapus dengan bulk action
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_media_sosial'];
    }
}
