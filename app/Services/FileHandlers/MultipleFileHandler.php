<?php

namespace App\Services\FileHandlers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class MultipleFileHandler
{
    /**
     * Delete files from a record attribute containing multiple files
     */
    public static function deleteFiles($record, string $attribute): void
    {
        $files = $record->$attribute;

        if (empty($files)) {
            return;
        }

        $files = is_array($files) ? $files : json_decode($files, true);

        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * Delete files from multiple records
     */
    public static function deleteBulkFiles(Collection $records, string $attribute): void
    {
        foreach ($records as $record) {
            self::deleteFiles($record, $attribute);
        }
    }

    /**
     * Handle removed files during update
     */
    public static function handleRemovedFiles($record, array $formData, string $attribute): void
    {
        if (!$record) {
            return;
        }

        // Get old files before update
        $oldFiles = $record->getOriginal($attribute);

        // Get new files from form data
        $newFiles = $formData[$attribute] ?? null;

        // Convert to arrays if needed
        $oldFiles = $oldFiles ? (is_array($oldFiles) ? $oldFiles : json_decode($oldFiles, true)) : [];
        $newFiles = $newFiles ? (is_array($newFiles) ? $newFiles : json_decode($newFiles, true)) : [];

        // Find deleted files (in old but not in new)
        $deletedFiles = array_diff($oldFiles, $newFiles);

        // Delete unused files
        foreach ($deletedFiles as $file) {
            if (Storage::disk('public')->exists($file)) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * Format file data before save
     */
    public static function formatFileData(array $data, string $attribute): array
    {
        if (isset($data[$attribute])) {
            $data[$attribute] = is_string($data[$attribute])
                ? json_decode($data[$attribute], true)
                : $data[$attribute];
        }

        return $data;
    }
}