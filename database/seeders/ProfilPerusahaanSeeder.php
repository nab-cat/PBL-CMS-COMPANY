<?php

namespace Database\Seeders;

use Illuminate\Http\File;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfilPerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $companyName = 'Biiscorp';

        // --- Logo Perusahaan ---
        $logoSourcePath = database_path('seeders/seeder_logo_perusahaan/');
        $logoTargetPath = 'logo-perusahaan';
        $originalLogoFile = 'Logo.png';
        Storage::disk('public')->makeDirectory($logoTargetPath);
        Storage::disk('public')->putFileAs($logoTargetPath, new File("$logoSourcePath/$originalLogoFile"), $originalLogoFile);
        $logoPath = $logoTargetPath . '/' . $originalLogoFile;

        // --- Thumbnail Perusahaan ---
        $imageSourcePath = database_path('seeders/seeder_image/');
        $imageTargetPath = 'thumbnail_perusahaan';
        Storage::disk('public')->makeDirectory($imageTargetPath);

        // Ambil semua nama file gambar di folder
        $files = array_values(array_filter(scandir($imageSourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Ambil 5 gambar secara acak dari folder
        shuffle($files);
        $selectedImages = array_slice($files, 0, 5);

        // Salin ke storage/public dan simpan path-nya
        $thumbnailPaths = [];

        foreach ($selectedImages as $image) {
            $targetName = $image;
            Storage::disk('public')->putFileAs($imageTargetPath, new File("$imageSourcePath/$image"), $targetName);
            $thumbnailPaths[] = "$imageTargetPath/$targetName";
        }


        // --- Data Sejarah ---
        $sejarahPerusahaan = [
            [
                'tahun' => 2018,
                'judul' => 'Pendirian',
                'deskripsi' => 'PT Biiscorp didirikan oleh sekelompok teknisi berpengalaman dengan fokus pada pengembangan perangkat lunak.'
            ],
            [
                'tahun' => 2019,
                'judul' => 'Proyek Pertama',
                'deskripsi' => 'Memperoleh klien besar pertama dan memulai proyek skala nasional di bidang logistik.'
            ],
            [
                'tahun' => 2021,
                'judul' => 'Ekspansi',
                'deskripsi' => 'Membuka kantor cabang di Surabaya dan meluncurkan produk SaaS pertamanya.'
            ],
            [
                'tahun' => 2023,
                'judul' => 'Inovasi',
                'deskripsi' => 'Bertransformasi menjadi perusahaan teknologi dengan layanan AI dan Cloud Infrastructure.'
            ],
        ];

        // --- Visi dan Misi ---
        $visiPerusahaan = sprintf(
            'Menjadi perusahaan teknologi terkemuka yang memberikan solusi inovatif untuk meningkatkan efisiensi dan produktivitas bisnis. ' .
            'Kami bercita-cita untuk %s dan menjadi pemimpin pasar dalam %s pada tahun %d.',
            $faker->sentence(10),
            $faker->words(5, true),
            $faker->numberBetween(2025, 2030)
        );

        $misiPerusahaan = '<p>Menyediakan solusi teknologi yang berkualitas tinggi, memberikan layanan terbaik kepada pelanggan, dan berkontribusi pada perkembangan industri teknologi di Indonesia. Kami berkomitmen untuk:</p><ol><li>Impedit rerum a magnam illum ut voluptas.</li><li>Amet occaecati beatae eos dicta totam ut veniam facere fugit nihil.</li><li>Consequatur aliquid ea tempora ab beatae rerum.</li><li>Quis accusamus aut hic fugit saepe architecto.</li><li>Quod deserunt fugiat voluptatem occaecati inventore sit natus eos ipsa.</li></ol>';

        // --- Insert ke DB ---
        DB::table('profil_perusahaan')->insert([
            [
                'id_profil_perusahaan' => 1,
                'nama_perusahaan' => $companyName,
                'deskripsi_perusahaan' => sprintf(
                    'PT %s adalah perusahaan teknologi yang bergerak di bidang pengembangan software, konsultasi IT, ' .
                    'dan penyediaan solusi teknologi untuk berbagai sektor industri. %s',
                    $companyName,
                    $faker->paragraph(3)
                ),
                'logo_perusahaan' => $logoPath,
                'thumbnail_perusahaan' => json_encode($thumbnailPaths),
                'alamat_perusahaan' => $faker->address,
                'link_alamat_perusahaan' => 'https://maps.app.goo.gl/EAs3t7yqP9LotHiK6',
                'email_perusahaan' => strtolower($companyName) . '@' . $faker->freeEmailDomain,
                'telepon_perusahaan' => $faker->phoneNumber,
                'sejarah_perusahaan' => json_encode($sejarahPerusahaan),
                'visi_perusahaan' => $visiPerusahaan,
                'misi_perusahaan' => $misiPerusahaan,
                'tema_perusahaan' => '#31487A',
            ],
        ]);
    }
}
