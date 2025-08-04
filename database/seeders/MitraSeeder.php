<?php

namespace Database\Seeders;

use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MitraSeeder extends Seeder
{
    public function run(): void
    {
        // bagian proses image
        $sourcePath = database_path('seeders/seeder_image_mitra/');
        $targetPath = 'mitra-logos';
        Storage::disk('public')->makeDirectory($targetPath);

        // Data mitra dengan source image
        $mitraData = [
            [
                'id_mitra' => 1,
                'nama' => 'PT Global Tech Solutions',
                'alamat_mitra' => 'Jl. Raya Kebon Jeruk No. 123, Jakarta Barat',
                'tanggal_kemitraan' => '2023-01-15',
                'source_image' => 'mitra-1.png'
            ],
            [
                'id_mitra' => 2,
                'nama' => 'CV Digital Kreatif',
                'alamat_mitra' => 'Jl. Mangga Dua Raya No. 45, Jakarta Utara',
                'tanggal_kemitraan' => '2023-05-20',
                'source_image' => 'mitra-2.png'
            ],
            [
                'id_mitra' => 3,
                'nama' => 'PT Solusi Andal Indonesia',
                'alamat_mitra' => 'Jl. Sudirman No. 78, Jakarta Pusat',
                'tanggal_kemitraan' => '2024-02-10',
                'source_image' => 'mitra-3.png'
            ],
        ];

        // Proses setiap mitra
        $insertData = [];
        foreach ($mitraData as $mitra) {
            $sourceImagePath = $sourcePath . $mitra['source_image'];

            if (file_exists($sourceImagePath)) {
                // Generate unique filename
                $uniqueId = strtoupper(Str::ulid());
                $newFileName = $uniqueId . '.webp';

                // Copy ke storage menggunakan Storage facade
                Storage::disk('public')->putFileAs($targetPath, new File($sourceImagePath), $newFileName);

                // Siapkan data untuk insert
                $insertData[] = [
                    'id_mitra' => $mitra['id_mitra'],
                    'nama' => $mitra['nama'],
                    'alamat_mitra' => $mitra['alamat_mitra'],
                    'tanggal_kemitraan' => $mitra['tanggal_kemitraan'],
                    'logo' => $targetPath . '/' . $newFileName
                ];
            } else {
                // Jika file tidak ditemukan, gunakan default atau kosong
                $insertData[] = [
                    'id_mitra' => $mitra['id_mitra'],
                    'nama' => $mitra['nama'],
                    'alamat_mitra' => $mitra['alamat_mitra'],
                    'tanggal_kemitraan' => $mitra['tanggal_kemitraan'],
                    'logo' => null
                ];
            }
        }

        // Insert data ke database
        DB::table('mitra')->insert($insertData);
    }
}