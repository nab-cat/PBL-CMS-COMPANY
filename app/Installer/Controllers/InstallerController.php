<?php

namespace App\Installer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Installer\Main\DatabaseManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Installer\Main\InstalledManager;
use Illuminate\Support\Facades\Validator;
use App\Installer\Main\PermissionsChecker;
use App\Installer\Main\RequirementsChecker;

class InstallerController extends Controller
{
    protected RequirementsChecker $requirements;

    protected PermissionsChecker $permissions;

    public function __construct(PermissionsChecker $permissions, RequirementsChecker $requirements)
    {
        $this->permissions = $permissions;
        $this->requirements = $requirements;
    }

    public function welcome()
    {
        return view('InstallerEragViews::welcome');
    }

    public function switchLanguage($locale)
    {
        // Validate locale
        $supportedLocales = ['en', 'id'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en'; // Default to English if invalid locale
        }

        // Store locale in session
        session(['installer_locale' => $locale]);

        // Set app locale for current request
        app()->setLocale($locale);

        // Get the previous URL or default to welcome page
        $previousUrl = url()->previous();
        $baseUrl = url('install-app');

        // If previous URL doesn't contain install-app, redirect to welcome
        if (!str_contains($previousUrl, 'install-app')) {
            $previousUrl = route('welcome');
        }

        // Redirect back with cookie and session flash for debugging
        return redirect($previousUrl)
            ->withCookie(cookie('installer_locale', $locale, 60 * 24 * 30)) // 30 days
            ->with('language_switched', $locale);
    }

    public function welcomeContinue()
    {
        return redirect(route('installs'));
    }

    public function index()
    {
        // Create storage symbolic link (essential for file uploads)
        try {
            Artisan::call('storage:link');
        } catch (\Exception $e) {
            Log::warning('Failed to create storage link: ' . $e->getMessage());
            // Continue installation even if storage link fails
        }

        $permissions = $this->permissions->check(
            config('install.permissions')
        );

        $phpSupportInfo = $this->requirements->checkPHPversion(
            config('install.core.minPhpVersion')
        );
        $requirements = $this->requirements->check(
            config('install.requirements')
        );

        return view('InstallerEragViews::index', compact('permissions', 'requirements', 'phpSupportInfo'));
    }

    public function install_check()
    {
        return redirect(route('database_import'));
    }

    public function profilPerusahaan()
    {
        // Log::info('Accessing profil perusahaan page');
        // Double-check database connection
        try {
            $dbConnection = DB::connection()->getPdo();
            // Log::info('Database connection established successfully', [
            //     'driver' => DB::getDriverName(),
            //     'host' => config('database.connections.mysql.host'),
            //     'database' => config('database.connections.mysql.database'),
            // ]);
            if (!$dbConnection) {
                return redirect()->route('database_import')
                    ->with('database_error', 'Database connection failed. Please check your configuration.');
            }

            // Run migration and essential seeders
            $migrationResult = DatabaseManager::MigrateAndEssentialSeed();

            if ($migrationResult[0] === 'error') {
                return redirect()->route('database_import')
                    ->with('database_error', 'Database migration/essential seeding failed: ' . ($migrationResult[1] ?? 'Unknown error'))
                    ->withErrors(['database_connection' => 'Database setup failed. Please check your database configuration.']);
            }

            // Log::info('Database migration and essential seeding completed', [
            //     'result' => $migrationResult[1]
            // ]);

            return view('InstallerEragViews::profil-perusahaan');
        } catch (\Exception $e) {
            Log::error('Database connection error: ' . $e->getMessage());
            return redirect()->route('database_import')
                ->with('database_error', 'Database error: ' . $e->getMessage())
                ->withErrors(['database_connection' => 'Database connection failed: ' . $e->getMessage()]);
        }
    }

    public function saveProfilPerusahaan(Request $request, Redirector $redirect)
    {
        $rules = config('install.profil_perusahaan');

        Log::info('Saving company profile', [
            'has_file' => $request->hasFile('logo_perusahaan'),
            'all_inputs' => $request->all(),
        ]);

        // Add custom validation for logo
        $request->validate($rules, [
            'logo_perusahaan.image' => __('installer.logo_invalid_format'),
            'logo_perusahaan.mimes' => __('installer.logo_invalid_format'),
            'logo_perusahaan.max' => __('installer.logo_file_too_large'),
            'logo_perusahaan.dimensions' => 'Dimensi logo minimal 100x100px.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.min' => 'Nama perusahaan minimal 2 karakter.',
            'alamat_perusahaan.required' => 'Alamat perusahaan wajib diisi.',
            'alamat_perusahaan.min' => 'Alamat perusahaan minimal 10 karakter.',
            'email_perusahaan.required' => 'Email perusahaan wajib diisi.',
            'email_perusahaan.email' => 'Format email tidak valid.',
            'link_alamat_perusahaan.url' => 'Link alamat harus berupa URL yang valid.',
            'deskripsi_perusahaan.max' => 'Deskripsi perusahaan maksimal 1000 karakter.',
        ]);

        // Debug information
        Log::info('Form submission received', [
            'has_file' => $request->hasFile('logo_perusahaan'),
            'all_inputs' => $request->all(),
        ]);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return $redirect->route('profil_perusahaan')->withInput()->withErrors($validator->errors());
        }

        // Initialize all fillable fields to null/empty to ensure they override existing seeded data
        $fillableFields = (new \App\Models\ProfilPerusahaan())->getFillable();
        $data = array_fill_keys($fillableFields, null);

        // Set the default theme instead of null
        $data['tema_perusahaan'] = '#31487A';

        // Then override with submitted form data
        $formData = $request->except(['_token', 'logo_perusahaan']);
        $data = array_merge($data, $formData);

        try {
            DB::beginTransaction();

            // Check if ProfilPerusahaan with ID 1 exists
            $profilPerusahaan = \App\Models\ProfilPerusahaan::find(1);

            // Handle logo upload if provided
            if ($request->hasFile('logo_perusahaan')) {
                $logo = $request->file('logo_perusahaan');

                // Additional server-side validation
                $logoValidation = $this->validateLogoFile($logo);
                if (!$logoValidation['valid']) {
                    return $redirect->route('profil_perusahaan')
                        ->withInput()
                        ->withErrors(['logo_perusahaan' => $logoValidation['message']]);
                }

                // Validate the file
                if ($logo->isValid()) {
                    // Delete old logo file if exists
                    if ($profilPerusahaan && $profilPerusahaan->logo_perusahaan) {
                        Storage::disk('public')->delete($profilPerusahaan->logo_perusahaan);
                    }

                    // Check if directory exists and is writable
                    $storageDir = storage_path('app/public/logo-perusahaan');

                    // Generate unique filename to prevent conflicts
                    $originalName = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $logo->getClientOriginalExtension();
                    $filename = $originalName . '_' . time() . '.' . $extension;

                    // Store the file with custom name
                    $path = $logo->storeAs('logo-perusahaan', $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Failed to store logo file');
                    }

                    $data['logo_perusahaan'] = $path;
                    Log::info('Logo uploaded successfully', ['path' => $path, 'size' => $logo->getSize()]);
                } else {
                    // Log error for debugging
                    Log::error('Logo upload failed: ' . $logo->getErrorMessage());
                    return $redirect->route('profil_perusahaan')
                        ->withInput()
                        ->withErrors(['logo_perusahaan' => 'File upload failed. Please try again.']);
                }
            }

            if ($profilPerusahaan) {
                // Update existing record
                $profilPerusahaan->update($data);
                Log::info('Company profile updated', ['id' => $profilPerusahaan->id_profil_perusahaan]);
            } else {
                // Create new record with ID 1
                $data['id_profil_perusahaan'] = 1;
                \App\Models\ProfilPerusahaan::create($data);
                Log::info('Company profile created', ['id' => 1]);
            }

            DB::commit();
            Log::info('Company profile saved successfully');

            // Redirect to super admin configuration instead of feature toggles
            return redirect(route('super_admin_config'))
                ->with('success', __('installer.company_profile_saved'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving company profile: ' . $e->getMessage());
            return $redirect->route('profil_perusahaan')
                ->withInput()
                ->withErrors(['general_error' => __('installer.company_profile_save_error') . ': ' . $e->getMessage()]);
        }
    }

    public function superAdminConfig()
    {
        return view('InstallerEragViews::super-admin-config');
    }

    public function saveSuperAdmin(Request $request, Redirector $redirect)
    {
        // Regular validation
        $rules = config('install.super_admin');
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return $redirect->route('super_admin_config')->withInput()->withErrors($validator->errors());
        }

        // Ambil nilai checkbox untuk data dummy
        $includeDummyData = $request->has('include_dummy_data') && $request->include_dummy_data == '1';

        // Jika user memilih untuk mengisi data dummy, jalankan seeder lengkap
        if ($includeDummyData) {
            try {
                $seedingResult = DatabaseManager::SeedOnly(true);

                if ($seedingResult[0] === 'error') {
                    Log::error('Database seeding failed: ' . ($seedingResult[1] ?? 'Unknown error'));
                    return $redirect->route('super_admin_config')
                        ->withInput()
                        ->with('database_error', 'Database seeding failed: ' . ($seedingResult[1] ?? 'Unknown error'))
                        ->withErrors(['general_error' => 'Database seeding failed. Please check your database configuration.']);
                }

                Log::info('Database dummy data seeding completed', [
                    'result' => $seedingResult[1]
                ]);

            } catch (\Exception $e) {
                Log::error('Error during dummy data seeding: ' . $e->getMessage());
                return $redirect->route('super_admin_config')
                    ->withInput()
                    ->withErrors(['general_error' => 'Database seeding failed: ' . $e->getMessage()]);
            }
        }

        // Check if account already exists
        $existingUser = \App\Models\User::where('email', $request->email)->first();

        if ($existingUser) {
            // If user already exists, check if they have super_admin role
            if ($existingUser->hasRole('super_admin')) {
                return $redirect->route('user_roles_list')
                    ->with('account_exists', __('installer.super_admin_exists', ['email' => $request->email]));
            } else {
                // User exists but doesn't have super_admin role
                try {
                    DB::beginTransaction();

                    // Assign super_admin role to existing user and update status_kepegawaian
                    if (class_exists('Spatie\Permission\Models\Role')) {
                        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
                        $existingUser->assignRole($superAdminRole);

                        // Update status_kepegawaian to Tetap
                        $existingUser->status_kepegawaian = 'Tetap';
                        $existingUser->save();

                        // Verify role assignment
                        if (!$existingUser->hasRole('super_admin')) {
                            throw new \Exception('Failed to assign super admin role to existing user');
                        }
                    } else {
                        throw new \Exception('Spatie Permission package not found');
                    }

                    DB::commit();
                    Log::info('Super admin role assigned to existing user: ' . $existingUser->email);

                    return $redirect->route('user_roles_list')
                        ->with('account_exists', __('installer.user_exists_role_assigned', ['email' => $request->email]));

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error assigning super admin role: ' . $e->getMessage());
                    return $redirect->route('super_admin_config')
                        ->withInput()
                        ->withErrors(['general_error' => __('installer.failed_assign_role') . ': ' . $e->getMessage()]);
                }
            }
        }

        try {
            // Use database transaction for super admin creation
            DB::beginTransaction();

            // Create super admin user
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status_kepegawaian' => 'Tetap', // Set status kepegawaian to Tetap
                'email_verified_at' => now(),
            ]);

            // Assign super admin role (menggunakan shield package)
            if (class_exists('Spatie\Permission\Models\Role')) {
                $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
                $user->assignRole($superAdminRole);

                // Verify role assignment
                if (!$user->hasRole('super_admin')) {
                    throw new \Exception('Failed to assign super admin role');
                }
            } else {
                // Log::warning('Spatie Permission package not found, role assignment skipped');
            }

            DB::commit();
            // Log::info('Super admin created successfully: ' . $user->email);

            // Arahkan ke halaman daftar user dengan role
            return redirect(route('user_roles_list'))
                ->with('success', __('installer.super_admin_created_msg'));

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error creating super admin: ' . $e->getMessage());
            return $redirect->route('super_admin_config')
                ->withInput()
                ->withErrors(['general_error' => 'Failed to create super admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilkan daftar user dengan role mereka
     */
    public function userRolesList()
    {
        // Ambil super admin yang baru saja dibuat
        $superAdmin = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->latest()->first();

        // Jika tidak ada super admin, kemungkinan user langsung mengakses URL ini
        // Kita redirect ke halaman sebelumnya
        if (!$superAdmin) {
            return redirect(route('super_admin_config'));
        }

        // Ambil semua user dengan role mereka
        $users = \App\Models\User::with('roles')->get();

        return view('InstallerEragViews::user-roles-list', [
            'superAdmin' => $superAdmin,
            'users' => $users
        ]);
    }

    public function featureToggles()
    {
        // Get all feature toggles or create default ones if none exist
        $features = \App\Models\FeatureToggle::all();

        // If no features exist yet, create default ones
        if ($features->isEmpty()) {
            try {
                $defaultFeatures = [
                    ['key' => 'artikel_module', 'label' => 'Modul Artikel', 'status_aktif' => true],
                    ['key' => 'case_study_module', 'label' => 'Modul Case Study', 'status_aktif' => true],
                    ['key' => 'event_module', 'label' => 'Modul Event', 'status_aktif' => true],
                    ['key' => 'feedback_module', 'label' => 'Modul Feedback', 'status_aktif' => true],
                    ['key' => 'galeri_module', 'label' => 'Modul Galeri', 'status_aktif' => true],
                    ['key' => 'lamaran_module', 'label' => 'Modul Lamaran', 'status_aktif' => true],
                    ['key' => 'lowongan_module', 'label' => 'Modul Lowongan', 'status_aktif' => true],
                    ['key' => 'mitra_module', 'label' => 'Modul Mitra', 'status_aktif' => true],
                    ['key' => 'produk_module', 'label' => 'Modul Produk', 'status_aktif' => true],
                    ['key' => 'testimoni_module', 'label' => 'Modul Testimoni', 'status_aktif' => true],
                    ['key' => 'unduhan_module', 'label' => 'Modul Unduhan', 'status_aktif' => true],
                ];

                // Use batch insert for better performance and atomicity
                DB::beginTransaction();

                foreach ($defaultFeatures as $feature) {
                    \App\Models\FeatureToggle::firstOrCreate(
                        ['key' => $feature['key']],
                        $feature
                    );
                }

                DB::commit();
                // Log::info('Default feature toggles created successfully');

                $features = \App\Models\FeatureToggle::all();

            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error('Error creating default feature toggles: ' . $e->getMessage());
                // Continue with empty features - user can create them manually
            }
        }

        return view('InstallerEragViews::feature-toggles', compact('features'));
    }

    public function saveFeatureToggles(Request $request, Redirector $redirect)
    {
        // Validate using rules from config
        $rules = config('install.feature_toggles');

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Log::warning('Feature toggle validation failed', ['errors' => $validator->errors()]);
            return $redirect->route('feature_toggles')->withInput()->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            // Get all feature toggles
            $allFeatures = \App\Models\FeatureToggle::all();

            // Extract submitted features from form
            $submittedFeatures = $request->input('features', []);

            // Update each feature's status
            foreach ($allFeatures as $feature) {
                $feature->status_aktif = isset($submittedFeatures[$feature->key]) ? true : false;
                $feature->save();
            }

            DB::commit();
            // Log::info('Feature toggles updated successfully');

            // Clear optimization cache after saving feature toggles
            Artisan::call('optimize:clear');

            return redirect(route('finish'))
                ->with('success', __('installer.feature_toggles_saved'));

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error updating feature toggles: ' . $e->getMessage());
            return $redirect->route('feature_toggles')
                ->withInput()
                ->withErrors(['general_error' => __('installer.feature_toggles_save_error') . ': ' . $e->getMessage()]);
        }
    }

    public function finish()
    {
        return view('InstallerEragViews::finish');

    }

    public function finishSave()
    {
        try {
            // Create installation marker
            $installResult = InstalledManager::create();

            if (!$installResult) {
                // Log::error('Failed to create installation marker');
                return redirect()->route('finish')
                    ->with('error', 'Installation could not be completed. Please try again.');
            }

            // Update .env file to set APP_INSTALLED=true
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);

                if ($envContent === false) {
                    // Log::error('Could not read .env file');
                    return redirect()->route('finish')
                        ->with('error', 'Could not update installation status. Please check file permissions.');
                }

                // Check if APP_INSTALLED already exists
                if (strpos($envContent, 'APP_INSTALLED') !== false) {
                    $envContent = preg_replace('/APP_INSTALLED=(.*)/i', 'APP_INSTALLED=true', $envContent);
                } else {
                    $envContent .= "\nAPP_INSTALLED=true\n";
                }

                if (file_put_contents($envPath, $envContent) === false) {
                    // Log::error('Could not write to .env file');
                    return redirect()->route('finish')
                        ->with('error', 'Could not update installation status. Please check file permissions.');
                }
            } else {
                // Log::error('.env file does not exist');
                return redirect()->route('finish')
                    ->with('error', '.env file not found. Installation may be incomplete.');
            }

            // Run optimization commands
            try {
                try {
                    Artisan::call('composer install --optimize-autoloader --no-dev');
                } catch (\Exception $e) {
                    Log::warning('Composer optimizer failed: ' . $e->getMessage());
                }

                // Clear all optimization caches first
                Artisan::call('optimize:clear');

                // Cache standard Laravel components
                Artisan::call('config:cache');
                Artisan::call('route:cache');
                Artisan::call('view:cache');

                // Try Filament-specific commands individually
                try {
                    Artisan::call('icons:cache');
                } catch (\Exception $e) {
                    Log::warning('Icons cache command failed: ' . $e->getMessage());
                }

                try {
                    Artisan::call('filament:cache-components');
                } catch (\Exception $e) {
                    Log::warning('Filament cache components command failed: ' . $e->getMessage());
                }

                // Log::info('Optimization commands completed successfully');
            } catch (\Exception $e) {
                Log::warning('Some optimization commands failed: ' . $e->getMessage());
                // Don't fail the installation if optimization fails
            }

            // Log::info('Installation completed successfully');
            return redirect(URL::to('/'))
                ->with('success', 'Installation completed successfully!');

        } catch (\Exception $e) {
            // Log::error('Error during installation finalization: ' . $e->getMessage());
            return redirect()->route('finish')
                ->with('error', 'Installation failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate logo file with enhanced checks
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function validateLogoFile($file)
    {
        // Configuration
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $minWidth = 100;
        $minHeight = 100;

        // Check if file is valid
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => __('installer.logo_file_corrupted')
            ];
        }

        // Check file size
        if ($file->getSize() > $maxSize) {
            $fileSizeMB = round($file->getSize() / 1024 / 1024, 2);
            $maxSizeMB = round($maxSize / 1024 / 1024, 1);
            return [
                'valid' => false,
                'message' => __('installer.logo_file_too_large') . " ({$fileSizeMB}MB dari maksimal {$maxSizeMB}MB)"
            ];
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            return [
                'valid' => false,
                'message' => __('installer.logo_invalid_format')
            ];
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            return [
                'valid' => false,
                'message' => __('installer.logo_invalid_format')
            ];
        }

        // For SVG files, do basic validation
        if ($extension === 'svg') {
            $content = file_get_contents($file->getRealPath());
            if (strpos($content, '<svg') === false || strpos($content, '</svg>') === false) {
                return [
                    'valid' => false,
                    'message' => __('installer.logo_svg_invalid')
                ];
            }

            // Check for potentially dangerous SVG content
            $dangerousPatterns = ['<script', 'javascript:', 'onload=', 'onerror='];
            foreach ($dangerousPatterns as $pattern) {
                if (stripos($content, $pattern) !== false) {
                    return [
                        'valid' => false,
                        'message' => __('installer.logo_svg_invalid')
                    ];
                }
            }
        } else {
            // For bitmap images, check dimensions
            try {
                $imageInfo = getimagesize($file->getRealPath());
                if ($imageInfo === false) {
                    return [
                        'valid' => false,
                        'message' => __('installer.logo_file_corrupted')
                    ];
                }

                list($width, $height) = $imageInfo;

                if ($width < $minWidth || $height < $minHeight) {
                    return [
                        'valid' => false,
                        'message' => "Dimensi logo terlalu kecil ({$width}x{$height}px). Minimal {$minWidth}x{$minHeight}px."
                    ];
                }

                // Check aspect ratio (warn if not square, but don't fail)
                $ratio = $width / $height;
                if (abs($ratio - 1) > 0.3) {
                    // This is just a warning, still valid
                    Log::info(__('installer.logo_ratio_warning') . ": {$width}x{$height}px (ratio: " . round($ratio, 2) . ")");
                }

            } catch (\Exception $e) {
                return [
                    'valid' => false,
                    'message' => __('installer.logo_processing_failed') . ': ' . $e->getMessage()
                ];
            }
        }

        // Check for file corruption by attempting to read a portion
        try {
            $handle = fopen($file->getRealPath(), 'rb');
            if ($handle === false) {
                return [
                    'valid' => false,
                    'message' => __('installer.logo_file_corrupted')
                ];
            }

            $chunk = fread($handle, 1024); // Read first 1KB
            fclose($handle);

            if ($chunk === false || strlen($chunk) === 0) {
                return [
                    'valid' => false,
                    'message' => __('installer.logo_file_corrupted')
                ];
            }
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => __('installer.logo_validation_failed')
            ];
        }

        return [
            'valid' => true,
            'message' => __('installer.logo_upload_success')
        ];
    }
}
