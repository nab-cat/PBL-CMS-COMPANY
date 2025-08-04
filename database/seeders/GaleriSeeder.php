<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Http\File;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $galeries = [];

        // Ambil 5 user pertama dari tabel users menggunakan User model
        try {
            $users = \App\Models\User::orderBy('id_user')->limit(5)->pluck('id_user')->toArray();

            // Jika tidak ada user, skip seeding
            if (empty($users)) {
                $this->command->warn('No users found in the database. Skipping galeri seeding.');
                return;
            }

            // Validasi bahwa semua user ID yang diambil valid
            $validUsers = \App\Models\User::whereIn('id_user', $users)->pluck('id_user')->toArray();
            if (count($validUsers) !== count($users)) {
                $this->command->warn('Some users not found, using only valid users for galeri seeding.');
                $users = $validUsers;
            }

            if (empty($users)) {
                $this->command->warn('No valid users found. Skipping galeri seeding.');
                return;
            }

        } catch (\Exception $e) {
            $this->command->error('Error accessing users table: ' . $e->getMessage());
            return;
        }

        // bagian proses image
        $sourcePath = database_path('seeders/seeder_image/');
        $targetPath = 'galeri-thumbnails';

        // Pastikan folder target ada
        Storage::disk('public')->makeDirectory($targetPath);

        // Ambil semua file di folder seeder_image
        $files = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        $titles = [
            'Kantor Baru',
            'Pelatihan Tahunan',
            'Company Gathering',
            'Peluncuran Produk',
            'Seminar Teknologi',
            'Roadshow 2025',
            'Pertemuan Tim',
            'Booth Exhibition',
            'Kunjungan Industri',
            'Showcase Aplikasi',
            'Fasilitas Kantor',
            'Team Building',
            'Penghargaan Karyawan',
            'Kolaborasi Mitra',
            'Inovasi Terbaru',
            'Pameran Teknologi',
            'Pelatihan Karyawan',
            'Kegiatan Sosial',
            'Perayaan Ulang Tahun',
            'Kegiatan Lingkungan',
            'Peluncuran Website',
            'Kunjungan Pelanggan',
            'Pelatihan Keamanan',
            'Pameran Produk',
            'Kegiatan Olahraga',
            'Pelatihan Manajemen',
            'Kegiatan CSR',
            'Pelatihan Soft Skill',
            'Pelatihan Hard Skill',
            'Pelatihan Kepemimpinan',
            'Pelatihan Komunikasi',
            'Pelatihan Tim Kerja',
            'Pelatihan Kreativitas',
            'Pelatihan Inovasi',
            'Pelatihan Digital Marketing',
            'Pelatihan SEO',
            'Pelatihan Media Sosial',
            'Pelatihan Desain Grafis',
            'Pelatihan Video Editing',
            'Pelatihan Fotografi',
            'Pelatihan Public Speaking',
            'Pelatihan Manajemen Waktu',
            'Pelatihan Negosiasi',
            'Pelatihan Analisis Data',
            'Pelatihan Manajemen Proyek',
            'Pelatihan Pengembangan Diri',
            'Pelatihan Keterampilan Teknis',
            'Pelatihan Keterampilan Interpersonal',
        ];

        for ($i = 1; $i <= 40; $i++) {
            $title = $faker->unique()->randomElement($titles);
            $slug = Str::slug($title);

            // Generate array untuk menyimpan multiple images
            $images = [];

            // Generate random date between 1 year ago and now
            $randomDate = Carbon::now()->subYear()->addDays(rand(0, 365));

            // Pilih dan proses beberapa gambar
            for ($j = 0; $j < 3; $j++) {
                // Pilih gambar random
                $originalFile = $files[array_rand($files)];

                // Buat nama baru biar unik
                $newFileName = Str::random(10) . '-' . $originalFile;

                // Copy ke storage dan set modified time
                $fullPath = Storage::disk('public')->path($targetPath . '/' . $newFileName);
                Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);
                touch($fullPath, $randomDate->timestamp);

                // Tambahkan path gambar ke array images
                $images[] = $targetPath . '/' . $newFileName;
            }

            $galeries[] = [
                'id_galeri' => $i,
                'id_user' => $faker->randomElement($users), // Ambil random dari 5 user pertama
                'id_kategori_galeri' => $faker->numberBetween(1, 3),
                'judul_galeri' => $title,
                'thumbnail_galeri' => json_encode($images),
                'deskripsi_galeri' => $faker->paragraph(rand(1, 3)),
                'slug' => $slug,
                'status_galeri' => $faker->randomElement(['terpublikasi', 'tidak terpublikasi']),
                'jumlah_unduhan' => $faker->numberBetween(5, 1000),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ];
        }

        DB::table('galeri')->insert($galeries);
    }
}