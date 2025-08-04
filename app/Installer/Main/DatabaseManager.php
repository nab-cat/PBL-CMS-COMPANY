<?php

namespace App\Installer\Main;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class DatabaseManager
{
    /**
     * Migrate and seed the database.
     */
    public static function MigrateAndSeed(bool $includeDummyData = false): array
    {
        $dm = new DatabaseManager;
        $outputLog = new BufferedOutput;

        return $dm->migrate($outputLog, $includeDummyData);
    }

    /**
     * Migrate and run essential seeders only.
     */
    public static function MigrateAndEssentialSeed(): array
    {
        $dm = new DatabaseManager;
        $outputLog = new BufferedOutput;

        return $dm->migrateAndEssential($outputLog);
    }

    /**
     * Run only seeding without migration.
     */
    public static function SeedOnly(bool $includeDummyData = false): array
    {
        $dm = new DatabaseManager;
        $outputLog = new BufferedOutput;

        return $dm->seed($outputLog, $includeDummyData);
    }

    /**
     * Run the migration and call the seeder.
     */
    private function migrate(BufferedOutput $outputLog, bool $includeDummyData = false): array
    {
        try {
            // Clear any existing output
            $outputLog->fetch();

            Artisan::call('migrate:fresh', [
                '--force' => true,
            ], $outputLog);

            $logContents = $outputLog->fetch();
            //Log::info('Migration result: ' . $logContents);

            // Check for migration errors more thoroughly
            if (
                stripos($logContents, 'error') !== false ||
                stripos($logContents, 'exception') !== false ||
                stripos($logContents, 'failed') !== false ||
                stripos($logContents, 'could not') !== false
            ) {
                // Log::error('Migration failed: ' . $logContents);
                throw new \Exception('Database migration failed: ' . $logContents);
            }

            // Check if any migrations were actually run
            if (
                stripos($logContents, 'Nothing to migrate') !== false &&
                stripos($logContents, 'Dropped all tables successfully') === false
            ) {
                // Log::warning('No migrations found or executed');
            }

        } catch (Exception $e) {
            // Log::error('Migration exception: ' . $e->getMessage());
            return ['error', $e->getMessage()];
        }

        return $this->seed($outputLog, $includeDummyData);
    }

    /**
     * Run the migration and call the essential seeder.
     */
    private function migrateAndEssential(BufferedOutput $outputLog): array
    {
        try {
            // Clear any existing output
            $outputLog->fetch();

            Artisan::call('migrate:fresh', [
                '--force' => true,
            ], $outputLog);

            $logContents = $outputLog->fetch();
            // Log::info('Migration result: ' . $logContents);

            // Check for migration errors more thoroughly
            if (
                stripos($logContents, 'error') !== false ||
                stripos($logContents, 'exception') !== false ||
                stripos($logContents, 'failed') !== false ||
                stripos($logContents, 'could not') !== false
            ) {
                // Log::error('Migration failed: ' . $logContents);
                throw new \Exception('Database migration failed: ' . $logContents);
            }

        } catch (Exception $e) {
            // Log::error('Migration exception: ' . $e->getMessage());
            return ['error', $e->getMessage()];
        }

        return $this->essentialSeed($outputLog);
    }

    /**
     * Seed the database.
     */
    private function seed(BufferedOutput $outputLog, bool $includeDummyData = false): array
    {
        try {
            // Clear any existing output
            $outputLog->fetch();

            if ($includeDummyData) {
                // Jalankan semua seeder (termasuk data dummy) tanpa migrate:fresh
                // karena migration sudah dilakukan sebelumnya
                Artisan::call('db:seed', ['--force' => true], $outputLog);
            } else {
                // Hanya jalankan ShieldSeeder
                Artisan::call('db:seed', [
                    '--class' => 'ShieldSeeder',
                    '--force' => true,
                ], $outputLog);
            }

            $logContents = $outputLog->fetch();
            // Log::info('Seeding result: ' . $logContents);

            // Check for seeding errors more thoroughly - improved error detection
            $errorPatterns = [
                'error',
                'exception',
                'failed',
                'could not',
                'class not found',
                'undefined property',
                'call to undefined method',
                'syntax error',
                'fatal error'
            ];

            foreach ($errorPatterns as $pattern) {
                if (stripos($logContents, $pattern) !== false) {
                    // Extract more specific error information
                    $lines = explode("\n", $logContents);
                    $errorDetails = [];
                    foreach ($lines as $line) {
                        if (stripos($line, $pattern) !== false) {
                            $errorDetails[] = trim($line);
                        }
                    }

                    $errorMessage = !empty($errorDetails)
                        ? 'Database seeding failed: ' . implode('; ', $errorDetails)
                        : 'Database seeding failed: ' . $logContents;

                    // Log::error('Seeding failed: ' . $errorMessage);
                    return ['error', $errorMessage];
                }
            }

            return ['success', $logContents];
        } catch (Exception $e) {
            // Log::error('Seeding exception: ' . $e->getMessage());
            return ['error', 'Seeding exception: ' . $e->getMessage()];
        }
    }

    /**
     * Seed the database with essential data only.
     */
    private function essentialSeed(BufferedOutput $outputLog): array
    {
        try {
            $essentialSeeders = [
                'ShieldSeeder',
                'ProfilPerusahaanSeeder',
                'KontenSliderSeeder',
                'FeatureToggleSeeder'
            ];

            // Clear any existing output
            $outputLog->fetch();

            foreach ($essentialSeeders as $seeder) {
                try {
                    Artisan::call('db:seed', [
                        '--class' => $seeder,
                        '--force' => true,
                    ], $outputLog);

                    // Log::info("Successfully ran seeder: $seeder");
                } catch (Exception $e) {
                    // Log::warning("Seeder $seeder failed: " . $e->getMessage());
                    // Continue with other seeders even if one fails (some might not exist)
                }
            }

            $logContents = $outputLog->fetch();
            // Log::info('Essential seeding result: ' . $logContents);

            // Check for critical seeding errors - but be less strict than general seeding
            if (
                stripos($logContents, 'fatal') !== false ||
                stripos($logContents, 'syntax error') !== false
            ) {
                // Log::error('Essential seeding failed: ' . $logContents);
                return ['error', 'Essential seeding failed: ' . $logContents];
            }

            return ['success', $logContents];
        } catch (Exception $e) {
            // Log::error('Essential seeding exception: ' . $e->getMessage());
            return ['error', $e->getMessage()];
        }
    }
}
