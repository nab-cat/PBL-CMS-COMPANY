<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeederDev extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
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

        Artisan::call('migrate:fresh', [
            '--force' => true,
        ]);

        $this->call([
            ShieldSeeder::class,
            FilamentUserSeeder::class,
            DummyUser::class,
            KategoriUnduhanSeeder::class,
            KategoriProdukSeeder::class,
            KategoriGaleriSeeder::class,
            KategoriArtikelSeeder::class,


            UnduhanSeeder::class,
            ProdukSeeder::class,
            GaleriSeeder::class,


            ProfilPerusahaanSeeder::class,
            MediaSosialSeeder::class,
            FeedbackSeeder::class,
            LowonganSeeder::class,
            EventSeeder::class,
            ArtikelSeeder::class,


            LamaranSeeder::class,
            KontenSliderSeeder::class,
            MitraSeeder::class,
            StrukturOrganisasiSeeder::class,
            FeatureToggleSeeder::class,
            CaseStudySeeder::class,
            TestimoniProdukSeeder::class,
            TestimoniArtikelSeeder::class,
            TestimoniEventSeeder::class,
            TestimoniLowonganSeeder::class,
            TestimoniSliderSeeder::class,
        ]);
    }
}