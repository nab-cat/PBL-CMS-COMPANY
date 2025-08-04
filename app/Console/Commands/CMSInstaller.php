<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CMSInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup installation by running series of commands';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting test installation setup...');

        // 1. Composer install
        $this->info('Step 1: Running composer install...');
        $this->executeCommand('composer install');

        // 2. NPM install
        $this->info('Step 2: Running npm install...');
        $this->executeCommand('npm install');

        // 3. NPM build
        $this->info('Step 3: Running npm run build...');
        $this->executeCommand('npm run build');

        // 4. Copy .env file
        $this->info('Step 4: Copying .env file...');
        if (File::exists('.env.example') && !File::exists('.env')) {
            File::copy('.env.example', '.env');
            $this->info('.env file created successfully.');
        } else {
            $this->warn('.env file already exists or .env.example not found.');
        }

        // 5. Generate application key
        $this->info('Step 5: Generating application key...');
        $this->executeArtisanCommand('key:generate');

        // // 6. Create storage link
        // $this->info('Step 6: Creating storage link...');
        // $this->executeArtisanCommand('storage:link');

        // 7. Clean up public storage
        $this->info('Step 6: Cleaning up public storage...');
        $this->cleanupPublicStorage();

        // 8. Run specific migration
        $this->info('Step 7: Running users table migration...');
        $this->executeArtisanCommand('migrate --path=database/migrations/0001_01_01_000000_create_users_table.php');

        // 9. Set proper permissions for Ubuntu environments
        $this->info('Step 8: Setting proper permissions for Ubuntu...');
        $this->setUbuntuPermissions();

        $this->info('Installation setup completed successfully!');
    }

    /**
     * Clean up public storage by removing all files except .gitignore
     * and all directories
     *
     * @return void
     */
    protected function cleanupPublicStorage()
    {
        $files = Storage::disk('public')->allFiles();
        $directories = Storage::disk('public')->allDirectories();

        // Hapus semua file kecuali .gitignore
        foreach ($files as $file) {
            if (basename($file) !== '.gitignore') {
                Storage::disk('public')->delete($file);
            }
        }

        // Hapus semua folder mulai dari yang terdalam
        foreach (array_reverse($directories) as $directory) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        $this->info('Public storage cleaned up successfully.');
    }

    /**
     * Set proper directory and file permissions for Ubuntu environments
     *
     * @return void
     */
    protected function setUbuntuPermissions()
    {
        // Check if running on Linux/Ubuntu
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->warn('Not running on Linux/Ubuntu. Skipping permission settings.');
            return;
        }

        $this->info('Setting storage directory permissions...');
        $this->executeCommand('chmod -R 775 storage');
        $this->executeCommand('chmod -R 775 bootstrap/cache');

        // Get webserver user (usually www-data)
        $webServerUser = 'www-data';
        $this->info("Setting ownership to user and {$webServerUser}...");

        // Get the current user
        $currentUser = trim(shell_exec('whoami'));
        if (!$currentUser) {
            $this->warn('Could not determine current user. Using default permissions only.');
        } else {
            $this->executeCommand("chown -R {$currentUser}:{$webServerUser} storage");
            $this->executeCommand("chown -R {$currentUser}:{$webServerUser} bootstrap/cache");

            // Set ownership for .env file if it exists
            if (File::exists('.env')) {
                $this->info('Setting permissions for .env file...');
                $this->executeCommand("chown {$currentUser}:{$webServerUser} .env");
                $this->executeCommand("chmod 660 .env");
            }
        }
    }

    /**
     * Execute a shell command and display output
     *
     * @param string $command
     * @return void
     */
    protected function executeCommand($command)
    {
        $process = Process::run($command);

        if ($process->successful()) {
            $this->info($process->output());
        } else {
            $this->error("Command failed: {$command}");
            $this->error($process->errorOutput());
        }
    }

    /**
     * Execute an Artisan command
     *
     * @param string $command
     * @return void
     */
    protected function executeArtisanCommand($command)
    {
        $this->executeCommand("php artisan {$command}");
    }
}
