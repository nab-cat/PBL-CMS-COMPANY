<?php

namespace App\Services\FileHandlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class SingleFileHandler
{
    /**
     * Delete a single file from a record attribute
     */
    public static function deleteFile($record, string $attribute): void
    {
        $file = $record->$attribute;

        if (!empty($file) && Storage::disk('public')->exists($file)) {
            Storage::disk('public')->delete($file);
        }
    }

    /**
     * Delete files from multiple records
     */
    public static function deleteBulkFiles(Collection $records, string $attribute): void
    {
        foreach ($records as $record) {
            self::deleteFile($record, $attribute);
        }
    }

    /**
     * Handle removed file during update
     */
    public static function handleRemovedFile($record, array $formData, string $attribute): void
    {
        if (!$record) {
            return;
        }

        $oldFile = $record->getOriginal($attribute);

        // Jika field tidak ada di form data → user tidak menyentuh field → jangan hapus
        if (!array_key_exists($attribute, $formData)) {
            return;
        }

        // Jika field kosong → user sengaja mengosongkan field → hapus file lama
        if (empty($formData[$attribute])) {
            if (!empty($oldFile) && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            return;
        }

        // Jika ada file baru → hapus file lama jika berbeda
        $newValue = $formData[$attribute];
        $newFile = is_array($newValue)
            ? reset($newValue)
            : $newValue;

        if (!empty($oldFile) && $oldFile !== $newFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }
    }



    /**
     * Format file data before save
     */
    public static function formatFileData(array $data, string $attribute): array
    {
        // No special formatting needed for single files, just return data
        return $data;
    }
}