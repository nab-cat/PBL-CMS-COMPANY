<?php

namespace App\Installer\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Installer\Main\ConnectionSupervisor;
use Illuminate\Support\Facades\Validator;
use App\Installer\Main\EnvironmentManager;

class DatabaseController extends Controller
{
    protected EnvironmentManager $EnvironmentManager;
    private string $lastDatabaseError = '';

    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    public function databaseImport(Request $request)
    {
        return view('InstallerEragViews::database-import');
    }

    public function saveWizard(Request $request, Redirector $redirect)
    {
        // Debug the request data
        // Log::info('Form submission data:', $request->all());
        // Log::info('Headers:', $request->headers->all());

        // Check if database name is provided, always required
        if (empty($request->input('database_name'))) {
            // Log::error('Missing required database name');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['database_fields' => [__('installer.database_name_required')]]
                ], 422);
            }

            return $redirect->route('database_import')->withInput()->withErrors([
                'database_fields' => __('installer.database_name_required'),
            ]);
        }

        // For SQLite, ensure database name has .sqlite extension and is valid
        if ($request->input('database_connection') === 'sqlite') {
            $dbName = $request->input('database_name');

            // Sanitize database name to only allow alphanumeric, dashes, underscores
            $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $dbName);

            if ($sanitizedName !== $dbName) {
                // Log::error('Invalid SQLite database name: ' . $dbName);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['database_name' => [__('installer.database_name_invalid')]]
                    ], 422);
                }

                return $redirect->route('database_import')->withInput()->withErrors([
                    'database_name' => __('installer.database_name_invalid'),
                ]);
            }

            // Add .sqlite extension if missing
            if (!str_ends_with(strtolower($sanitizedName), '.sqlite')) {
                $sanitizedName .= '.sqlite';
            }

            $request->merge(['database_name' => $sanitizedName]);
        }

        // For MySQL connection, we need additional checks
        if ($request->input('database_connection') === 'mysql') {
            if (empty($request->input('database_hostname')) || empty($request->input('database_username'))) {
                // Log::error('Missing required MySQL database credentials');

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['database_fields' => [__('installer.mysql_credentials_required')]]
                    ], 422);
                }

                return $redirect->route('database_import')->withInput()->withErrors([
                    'database_fields' => __('installer.mysql_credentials_required'),
                ]);
            }
        }

        $rules = config('install.environment.form.rules');

        // Add conditional validation for email fields based on mail driver
        $mailDriver = $request->input('mail_mailer');
        if ($mailDriver === 'smtp') {
            // Require host and port for SMTP
            $rules['mail_host'] = 'required|string|max:100';
            $rules['mail_port'] = 'required|numeric|min:1|max:65535';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::error('Validation errors:', $validator->errors()->toArray());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return $redirect->route('database_import')->withInput()->withErrors($validator->errors());
        }

        // Check database connection
        $dbConnectionSuccess = $this->checkDatabaseConnection($request);
        // Log::info('Database connection check result: ' . ($dbConnectionSuccess ? 'success' : 'failed'));

        if (!$dbConnectionSuccess) {
            // Log::error('Database connection failed - stopping installation process');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'database_connection' => [
                            'message' => __('installer.database_connection_failed_friendly'),
                            'technical_details' => $this->lastDatabaseError
                        ]
                    ]
                ], 422);
            }

            return $redirect->route('database_import')->withInput()->withErrors([
                'database_connection' => __('installer.database_connection_failed_friendly'),
            ]);
        }

        try {
            // Save environment configuration - database connection already verified
            $result = $this->EnvironmentManager->saveFileWizard($request);
            // Log::info('Environment saved: ' . $result);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('profil_perusahaan')
                ]);
            }

            // Only proceed to next step if database connection is established
            // Log::info('Redirecting to company profile setup');
            return redirect(route('profil_perusahaan'));
        } catch (\Exception $e) {
            // Log::error('Error saving environment: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['save_error' => [__('installer.config_save_failed')]]
                ], 500);
            }

            return $redirect->route('database_import')->withInput()->withErrors([
                'save_error' => __('installer.config_save_failed'),
            ]);
        }
    }

    /**
     * Test email connection with provided configuration
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmailConnection(Request $request)
    {
        try {
            // Validate basic email configuration
            $validator = Validator::make($request->all(), [
                'mail_mailer' => 'required|string|in:smtp',
                'mail_from_address' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('installer.email_config_invalid')
                ]);
            }

            $mailDriver = $request->input('mail_mailer');

            // Only SMTP is supported now
            if ($mailDriver !== 'smtp') {
                return response()->json([
                    'success' => false,
                    'message' => __('installer.email_smtp_only')
                ]);
            }

            // For SMTP, validate required fields
            $additionalValidator = Validator::make($request->all(), [
                'mail_host' => 'required|string',
                'mail_port' => 'required|numeric|min:1|max:65535',
            ]);

            if ($additionalValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => __('installer.email_smtp_config_missing')
                ]);
            }

            // Configure temporary mail settings for SMTP
            $originalMailConfig = config('mail');

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => $request->input('mail_host'),
                    'port' => $request->input('mail_port'),
                    'encryption' => $request->input('mail_encryption'),
                    'username' => $request->input('mail_username'),
                    'password' => $request->input('mail_password'),
                    'timeout' => 10,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ],
                'mail.from.address' => $request->input('mail_from_address'),
                'mail.from.name' => 'CMS Company Installation Test',
            ]);

            // Try to send a test email
            try {
                \Illuminate\Support\Facades\Mail::raw('This is a test email from CMS Company installer.', function ($message) use ($request) {
                    $message->to($request->input('mail_from_address'))
                        ->subject('CMS Company Installation - Email Test');
                });

                // Restore original mail configuration
                config(['mail' => $originalMailConfig]);

                return response()->json([
                    'success' => true,
                    'message' => __('installer.email_test_successful', ['email' => $request->input('mail_from_address')])
                ]);

            } catch (\Exception $e) {
                // Restore original mail configuration
                config(['mail' => $originalMailConfig]);

                return response()->json([
                    'success' => false,
                    'message' => __('installer.email_test_failed_friendly'),
                    'technical_details' => $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('installer.email_test_error_friendly'),
                'technical_details' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check database permissions
     * 
     * @return array
     */
    private function checkDatabasePermissions(): array
    {
        return ConnectionSupervisor::checkPermissions();
    }

    private function checkDatabaseConnection(Request $request): bool
    {
        // Store current connection state to restore later
        $currentConnection = config('database.default');
        $connectionSuccess = false;

        $connection = $request->input('database_connection');
        $database = $request->input('database_name');

        if (!$database) {
            // Log::error('No database name provided');
            return false;
        }

        // Use a temporary connection name to avoid conflicts
        $tempConnectionName = 'installer_test_' . time();

        try {
            $settings = config("database.connections.$connection");

            // Handle SQLite differently
            if ($connection === 'sqlite') {
                // Use storage root directory directly for SQLite database
                $databasePath = storage_path($database);

                // Ensure path uses forward slashes for consistency
                $databasePath = str_replace('\\', '/', $databasePath);

                // Create directory if it doesn't exist
                $dirPath = dirname($databasePath);
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0755, true);
                }

                // Remove temporary database.sqlite if it exists
                $tempDbPath = storage_path('database.sqlite');
                if (file_exists($tempDbPath)) {
                    try {
                        // Close any existing connections before attempting to delete
                        DB::disconnect('sqlite');

                        // Try to delete the file with proper error handling
                        if (!unlink($tempDbPath)) {
                            // Log::warning('Could not remove temporary database file: ' . $tempDbPath);
                        } else {
                            // Log::info('Removed temporary database file: ' . $tempDbPath);
                        }
                    } catch (\Exception $e) {
                        // Log file deletion failure but continue with the process
                        // Log::warning('Could not remove temporary database file: ' . $e->getMessage());
                        // Continue execution even if we can't delete the file
                    }
                }

                // Create empty database file if it doesn't exist
                if (!file_exists($databasePath)) {
                    try {
                        if (!touch($databasePath)) {
                            throw new \Exception('Unable to create database file');
                        }
                        chmod($databasePath, 0644);
                        // Log::info('Created SQLite database file at: ' . $databasePath);
                    } catch (\Exception $e) {
                        // Log::error('Failed to create SQLite database file: ' . $e->getMessage());
                        $this->lastDatabaseError = 'SQLite file creation failed: ' . $e->getMessage();
                        return false;
                    }
                }

                // Configure temporary SQLite connection
                config([
                    "database.connections.$tempConnectionName" => [
                        'driver' => 'sqlite',
                        'database' => $databasePath,
                        'prefix' => '',
                        'foreign_key_constraints' => true,
                    ],
                ]);
            } else {
                // MySQL configuration
                $host = $request->input('database_hostname', '127.0.0.1');
                $port = $request->input('database_port', '3306');
                $username = $request->input('database_username', '');
                $password = $request->input('database_password', '');

                // Configure temporary MySQL connection
                config([
                    "database.connections.$tempConnectionName" => [
                        'driver' => $connection,
                        'host' => $host,
                        'port' => $port,
                        'database' => $database,
                        'username' => $username,
                        'password' => $password,
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'strict' => true,
                    ],
                ]);
            }

            // Test the connection with a simple query using temporary connection
            $result = DB::connection($tempConnectionName)->select('SELECT 1 as connection_test');

            if (!$result || !isset($result[0]->connection_test) || $result[0]->connection_test !== 1) {
                // Log::error('Database connection failed: Test query failed');
                $this->lastDatabaseError = 'Database test query failed';
                return false;
            }

            // Check database permissions for MySQL/MariaDB
            if ($connection === 'mysql') {
                // Use the temporary connection for permission check
                $originalDefault = config('database.default');
                config(['database.default' => $tempConnectionName]);

                $permissionResults = $this->checkDatabasePermissions();

                // Restore original default connection
                config(['database.default' => $originalDefault]);

                if (!$permissionResults['success']) {
                    // Log::error('Database permission check failed: ' . implode(', ', $permissionResults['messages']));
                    $this->lastDatabaseError = 'Permission check failed: ' . implode(', ', $permissionResults['messages']);
                    return false;
                }

                // Log::info('Database permission check passed');
            }

            // Log::info('Database connection verified successfully');
            $connectionSuccess = true;
        } catch (Exception $e) {
            // Log::error('Database connection error: ' . $e->getMessage());
            $this->lastDatabaseError = $e->getMessage();
            $connectionSuccess = false;
        } finally {
            // Always disconnect the temporary connection and restore original state
            try {
                DB::disconnect($tempConnectionName);
                config(['database.default' => $currentConnection]);
            } catch (Exception $e) {
                // Log cleanup errors but don't fail the process
                // Log::warning('Error cleaning up temporary database connection: ' . $e->getMessage());
            }
        }

        return $connectionSuccess;
    }
}
