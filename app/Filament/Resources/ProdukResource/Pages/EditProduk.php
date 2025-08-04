<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use App\Services\FileHandlers\MultipleFileHandler;

class EditProduk extends EditRecord
{
    protected static string $resource = ProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    MultipleFileHandler::deleteFiles($record, 'thumbnail_produk');
                }),
        ];
    }

    /**
     * Menangani penghapusan file yang dihapus saat update
     * Method ini dipanggil sebelum validasi sehingga kita bisa membandingkan data
     */
    public function beforeSave(): void
    {
        MultipleFileHandler::handleRemovedFiles($this->record, $this->data, 'thumbnail_produk');
    }

    /**
     * Menangani file yang diunggah bersama form edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MultipleFileHandler::formatFileData($data, 'thumbnail_produk');
    }

    /**
     * Menangani jika record dihapus dengan bulk action
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_produk', 'slug'];
    }
}
