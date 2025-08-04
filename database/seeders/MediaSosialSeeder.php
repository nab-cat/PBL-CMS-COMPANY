<?php

namespace Database\Seeders;

use App\Enums\Contentstatus_aktif;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MediaSosialSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $companyName = 'biiscorp';

        $mediaSosial = [
            [
                'id_media_sosial' => 1,
                'nama_media_sosial' => 'Facebook',
                'link' => 'https://facebook.com/' . $companyName,
                'status_aktif' => true,
            ],
            [
                'id_media_sosial' => 2,
                'nama_media_sosial' => 'Instagram',
                'link' => 'https://instagram.com/' . $companyName,
                'status_aktif' => true,
            ],
            [
                'id_media_sosial' => 3,
                'nama_media_sosial' => 'LinkedIn',
                'link' => 'https://linkedin.com/company/' . $companyName,
                'status_aktif' => true,
            ],
            [
                'id_media_sosial' => 4,
                'nama_media_sosial' => 'Twitter',
                'link' => 'https://twitter.com/' . $companyName,
                'status_aktif' => false,
            ],
            [
                'id_media_sosial' => 5,
                'nama_media_sosial' => 'YouTube',
                'link' => 'https://youtube.com/' . $companyName,
                'status_aktif' => false,
            ],
            [
                'id_media_sosial' => 6,
                'nama_media_sosial' => 'TikTok',
                'link' => 'https://tiktok.com/@' . $companyName,
                'status_aktif' => false,
            ],
            [
                'id_media_sosial' => 7,
                'nama_media_sosial' => 'WhatsApp Business',
                'link' => 'https://wa.me/6281234567890',
                'status_aktif' => false,
            ],
            [
                'id_media_sosial' => 8,
                'nama_media_sosial' => 'Telegram',
                'link' => 'https://t.me/' . $companyName,
                'status_aktif' => true,
            ],
            [
                'id_media_sosial' => 9,
                'nama_media_sosial' => 'GitHub',
                'link' => 'https://github.com/' . $companyName,
                'status_aktif' => true,
            ],
        ];

        DB::table('media_sosial')->insert($mediaSosial);
    }
}