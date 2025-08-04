<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Essential seeders - check if data already exists
        $this->runEssentialSeeders();

        $this->call([
            FilamentUserSeeder::class,
                // DummyUser::class,
            KategoriUnduhanSeeder::class,
            KategoriProdukSeeder::class,
            KategoriGaleriSeeder::class,
            KategoriArtikelSeeder::class,


            UnduhanSeeder::class,
            ProdukSeeder::class,
            GaleriSeeder::class,


            MediaSosialSeeder::class,
            FeedbackSeeder::class,
                // TestimoniSliderSeeder::class,
                // LowonganSeeder::class,
            EventSeeder::class,
            ArtikelSeeder::class,


                // LamaranSeeder::class,
            MitraSeeder::class,
            StrukturOrganisasiSeeder::class,
            CaseStudySeeder::class,
            TestimoniProdukSeeder::class,
            TestimoniArtikelSeeder::class,
            TestimoniEventSeeder::class,
                // TestimoniLowonganSeeder::class,
            TestimoniSliderSeeder::class,
        ]);
    }

    /**
     * Run essential seeders with existence checks
     */
    private function runEssentialSeeders(): void
    {
        // Create necessary storage directories for Filament resources
        $this->createStorageDirectories();

        // Check and run ShieldSeeder if no roles exist
        if (
            class_exists('Spatie\Permission\Models\Role') &&
            \Spatie\Permission\Models\Role::count() === 0
        ) {
            $this->call(ShieldSeeder::class);
        }

        // Check and run ProfilPerusahaanSeeder if no company profile exists
        if (\App\Models\ProfilPerusahaan::count() === 0) {
            $this->call(ProfilPerusahaanSeeder::class);
        }

        // Check and run KontenSliderSeeder if no slider content exists
        if (\App\Models\KontenSlider::count() === 0) {
            $this->call(KontenSliderSeeder::class);
        }

        // Check and run FeatureToggleSeeder if no feature toggles exist
        if (\App\Models\FeatureToggle::count() === 0) {
            $this->call(FeatureToggleSeeder::class);
        }
    }

    /**
     * Create storage directories for Filament resources
     */
    private function createStorageDirectories(): void
    {
        $directories = [
            'profile-photos',
            'artikel-thumbnails',
            'case-study-thumbnails',
            'event-thumbnails',
            'galeri-thumbnails',
            'lamaran-cv',
            'lamaran-portfolio',
            'lowongan-images',
            'media-social-icons',
            'mitra-logos',
            'mitra-dokumen/siup',
            'mitra-dokumen/npwp',
            'produk-thumbnails',
            'logo-perusahaan',
            'perusahaan-images',
            'testimoni-images',
            'unduhan-files',
        ];

        foreach ($directories as $directory) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $this->command->info('Storage directories created successfully.');
    }
}