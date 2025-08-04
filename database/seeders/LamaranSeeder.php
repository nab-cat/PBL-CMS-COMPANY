<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LamaranSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $statusOptions = ['Diproses', 'Diterima', 'Ditolak'];
        $lamaran = [];

        // Ambil 5 user pertama dari tabel users menggunakan User model
        try {
            $users = \App\Models\User::orderBy('id_user')->limit(3)->pluck('id_user')->toArray();

            // Jika tidak ada user, skip seeding
            if (empty($users)) {
                $this->command->warn('No users found in the database. Skipping lamaran seeding.');
                return;
            }

            // Validasi bahwa semua user ID yang diambil valid
            $validUsers = \App\Models\User::whereIn('id_user', $users)->pluck('id_user')->toArray();
            if (count($validUsers) !== count($users)) {
                $this->command->warn('Some users not found, using only valid users for lamaran seeding.');
                $users = $validUsers;
            }

            if (empty($users)) {
                $this->command->warn('No valid users found. Skipping lamaran seeding.');
                return;
            }

        } catch (\Exception $e) {
            $this->command->error('Error accessing users table: ' . $e->getMessage());
            return;
        }

        // Generate 30 applications
        for ($i = 1; $i <= 10; $i++) {
            $createdAt = $faker->dateTimeBetween('-3 months', '-1 month');
            $updatedAt = $faker->dateTimeBetween($createdAt, 'now');

            $lamaran[] = [
                'id_lamaran' => $i,
                'id_user' => $faker->randomElement($users), // Ambil random dari 5 user pertama
                'id_lowongan' => $i,
                'pesan_pelamar' => $faker->sentence(10),
                'status_lamaran' => $faker->randomElement($statusOptions),
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        DB::table('lamaran')->insert($lamaran);
    }
}