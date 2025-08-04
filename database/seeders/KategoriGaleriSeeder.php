<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriGaleriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_galeri')->insert([
            [
                'id_kategori_galeri' => 1,
                'nama_kategori_galeri' => 'Kegiatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_galeri' => 2,
                'nama_kategori_galeri' => 'Produk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_galeri' => 3,
                'nama_kategori_galeri' => 'Tim',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}