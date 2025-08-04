<?php

namespace App\Installer\Main;

use Exception;
use PDOException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConnectionSupervisor
{
    /**
     * Check if database connection is properly set up
     * 
     * @return bool
     */
    public static function checkConnection(): bool
    {
        try {
            // Check if we can connect to the database
            $pdo = DB::connection()->getPdo();
            if (!$pdo) {
                // Log::error('ConnectionSupervisor: PDO connection failed');
                return false;
            }

            // Execute a simple query to verify permissions
            $result = DB::select('SELECT 1 as connection_test');

            if (!$result || !isset($result[0]->connection_test) || $result[0]->connection_test !== 1) {
                // Log::error('ConnectionSupervisor: Test query failed');
                return false;
            }

            // Check database version
            if (DB::connection()->getDriverName() === 'mysql') {
                try {
                    $version = DB::select('SELECT VERSION() as version')[0]->version;
                    // Log::info("ConnectionSupervisor: Database version: {$version}");
                } catch (Exception $e) {
                    // Log::warning("ConnectionSupervisor: Failed to get database version: {$e->getMessage()}");
                    // Continue even if this fails
                }
            }

            // Log::info('ConnectionSupervisor: Database connection verification successful');
            return true;
        } catch (PDOException $e) {
            // Log::error('ConnectionSupervisor: PDO Exception: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            // Log::error('ConnectionSupervisor: General Exception: ' . $e->getMessage());
            return false;
        }
    }    /**
         * Check if database has all required table permissions
         * 
         * @return array
         */
    public static function checkPermissions(): array
    {
        $results = [
            'success' => true,
            'messages' => [],
        ];

        try {
            // Get database driver
            $driver = DB::connection()->getDriverName();

            // Different checks based on driver
            if ($driver === 'sqlite') {
                // SQLite permissions check
                try {
                    // Make sure SQLite file exists and is writable
                    $dbPath = DB::connection()->getDatabaseName();

                    if (!file_exists($dbPath)) {
                        // Create file if it doesn't exist
                        try {
                            touch($dbPath);
                            chmod($dbPath, 0644);
                            $results['messages'][] = 'SQLite database created: OK';
                        } catch (Exception $e) {
                            $results['success'] = false;
                            $results['messages'][] = 'SQLite database creation failed: ' . $e->getMessage();
                            return $results;
                        }
                    }

                    if (!is_writable($dbPath)) {
                        $results['success'] = false;
                        $results['messages'][] = 'SQLite database is not writable';
                        return $results;
                    }

                    $results['messages'][] = 'SQLite file permissions: OK';

                    // Test basic operations
                    $tempTableName = 'temp_installer_check_' . rand(1000, 9999);
                    DB::statement("CREATE TABLE {$tempTableName} (id INTEGER PRIMARY KEY)");
                    $results['messages'][] = 'CREATE TABLE permission: OK';

                    DB::table($tempTableName)->insert(['id' => 1]);
                    $results['messages'][] = 'INSERT permission: OK';

                    DB::statement("DROP TABLE {$tempTableName}");
                    $results['messages'][] = 'DROP TABLE permission: OK';

                } catch (Exception $e) {
                    $results['success'] = false;
                    $results['messages'][] = 'SQLite operations failed: ' . $e->getMessage();
                }

                return $results;
            }

            // MySQL permissions check
            try {
                $tempTableName = 'temp_installer_check_' . rand(1000, 9999);
                DB::statement("CREATE TABLE {$tempTableName} (id INT)");
                $results['messages'][] = 'CREATE TABLE permission: OK';

                // Test INSERT permission
                try {
                    DB::table($tempTableName)->insert(['id' => 1]);
                    $results['messages'][] = 'INSERT permission: OK';
                } catch (Exception $e) {
                    $results['success'] = false;
                    $results['messages'][] = 'INSERT permission: Failed - ' . $e->getMessage();
                }

                // Test UPDATE permission
                try {
                    DB::table($tempTableName)->where('id', 1)->update(['id' => 2]);
                    $results['messages'][] = 'UPDATE permission: OK';
                } catch (Exception $e) {
                    $results['success'] = false;
                    $results['messages'][] = 'UPDATE permission: Failed - ' . $e->getMessage();
                }

                // Test DELETE permission
                try {
                    DB::table($tempTableName)->where('id', 2)->delete();
                    $results['messages'][] = 'DELETE permission: OK';
                } catch (Exception $e) {
                    $results['success'] = false;
                    $results['messages'][] = 'DELETE permission: Failed - ' . $e->getMessage();
                }

                // Drop the temporary table
                DB::statement("DROP TABLE {$tempTableName}");
                $results['messages'][] = 'DROP TABLE permission: OK';

            } catch (Exception $e) {
                $results['success'] = false;
                $results['messages'][] = 'CREATE/DROP TABLE permission: Failed - ' . $e->getMessage();
            }
        } catch (Exception $e) {
            $results['success'] = false;
            $results['messages'][] = 'Error checking permissions: ' . $e->getMessage();
        }

        return $results;
    }
}
