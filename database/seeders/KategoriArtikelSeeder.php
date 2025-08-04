<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_artikel')->insert([
            [
                'id_kategori_artikel' => 1,
                'nama_kategori_artikel' => 'Teknologi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_artikel' => 2,
                'nama_kategori_artikel' => 'Bisnis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_artikel' => 3,
                'nama_kategori_artikel' => 'Tutorial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}