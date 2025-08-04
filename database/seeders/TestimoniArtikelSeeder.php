<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestimoniArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil artikel dan user yang ada menggunakan model yang benar
        try {
            $artikelIds = DB::table('artikel')->pluck('id_artikel')->toArray();
            $userIds = DB::table('users')->pluck('id_user')->toArray(); // FIX: gunakan 'id_user' karena itu adalah primary key di tabel users

            // Validasi data tersedia
            if (empty($artikelIds)) {
                $this->command->warn('No articles found in database. Skipping testimoni artikel seeding.');
                return;
            }

            if (empty($userIds)) {
                $this->command->warn('No users found in database. Skipping testimoni artikel seeding.');
                return;
            }

            $data = [];

            for ($i = 1; $i <= 20; $i++) {
                $data[] = [
                    'isi_testimoni' => $faker->paragraph(2),
                    'rating' => $faker->numberBetween(3, 5),
                    'status' => 'terpublikasi',
                    'id_artikel' => $faker->randomElement($artikelIds),
                    'id_user' => $faker->randomElement($userIds),
                    'created_at' => Carbon::now()->subDays(rand(1, 180)),
                    'updated_at' => Carbon::now(),
                ];
            }

            DB::table('testimoni_artikel')->insert($data);

        } catch (\Exception $e) {
            $this->command->error('Error in TestimoniArtikelSeeder: ' . $e->getMessage());
        }
    }
}
