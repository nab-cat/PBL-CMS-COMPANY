<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestimoniProdukSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Contoh produk dan user ID
        $produkIds = DB::table('produk')->pluck('id_produk')->toArray();
        $userIds = DB::table('users')->pluck('id_user')->toArray();

        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'isi_testimoni' => $faker->paragraph(2),
                'rating' => $faker->numberBetween(3, 5),
                'status' => 'terpublikasi',
                'id_produk' => $faker->randomElement($produkIds),
                'id_user' => $faker->randomElement($userIds),
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('testimoni_produk')->insert($data);
    }
}
