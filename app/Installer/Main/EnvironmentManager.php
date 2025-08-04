<?php

namespace App\Installer\Main;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class EnvironmentManager
{
    private string $envPath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
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

        if (!$process->successful()) {
            // Log::error("Command failed: {$command}");
            // Log::error($process->errorOutput());
            throw new Exception("Command failed: {$command}");
        }
    }

    public function saveFileWizard(Request $request): string
    {
        $results = 'Installer Successfully';

        $env = config('install.env');

        // Add APP_INSTALLED flag to indicate installation is complete
        $envFileData =
            'APP_NAME="${COMPANY_NAME}"' . "\n" .
            'APP_ENV=' . $request->environment . "\n" .
            'APP_KEY=' . 'base64:' . base64_encode(Str::random(32)) . "\n" .
            'APP_DEBUG=' . $request->app_debug . "\n" .
            'APP_TIMEZONE=' . ($request->app_timezone ?? 'UTC') . "\n" .
            'APP_URL=' . $request->app_url . "\n" .
            "\n" .
            'APP_LOCALE=' . ($request->app_locale ?? 'en') . "\n" .
            'APP_FALLBACK_LOCALE=en' . "\n" .
            'APP_FAKER_LOCALE=en_US' . "\n" .
            "\n" .
            'APP_MAINTENANCE_DRIVER=file' . "\n" .
            'PHP_CLI_SERVER_WORKERS=4' . "\n" .
            "\n" .
            'BCRYPT_ROUNDS=12' . "\n" .
            "\n" .
            'LOG_CHANNEL=stack' . "\n" .
            'LOG_STACK=single' . "\n" .
            'LOG_DEPRECATIONS_CHANNEL=null' . "\n" .
            'LOG_LEVEL=' . $request->app_log_level . "\n" .
            "\n" .
            'APP_INSTALLED=false' . "\n" .
            "\n" .
            'DB_CONNECTION=' . $request->database_connection . "\n";

        // Add database configuration based on connection type
        if ($request->database_connection === 'sqlite') {
            // For SQLite, store just the database name in .env
            // Laravel will use storage_path($request->database_name) when needed
            $envFileData .= 'DB_DATABASE=' . $request->database_name . "\n\n";
        } else {
            // For MySQL
            $envFileData .= 'DB_HOST=' . $request->database_hostname . "\n" .
                'DB_PORT=' . $request->database_port . "\n" .
                'DB_DATABASE=' . $request->database_name . "\n" .
                'DB_USERNAME=' . $request->database_username . "\n" .
                'DB_PASSWORD=' . ($request->database_password ?? '') . "\n\n";
        }

        // Add email configuration
        $envFileData .= 'MAIL_MAILER=' . $request->mail_mailer . "\n" .
            'MAIL_HOST=' . ($request->mail_host ?? '') . "\n" .
            'MAIL_PORT=' . ($request->mail_port ?? '587') . "\n" .
            'MAIL_USERNAME="' . ($request->mail_username ?? '') . '"' . "\n" .
            'MAIL_PASSWORD="' . ($request->mail_password ?? '') . '"' . "\n" .
            'MAIL_ENCRYPTION=' . ($request->mail_encryption ?? '') . "\n" .
            'MAIL_FROM_ADDRESS="' . $request->mail_from_address . '"' . "\n" .
            'MAIL_FROM_NAME="${COMPANY_NAME}"' . "\n\n";

        // Add the rest of the environment configuration
        $envFileData .= $env;

        try {
            // Make sure the directory exists
            $envDir = dirname($this->envPath);
            if (!file_exists($envDir)) {
                mkdir($envDir, 0755, true);
            }

            // Write the file
            if (file_put_contents($this->envPath, $envFileData) === false) {
                throw new Exception('Could not write to .env file. Check permissions.');
            }

        } catch (Exception $e) {
            // Log::error('Error writing .env file: ' . $e->getMessage());
            $results = 'Installer Errors: ' . $e->getMessage();
            throw $e; // Re-throw to be caught by the controller
        }

        return $results;
    }
}
