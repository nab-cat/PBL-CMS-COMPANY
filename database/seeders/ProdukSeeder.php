<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Http\File;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Path untuk gambar
        $sourcePath = database_path('seeders/seeder_image/');
        $targetPath = 'produk-thumbnails';

        // Buat folder target jika belum ada
        Storage::disk('public')->makeDirectory($targetPath);

        // Ambil file gambar
        $files = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Data dasar produk
        $products = [
            ['nama' => 'Aplikasi Manajemen Keuangan', 'kategori' => 3, 'harga' => 1500000],
            ['nama' => 'Jasa Konsultasi IT', 'kategori' => 2, 'harga' => 500000],
            ['nama' => 'Server Rack', 'kategori' => 1, 'harga' => 15000000],
            ['nama' => 'Sistem Manajemen Inventaris', 'kategori' => 3, 'harga' => 2750000],
            ['nama' => 'Aplikasi HR & Kepegawaian', 'kategori' => 3, 'harga' => 3200000],
            ['nama' => 'Layanan Pengembangan Website', 'kategori' => 2, 'harga' => 5000000],
            ['nama' => 'Maintenance & Support IT', 'kategori' => 2, 'harga' => 2000000],
            ['nama' => 'Networking Kit Enterprise', 'kategori' => 1, 'harga' => 8500000],
            ['nama' => 'Workstation PC Pro', 'kategori' => 1, 'harga' => 12750000],
            ['nama' => 'Sistem Keamanan Data Enterprise', 'kategori' => 3, 'harga' => 4500000],
        ];

        // Daftar link marketplace
        $marketplaces = [
            'https://www.tokopedia.com/',
            'https://shopee.co.id/',
            'https://www.lazada.co.id/',
            'https://www.blibli.com/',
        ];

        // Generate 30 produk
        for ($i = 1; $i <= 30; $i++) {
            $randomProduct = $faker->randomElement($products);
            $createdAt = Carbon::now()->subYear()->addDays(rand(0, 365));

            $images = [];
            $imageCount = rand(1, 3);

            for ($j = 0; $j < $imageCount; $j++) {
                $originalFile = $files[array_rand($files)];
                $newFileName = Str::random(10) . '-' . $originalFile;

                Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);
                $fullPath = Storage::disk('public')->path($targetPath . '/' . $newFileName);
                touch($fullPath, $createdAt->getTimestamp());

                $images[] = $targetPath . '/' . $newFileName;
            }

            $randomName = $randomProduct['nama'] . ' ' . $faker->words(2, true);
            $slug = Str::slug($randomName);

            DB::table('produk')->insert([
                'id_produk' => $i,
                'id_kategori_produk' => $randomProduct['kategori'],
                'nama_produk' => $randomName,
                'thumbnail_produk' => json_encode($images),
                'tampilkan_harga' => $faker->boolean(),
                'harga_produk' => $randomProduct['harga'],
                'slug' => $slug,
                'link_produk' => $faker->randomElement($marketplaces),
                'status_produk' => $faker->randomElement(['terpublikasi', 'tidak terpublikasi']),
                'deskripsi_produk' => $faker->paragraph(10),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, 30)),
            ]);
        }
    }
}
