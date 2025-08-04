<?php

namespace App\Installer\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DatabaseTestController extends Controller
{
    /**
     * Test database connection based on provided credentials
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection(Request $request)
    {
        $connection = $request->input('database_connection', 'mysql');
        $database = $request->input('database_name', '');

        // SQLite configuration
        if ($connection == 'sqlite') {
            // Set SQLite database path to storage root directory
            $database = storage_path($database ?: 'database-test.sqlite');

            // Create empty SQLite file if it doesn't exist
            if (!file_exists(dirname($database))) {
                mkdir(dirname($database), 0755, true);
            }

            if (!file_exists($database)) {
                try {
                    touch($database);
                    chmod($database, 0644);
                } catch (Exception $e) {
                    // Log::error('Error creating SQLite database file: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => __('installer.sqlite_file_creation_failed_friendly'),
                        'technical_details' => $e->getMessage()
                    ]);
                }
            }

            // Log::info('Testing SQLite database connection', [
            //     'connection' => $connection,
            //     'database' => $database
            // ]);

            // Configure SQLite connection
            config([
                'database' => [
                    'default' => 'testing',
                    'connections' => [
                        'testing' => [
                            'driver' => 'sqlite',
                            'database' => $database,
                            'prefix' => '',
                            'foreign_key_constraints' => true,
                        ],
                    ],
                ],
            ]);
        }
        // MySQL configuration
        else {
            $host = $request->input('database_hostname', '127.0.0.1');
            $port = $request->input('database_port', '3306');
            $username = $request->input('database_username', '');
            $password = $request->input('database_password', '');

            // Log::info('Testing MySQL database connection', [
            //     'connection' => $connection,
            //     'host' => $host,
            //     'port' => $port,
            //     'database' => $database,
            //     'username' => $username
            // ]);

            // Configure MySQL connection
            config([
                'database' => [
                    'default' => 'testing',
                    'connections' => [
                        'testing' => [
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
                    ],
                ],
            ]);
        }

        // Test connection
        DB::purge();

        try {
            DB::connection('testing')->getPdo();

            // Try to run a simple query
            DB::connection('testing')->select('SELECT 1 as connection_test');
            DB::disconnect('testing');

            // Log::info('Database connection test successful');

            if ($connection == 'sqlite') {
                // If SQLite, unlink the database file after testing
                unlink($database);
            }

            return response()->json([
                'success' => true,
                'message' => __('installer.database_connection_successful'),
            ]);
        } catch (Exception $e) {
            // Log::error('Database connection test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('installer.database_connection_failed_friendly'),
                'technical_details' => $e->getMessage(),
            ]);
        }
    }
}
