<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UnduhanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $unduhan = [];

        // Setup paths for files
        $sourcePath = database_path('seeders/seeder_file/');
        $targetPath = 'unduhan-files';
        Storage::disk('public')->makeDirectory($targetPath);

        // Get all files from the seeder_file directory
        $availableFiles = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        $dokumenTemplates = [
            'Panduan Pengguna' => ['extension' => 'pdf', 'description' => 'Dokumen panduan untuk pengguna aplikasi perusahaan'],
            'Formulir Pendaftaran' => ['extension' => 'doc', 'description' => 'Formulir untuk pendaftaran layanan atau keanggotaan'],
            'Laporan Tahunan' => ['extension' => 'pdf', 'description' => 'Laporan keuangan dan kinerja perusahaan tahunan'],
            'Ketentuan Layanan' => ['extension' => 'pdf', 'description' => 'Dokumen berisi ketentuan penggunaan layanan'],
            'Panduan Instalasi' => ['extension' => 'pdf', 'description' => 'Petunjuk instalasi aplikasi dan konfigurasi awal'],
            'Template Proposal' => ['extension' => 'doc', 'description' => 'Template untuk pembuatan proposal kerjasama'],
            'Brosur Produk' => ['extension' => 'pdf', 'description' => 'Informasi detail tentang produk dan layanan'],
            'Manual Pengoperasian' => ['extension' => 'pdf', 'description' => 'Panduan pengoperasian perangkat atau sistem'],
            'Checklist Quality Assurance' => ['extension' => 'xls', 'description' => 'Daftar cek untuk menjamin kualitas produk'],
            'Whitepaper Teknis' => ['extension' => 'pdf', 'description' => 'Dokumen teknis mengenai teknologi yang digunakan'],
            'Presentasi Perusahaan' => ['extension' => 'pdf', 'description' => 'Slide presentasi tentang profil perusahaan'],
            'Katalog Layanan' => ['extension' => 'pdf', 'description' => 'Katalog lengkap layanan yang ditawarkan'],
            'Update Sistem' => ['extension' => 'doc', 'description' => 'Pembaruan sistem untuk perangkat lunak'],
            'Sertifikat Keamanan' => ['extension' => 'pdf', 'description' => 'Dokumen sertifikasi keamanan sistem'],
            'FAQ' => ['extension' => 'pdf', 'description' => 'Kumpulan pertanyaan yang sering ditanyakan'],
            'Template Dokumen' => ['extension' => 'doc', 'description' => 'Template standar untuk dokumen perusahaan'],
            'Ebook Edukasi' => ['extension' => 'pdf', 'description' => 'Materi edukasi tentang teknologi terbaru'],
            'Changelog' => ['extension' => 'xls', 'description' => 'Catatan perubahan pada versi terbaru aplikasi'],
            'Standar Operasional' => ['extension' => 'pdf', 'description' => 'Standar prosedur operasional perusahaan'],
            'Rencana Strategis' => ['extension' => 'pdf', 'description' => 'Dokumen rencana strategis jangka panjang']
        ];

        $keys = array_keys($dokumenTemplates);

        for ($i = 1; $i <= 20; $i++) {
            $type = $keys[$i - 1];
            $template = $dokumenTemplates[$type];
            $year = $faker->year('now');


            $matchingFiles = array_filter($availableFiles, function ($file) use ($template) {
                return pathinfo($file, PATHINFO_EXTENSION) == $template['extension'];
            });


            $sourceFile = !empty($matchingFiles)
                ? $matchingFiles[array_rand($matchingFiles)]
                : $availableFiles[array_rand($availableFiles)];


            $newFileName = Str::slug($type) . "-" . $year . "-" . Str::random(5) . "." . pathinfo($sourceFile, PATHINFO_EXTENSION);


            // Store file and set modification time
            Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$sourceFile"), $newFileName);
            $fullPath = Storage::disk('public')->path($targetPath . '/' . $newFileName);
            $createdAt = $faker->dateTimeBetween('-2 years', 'now');
            touch($fullPath, $createdAt->getTimestamp());

            $categoryId = $faker->numberBetween(1, 3); // Categories: 1=Dokumen, 2=Formulir, 3=Panduan
            $isPublished = $faker->boolean(70);
            $createdAt = $faker->dateTimeBetween('-2 years', 'now');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');


            $nameExtra = '';
            if ($faker->boolean(60)) {
                $nameExtra = ' ' . $faker->randomElement(['v' . $faker->numerify('#.#.#'), 'Edisi ' . $faker->randomElement(['Basic', 'Pro', 'Enterprise', 'Standar', 'Premium']), 'Revisi ' . $faker->numberBetween(1, 5)]);
            }


            $descExtra = $faker->boolean(80)
                ? ' ' . $faker->randomElement([
                    'Diperbarui pada ' . $faker->date('F Y') . '.',
                    'Versi terbaru dengan perbaikan ' . $faker->words(3, true) . '.',
                    'Sangat direkomendasikan untuk ' . $faker->words(4, true) . '.',
                    'Dokumen ini mencakup ' . $faker->words(3, true) . ' dan ' . $faker->words(2, true) . '.',
                    'Cocok untuk ' . $faker->words(3, true) . '.'
                ])
                : '';

            $unduhan[] = [
                'id_unduhan' => $i,
                'id_kategori_unduhan' => $categoryId,
                'id_user' => $faker->numberBetween(1, 7),
                'nama_unduhan' => $type . ' ' . $year . $nameExtra,
                'slug' => Str::slug($type . ' ' . $year . $nameExtra),
                'lokasi_file' => $targetPath . '/' . $newFileName,
                'deskripsi' => $template['description'] . '. ' . $faker->sentence(rand(8, 15)) . $descExtra,
                'jumlah_unduhan' => $faker->numberBetween(10, 2000),
                'status_unduhan' => $isPublished ? ContentStatus::TERPUBLIKASI->value : ContentStatus::TIDAK_TERPUBLIKASI->value,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => null,
            ];
        }

        DB::table('unduhan')->insert($unduhan);
    }
}