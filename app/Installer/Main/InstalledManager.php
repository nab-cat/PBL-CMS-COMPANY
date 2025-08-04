<?php

namespace App\Installer\Main;

use Illuminate\Support\Facades\Log;

class InstalledManager
{
    /**
     * Create installed file.
     */
    public static function create(): bool
    {
        try {
            $installedLogFile = storage_path('installed');
            $dateStamp = date('Y/m/d h:i:sa');
            $message = 'successfully installed';

            // Ensure storage directory exists
            $storageDir = dirname($installedLogFile);
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
            }

            // Write installation marker with proper error checking
            $result = file_put_contents($installedLogFile, $message . ' ' . $dateStamp . PHP_EOL, FILE_APPEND | LOCK_EX);

            if ($result === false) {
                //Log::error('Failed to create installation marker file');
                return false;
            }

            // Log::info('Installation marker created successfully');
            return true;

        } catch (\Exception $e) {
            // Log::error('Exception creating installation marker: ' . $e->getMessage());
            return false;
        }
    }
}
