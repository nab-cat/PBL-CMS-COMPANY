<?php

use Illuminate\Support\Facades\Route;
use App\Installer\Controllers\InstallerController;
use App\Installer\Controllers\DatabaseController;
use App\Installer\Controllers\DatabaseTestController;
use App\Installer\Middleware\CheckDatabaseConnectionMiddleware;
use App\Installer\Middleware\SetInstallerLocale;

Route::get('install-app', function () {
    return redirect(route('welcome'));
});

Route::group(['middleware' => ['installCheck', SetInstallerLocale::class], 'prefix' => 'install-app'], function () {

    // Language switching route
    Route::get('language/{locale}', [InstallerController::class, 'switchLanguage'])->name('installer.language');

    Route::get('welcome', [InstallerController::class, 'welcome'])->name('welcome');
    Route::post('welcome-continue', [InstallerController::class, 'welcomeContinue'])->name('welcome_continue');

    Route::get('requirements-permissions', [InstallerController::class, 'index'])->name('installs');
    Route::post('install-check', [InstallerController::class, 'install_check'])->name('install_check');

    Route::get('database-import', [DatabaseController::class, 'databaseImport'])->name('database_import');
    Route::post('save-wizard', [DatabaseController::class, 'saveWizard'])->name('saveWizard');
    Route::post('test-database-connection', [DatabaseTestController::class, 'testConnection'])->name('test_database_connection');
    Route::post('test-email-connection', [DatabaseController::class, 'testEmailConnection'])->name('test_email_connection');

    Route::middleware([CheckDatabaseConnectionMiddleware::class])->group(function () {
        Route::get('profil-perusahaan', [InstallerController::class, 'profilPerusahaan'])->name('profil_perusahaan');
        Route::post('profil-perusahaan-save', [InstallerController::class, 'saveProfilPerusahaan'])->name('saveProfilPerusahaan');

        Route::get('super-admin-config', [InstallerController::class, 'superAdminConfig'])->name('super_admin_config');
        Route::post('super-admin-save', [InstallerController::class, 'saveSuperAdmin'])->name('saveSuperAdmin');

        Route::get('user-roles-list', [InstallerController::class, 'userRolesList'])->name('user_roles_list');

        Route::get('feature-toggles', [InstallerController::class, 'featureToggles'])->name('feature_toggles');
        Route::post('feature-toggles-save', [InstallerController::class, 'saveFeatureToggles'])->name('saveFeatureToggles');

        Route::get('finish', [InstallerController::class, 'finish'])->name('finish');
        Route::get('finish-save', [InstallerController::class, 'finishSave'])->name('finishSave');
    });
});