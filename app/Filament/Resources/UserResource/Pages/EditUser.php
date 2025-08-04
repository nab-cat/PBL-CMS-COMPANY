<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\FileHandlers\SingleFileHandler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    SingleFileHandler::deleteFile($record, 'foto_profil');
                }),
        ];
    }

    /**
     * Menangani penghapusan file yang dihapus saat update
     * Method ini dipanggil sebelum validasi sehingga kita bisa membandingkan data
     */
    public function beforeSave(): void
    {
        SingleFileHandler::handleRemovedFile($this->record, $this->data, 'foto_profil');
    }

    /**
     * Menangani file yang diunggah bersama form edit
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return SingleFileHandler::formatFileData($data, 'foto_profil');
    }

    /**
     * Menangani password update - hash jika ada password baru
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash password jika ada dan tidak kosong
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Jika password kosong, hapus dari data agar tidak diupdate
            unset($data['password']);
        }

        return SingleFileHandler::formatFileData($data, 'foto_profil');
    }
}
