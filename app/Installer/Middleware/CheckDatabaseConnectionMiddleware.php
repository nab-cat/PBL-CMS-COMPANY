<?php

namespace App\Installer\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Installer\Main\ConnectionSupervisor;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Routes that require database connection during installation
        $routesRequiringDatabase = [
            'profil_perusahaan',
            'saveProfilPerusahaan',
            'super_admin_config',
            'saveSuperAdmin',
            'user_roles_list',
            'feature_toggles',
            'saveFeatureToggles',
            'finish',
            'finishSave'
        ];

        if (in_array($request->route()->getName(), $routesRequiringDatabase)) {
            if (!$this->checkDatabaseConnection()) {
                // Log::error('Database connection not configured properly. Redirecting to database setup.');

                // Flash a message to the session
                session()->flash('database_error', 'Database connection is not configured properly. Please complete the database configuration first.');

                return redirect()->route('database_import')
                    ->withErrors(['database_connection' => 'Database connection is not configured properly. Please complete the database configuration first.']);
            }
        }

        return $next($request);
    }    /**
         * Check if database connection is working
         * 
         * @return bool
         */
    private function checkDatabaseConnection(): bool
    {
        try {
            // Check if we have a .env file with DB credentials
            if (!file_exists(base_path('.env'))) {
                // Log::error('Middleware: .env file does not exist');
                return false;
            }

            // Use the ConnectionSupervisor to check the connection
            $connectionResult = ConnectionSupervisor::checkConnection();

            if ($connectionResult) {
                // Also verify permissions
                $permissionsCheck = ConnectionSupervisor::checkPermissions();

                if (!$permissionsCheck['success']) {
                    // Log::error('Middleware: Database permission check failed: ' . implode(', ', $permissionsCheck['messages']));
                    return false;
                }

                // Log::info('Middleware: Database connection and permissions verified successfully');
                return true;
            }

            return false;
        } catch (Exception $e) {
            // Log::error('Middleware: Database connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}
