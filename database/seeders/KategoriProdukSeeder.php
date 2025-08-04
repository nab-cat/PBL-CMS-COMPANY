<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_produk')->insert([
            [
                'id_kategori_produk' => 1,
                'nama_kategori_produk' => 'Elektronik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_produk' => 2,
                'nama_kategori_produk' => 'Jasa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_kategori_produk' => 3,
                'nama_kategori_produk' => 'Software',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}