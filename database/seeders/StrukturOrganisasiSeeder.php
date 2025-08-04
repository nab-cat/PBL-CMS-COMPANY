<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StrukturOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        // Base date for calculating start dates
        $baseDate = Carbon::now()->subYears(2);

        DB::table('struktur_organisasi')->insert([
            // Management Level
            [
                'id_struktur_organisasi' => 1,
                'id_user' => 2, // John Director
                'jabatan' => 'Direktur Utama',
                'deskripsi' => 'Bertanggung jawab atas pengelolaan perusahaan secara keseluruhan.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(2),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_struktur_organisasi' => 2,
                'id_user' => 1, // John Admin
                'jabatan' => 'Chief Technology Officer',
                'deskripsi' => 'Bertanggung jawab atas pengembangan dan implementasi teknologi perusahaan.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(3),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Content Management Team
            [
                'id_struktur_organisasi' => 3,
                'id_user' => 3, // John Editor
                'jabatan' => 'Content Manager',
                'deskripsi' => 'Bertanggung jawab atas pengelolaan konten dan strategi pemasaran.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(4),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_struktur_organisasi' => 4,
                'id_user' => 4, // Johny Editor
                'jabatan' => 'Senior Content Writer',
                'deskripsi' => 'Bertanggung jawab atas penulisan dan pengeditan konten.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(5),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_struktur_organisasi' => 5,
                'id_user' => 5, // Johnes Editor
                'jabatan' => 'Chief Marketing Officer',
                'deskripsi' => 'Bertanggung jawab atas strategi pemasaran dan pengembangan merek.',
                'tanggal_mulai' => $baseDate->copy()->subMonths(12),
                'tanggal_selesai' => $baseDate->copy()->addMonths(1),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Customer Service Team
            [
                'id_struktur_organisasi' => 6,
                'id_user' => 6, // John Customer Service
                'jabatan' => 'Customer Service Manager',
                'deskripsi' => 'Bertanggung jawab atas pengelolaan tim customer service dan kepuasan pelanggan.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(7),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_struktur_organisasi' => 7,
                'id_user' => 7, // Johny Customer Service
                'jabatan' => 'Junior Content Writer',
                'deskripsi' => 'Bertanggung jawab atas penulisan konten untuk media sosial dan blog.',
                'tanggal_mulai' => $baseDate->copy()->subMonths(6),
                'tanggal_selesai' => $baseDate->copy()->addMonths(4),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_struktur_organisasi' => 8,
                'id_user' => 8, // Johnes Customer Service
                'jabatan' => 'Technical Support Representative',
                'deskripsi' => 'Bertanggung jawab atas dukungan teknis dan pemecahan masalah produk.',
                'tanggal_mulai' => $baseDate->copy()->addMonths(9),
                'tanggal_selesai' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}