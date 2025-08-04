<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriUnduhanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_unduhan')->insert([
            [
                'id_kategori_unduhan' => 1,
                'nama_kategori_unduhan' => 'Dokumen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_unduhan' => 2,
                'nama_kategori_unduhan' => 'Formulir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_unduhan' => 3,
                'nama_kategori_unduhan' => 'Panduan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}