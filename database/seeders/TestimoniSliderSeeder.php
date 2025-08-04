<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestimoniSliderSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials[] = [
            'id_testimoni_slider' => 1,
            'id_testimoni_produk' => null,
            'id_testimoni_lowongan' => null,
            'id_testimoni_event' => null,
            'id_testimoni_artikel' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        DB::table('testimoni_slider')->insert($testimonials);
    }
}
