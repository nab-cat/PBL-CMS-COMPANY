<?php

namespace App\Providers;

use App\Models\User;
use Inertia\Inertia;
use Filament\Facades\Filament;
use App\Models\ProfilPerusahaan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, \App\Http\Responses\LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, \App\Http\Responses\LogoutResponse::class);
        $this->app->bind(RegistrationResponseContract::class, \App\Http\Responses\RegistrationResponse::class);

        // Register API Cache Service
        $this->app->singleton(\App\Services\ApiCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('install-input', \App\Installer\Components\InstallInput::class);
        Blade::component('install-error', \App\Installer\Components\InstallError::class);
        Blade::component('install-select', \App\Installer\Components\InstallSelect::class);

        Vite::prefetch(concurrency: 3);

        // if (app()->environment('production')) {
        //     URL::forceScheme('https');
        // }

        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::user(),
                ];
            },
        ]);

        Inertia::share([
            'theme' => function () {
                try {
                    $profil = ProfilPerusahaan::first();
                    return [
                        'secondary' => $profil?->tema_perusahaan ?? '#31487A',
                    ];
                } catch (\Exception $e) {
                    return [
                        'secondary' => '#31487A',
                    ];
                }
            },
        ]);

        try {
            $profil = ProfilPerusahaan::first();
            $logo = $profil?->logo_perusahaan ?? 'favicon.ico';
            $titlePerusahaan = $profil?->nama_perusahaan ?? 'Sistem Informasi Manajemen';

            // Share values to views
            View::share('logoPerusahaan', $logo);
            View::share('titlePerusahaan', $titlePerusahaan);

            // Set the application name (for title)
            config(['app.name' => $titlePerusahaan]);

            // Set email from name configuration
            config(['mail.from.name' => $titlePerusahaan]);

        } catch (\Exception $e) {
            // Set default values if database is not available
            View::share('logoPerusahaan', 'favicon.ico');
            View::share('titlePerusahaan', 'Sistem Informasi Manajemen');
            config(['app.name' => 'Sistem Informasi Manajemen']);
            config(['mail.from.name' => 'Sistem Informasi Manajemen']);
        }

        // Register model observers for cache invalidation
        if (class_exists('\App\Models\Artikel')) {
            \App\Models\Artikel::observe(\App\Observers\ArtikelObserver::class);
        }

        if (class_exists('\App\Models\Event')) {
            \App\Models\Event::observe(\App\Observers\EventObserver::class);
        }

        if (class_exists('\App\Models\Produk')) {
            \App\Models\Produk::observe(\App\Observers\ProdukObserver::class);
        }

        if (class_exists('\App\Models\CaseStudy')) {
            \App\Models\CaseStudy::observe(\App\Observers\CaseStudyObserver::class);
        }

        if (class_exists('\App\Models\Galeri')) {
            \App\Models\Galeri::observe(\App\Observers\GaleriObserver::class);
        }

        if (class_exists('\App\Models\Unduhan')) {
            \App\Models\Unduhan::observe(\App\Observers\UnduhanObserver::class);
        }

        if (class_exists('\App\Models\Lowongan')) {
            \App\Models\Lowongan::observe(\App\Observers\LowonganObserver::class);
        }

        if (class_exists('\App\Models\Lamaran')) {
            \App\Models\Lamaran::observe(\App\Observers\LamaranObserver::class);
        }

        if (class_exists('\App\Models\Mitra')) {
            \App\Models\Mitra::observe(\App\Observers\MitraObserver::class);
        }

        if (class_exists('\App\Models\FeatureToggle')) {
            \App\Models\FeatureToggle::observe(\App\Observers\FeatureToggleObserver::class);
        }

        if (class_exists('\App\Models\KontenSlider')) {
            \App\Models\KontenSlider::observe(\App\Observers\KontenSliderObserver::class);
        }

        if (class_exists('\App\Models\MediaSosial')) {
            \App\Models\MediaSosial::observe(\App\Observers\MediaSosialObserver::class);
        }

        if (class_exists('\App\Models\ProfilPerusahaan')) {
            \App\Models\ProfilPerusahaan::observe(\App\Observers\ProfilPerusahaanObserver::class);
        }

        if (class_exists('\App\Models\TestimoniSlider')) {
            \App\Models\TestimoniSlider::observe(\App\Observers\TestimoniSliderObserver::class);
        }

        // Register category observers
        if (class_exists('\App\Models\KategoriArtikel')) {
            \App\Models\KategoriArtikel::observe(\App\Observers\KategoriArtikelObserver::class);
        }

        if (class_exists('\App\Models\KategoriProduk')) {
            \App\Models\KategoriProduk::observe(\App\Observers\KategoriProdukObserver::class);
        }

        if (class_exists('\App\Models\KategoriGaleri')) {
            \App\Models\KategoriGaleri::observe(\App\Observers\KategoriGaleriObserver::class);
        }

        if (class_exists('\App\Models\KategoriUnduhan')) {
            \App\Models\KategoriUnduhan::observe(\App\Observers\KategoriUnduhanObserver::class);
        }

        // Register specific testimoni observers
        if (class_exists('\App\Models\TestimoniProduk')) {
            \App\Models\TestimoniProduk::observe(\App\Observers\TestimoniProdukObserver::class);
        }

        if (class_exists('\App\Models\TestimoniArtikel')) {
            \App\Models\TestimoniArtikel::observe(\App\Observers\TestimoniArtikelObserver::class);
        }

        if (class_exists('\App\Models\TestimoniEvent')) {
            \App\Models\TestimoniEvent::observe(\App\Observers\TestimoniEventObserver::class);
        }

        if (class_exists('\App\Models\StrukturOrganisasi')) {
            \App\Models\StrukturOrganisasi::observe(\App\Observers\StrukturOrganisasiObserver::class);
        }

        // Register User observer to handle profile changes that affect struktur organisasi
        if (class_exists('\App\Models\User')) {
            \App\Models\User::observe(\App\Observers\UserObserver::class);
        }

        // Register additional observers for cache invalidation
        if (class_exists('\App\Models\Feedback')) {
            \App\Models\Feedback::observe(\App\Observers\FeedbackObserver::class);
        }
    }
}
