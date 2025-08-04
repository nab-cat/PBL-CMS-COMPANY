<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestimoniEventSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Contoh produk dan user ID
        $eventIds = DB::table('event')->pluck('id_event')->toArray();
        $userIds = DB::table('users')->pluck('id_user')->toArray();

        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'isi_testimoni' => $faker->paragraph(4),
                'rating' => $faker->numberBetween(3, 5),
                'status' => 'terpublikasi',
                'id_event' => $faker->randomElement($eventIds),
                'id_user' => $faker->randomElement($userIds),
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('testimoni_event')->insert($data);
    }
}
