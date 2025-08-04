<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil 5 user pertama dari tabel users menggunakan User model
        try {
            $users = \App\Models\User::orderBy('id_user')->limit(5)->pluck('id_user')->toArray();

            // Jika tidak ada user, skip seeding
            if (empty($users)) {
                $this->command->warn('No users found in the database. Skipping feedback seeding.');
                return;
            }

            // Validasi bahwa semua user ID yang diambil valid
            $validUsers = \App\Models\User::whereIn('id_user', $users)->pluck('id_user')->toArray();
            if (count($validUsers) !== count($users)) {
                $this->command->warn('Some users not found, using only valid users for feedback seeding.');
                $users = $validUsers;
            }

            if (empty($users)) {
                $this->command->warn('No valid users found. Skipping feedback seeding.');
                return;
            }

        } catch (\Exception $e) {
            $this->command->error('Error accessing users table: ' . $e->getMessage());
            return;
        }

        $subjects = [
            'Laporan Bug Aplikasi',
            'Peningkatan Kualitas Layanan',
            'Saran Perbaikan Website',
            'Saran Fitur Baru',
            'Perbaikan Antarmuka',
            'Masukan untuk Produk',
            'Laporan Error',
            'Pertanyaan tentang Layanan',
            'Kendala saat Registrasi',
            'Masalah Login',
            'Kritik untuk Sistem',
            'Kesan Penggunaan Aplikasi',
            'Permintaan Bantuan Teknis'
        ];

        for ($i = 1; $i <= 10; $i++) {
            $hasResponse = $faker->boolean(70);

            $feedbacks[] = [
                'id_feedback' => $i,
                'id_user' => $faker->randomElement($users),
                'tingkat_kepuasan' => $faker->numberBetween(1, 5),
                'subjek_feedback' => $faker->randomElement($subjects),
                'isi_feedback' => $faker->paragraph(rand(1, 3)),
                'tanggapan_feedback' => $hasResponse ? $faker->paragraph(rand(1, 2)) : null,
                'status_feedback' => $hasResponse ? 'terpublikasi' : 'tidak terpublikasi',
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ];
        }

        DB::table('feedback')->insert($feedbacks);
    }
}
