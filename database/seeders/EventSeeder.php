<?php

namespace Database\Seeders;

use Illuminate\Http\File;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan faker default (en_US) untuk bs atau catchprase
        $fakerEN = Faker::create('en_US');
        // Gunakan faker Indonesia untuk data lainnya
        $faker = Faker::create('id_ID');

        $events = [];

        // bagian proses image
        $sourcePath = database_path('seeders/seeder_image/');
        $targetPath = 'event-thumbnails';

        // Pastikan folder target ada
        Storage::disk('public')->makeDirectory($targetPath);

        // Ambil semua file di folder seeder_image
        $files = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Generate 20 event dengan Faker
        for ($i = 1; $i <= 20; $i++) {
            $namaEvent = $fakerEN->bs();
            $startDate = $faker->dateTimeBetween('-2 months', '+6 months');
            $endDate = clone $startDate;
            $duration = rand(1, 8); // Durasi dalam jam
            $endDate->modify("+$duration hours");

            // 30% event daring, 70% luring
            $isOnline = $faker->boolean(30);

            if ($isOnline) {
                $lokasi = 'Online via ' . $faker->randomElement(['Zoom', 'Google Meet', 'Microsoft Teams', 'Webex']);
            } else {
                $lokasi = $faker->company() . ', ' . $faker->city();
            }

            // Generate array untuk menyimpan multiple images
            $images = [];

            // Tentukan jumlah gambar untuk event ini (1-3 gambar)
            $imageCount = rand(1, 3);

            // Pilih dan proses beberapa gambar
            for ($j = 0; $j < $imageCount; $j++) {
                // Pilih gambar random
                $originalFile = $files[array_rand($files)];

                // Buat nama baru biar unik
                $newFileName = Str::random(10) . '-' . $originalFile;

                // Copy ke storage
                Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);

                // Tambahkan path gambar ke array images
                $images[] = $targetPath . '/' . $newFileName;
            }

            // Generate deskripsi HTML yang sederhana
            $deskripsi = $this->generateEventDescription($faker, $fakerEN, $images);

            // Generate slug and check for duplicates
            $baseSlug = Str::slug($namaEvent);
            $slug = $baseSlug;
            $counter = 1;

            // Check if slug exists in current batch or database
            while (
                collect($events)->contains('slug', $slug) ||
                DB::table('event')->where('slug', $slug)->exists()
            ) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $events[] = [
                'id_event' => $i,
                'nama_event' => ucfirst($namaEvent),
                'deskripsi_event' => $deskripsi,
                'thumbnail_event' => json_encode($images),
                'lokasi_event' => $lokasi,
                'link_lokasi_event' => 'https://maps.app.goo.gl/TkgARfqzGjPkxFRcA',
                'waktu_start_event' => $startDate,
                'waktu_end_event' => $endDate,
                'jumlah_pendaftar' => rand(0, 200),
                'slug' => $slug,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ];
        }

        DB::table('event')->insert($events);
    }

    /**
     * Generate deskripsi event dengan struktur HTML sederhana
     * @param \Faker\Generator $faker
     * @param \Faker\Generator $fakerEN
     * @param array $images Array of image paths to include in content
     * @return string
     */
    private function generateEventDescription($faker, $fakerEN, $images = [])
    {
        // Opening section dengan deskripsi utama
        $deskripsi = '<h2>' . $fakerEN->sentence(rand(4, 8)) . '</h2>';
        $deskripsi .= '<p>' . $faker->paragraph(rand(15, 25)) . '</p>';

        // Section Overview
        $deskripsi .= '<h3>Overview Event</h3>';
        $deskripsi .= '<p>' . $faker->paragraph(rand(10, 15)) . '</p>';

        // Tambahkan blockquote dengan quote inspiratif
        if ($faker->boolean(80)) {
            $quotes = [
                'Innovation distinguishes between a leader and a follower.',
                'The future belongs to those who believe in the beauty of their dreams.',
                'Success is not final, failure is not fatal: it is the courage to continue that counts.',
                'The only way to do great work is to love what you do.',
                'Learning never exhausts the mind.'
            ];
            $selectedQuote = $faker->randomElement($quotes);
            $deskripsi .= '<blockquote>"' . $selectedQuote . '"<br>&nbsp;— <em>' . $faker->name . ', ' . $faker->jobTitle . '</em></blockquote>';
        }

        // Section Pembicara 
        if ($faker->boolean(85)) {
            $deskripsi .= '<h3>Pembicara</h3>';
            $deskripsi .= '<p>Event ini akan dipandu oleh para ahli terbaik di bidangnya:</p>';
            $deskripsi .= '<ul>';
            for ($i = 0; $i < rand(2, 4); $i++) {
                $deskripsi .= '<li><strong>' . $faker->name() . '</strong><br>';
                $deskripsi .= '<em>' . $faker->jobTitle() . ' di ' . $faker->company() . '</em><br>';
                $deskripsi .= $faker->sentence(rand(8, 12)) . '</li>';
            }
            $deskripsi .= '</ul>';
        }

        // Section Agenda dengan format sederhana
        if ($faker->boolean(90)) {
            $deskripsi .= '<h3>Agenda Kegiatan</h3>';
            $deskripsi .= '<p>Berikut adalah rundown acara yang telah kami persiapkan:</p>';
            
            $deskripsi .= '<ul>';
            $activities = [
                'Registration & Welcome',
                'Keynote Speech',
                'Panel Discussion',
                'Workshop Session',
                'Networking Break',
                'Q&A Session',
                'Closing Ceremony'
            ];
            
            $selectedActivities = $faker->randomElements($activities, rand(4, 6));
            foreach ($selectedActivities as $activity) {
                $deskripsi .= '<li>' . $activity . '</li>';
            }
            $deskripsi .= '</ul>';
        }

        // Section Yang Akan Dipelajari
        if ($faker->boolean(80)) {
            $deskripsi .= '<h3>Apa yang Akan Anda Pelajari?</h3>';
            $deskripsi .= '<p>Dalam event ini, Anda akan mendapatkan insight mendalam tentang:</p>';
            $deskripsi .= '<ol>';
            for ($i = 0; $i < rand(4, 6); $i++) {
                $deskripsi .= '<li><strong>' . $fakerEN->words(rand(3, 5), true) . '</strong><br>';
                $deskripsi .= $faker->sentence(rand(8, 15)) . '</li>';
            }
            $deskripsi .= '</ol>';
        }

        // Section Target Peserta
        if ($faker->boolean(75)) {
            $deskripsi .= '<h3>Target Peserta</h3>';
            $targets = [
                'Entrepreneur dan Startup Founder',
                'Marketing Professional',
                'Digital Marketing Specialist',
                'Business Development Manager',
                'Product Manager',
                'C-Level Executive',
                'Fresh Graduate',
                'Business Owner',
                'Consultant',
                'Project Manager'
            ];
            
            $selectedTargets = $faker->randomElements($targets, rand(3, 5));
            $deskripsi .= '<ul>';
            foreach ($selectedTargets as $target) {
                $deskripsi .= '<li>' . $target . '</li>';
            }
            $deskripsi .= '</ul>';
        }

        // Section Fasilitas
        if ($faker->boolean(85)) {
            $deskripsi .= '<h3>Fasilitas</h3>';
            $facilities = [
                'Sertifikat resmi yang diakui industri',
                'Materi pembelajaran lengkap dalam bentuk digital',
                'Coffee break dan makan siang',
                'Merchandise eksklusif',
                'Networking session dengan para professional',
                'Akses grup komunitas eksklusif',
                'Template dan tools yang siap pakai'
            ];
            
            $selectedFacilities = $faker->randomElements($facilities, rand(4, 6));
            $deskripsi .= '<ul>';
            foreach ($selectedFacilities as $facility) {
                $deskripsi .= '<li>' . $facility . '</li>';
            }
            $deskripsi .= '</ul>';
        }

        // Section Requirements
        if ($faker->boolean(60)) {
            $deskripsi .= '<h3>Persiapan yang Dibutuhkan</h3>';
            $deskripsi .= '<ul>';
            $deskripsi .= '<li>Laptop atau notepad untuk mencatat</li>';
            $deskripsi .= '<li>Koneksi internet yang stabil (untuk event online)</li>';
            $deskripsi .= '<li>Semangat belajar yang tinggi</li>';
            $deskripsi .= '</ul>';
        }

        // Call to Action sederhana
        $deskripsi .= '<h3>Informasi Pendaftaran</h3>';
        $deskripsi .= '<p>Segera daftarkan diri Anda sebelum kuota penuh. Investasi terbaik adalah investasi untuk diri sendiri.</p>';

        // Contact info
        if ($faker->boolean(80)) {
            $deskripsi .= '<h3>Kontak</h3>';
            $deskripsi .= '<p>Untuk informasi lebih lanjut, hubungi kami di:</p>';
            $deskripsi .= '<ul>';
            $deskripsi .= '<li>Email: info@example.com</li>';
            $deskripsi .= '<li>WhatsApp: +62 812-3456-7890</li>';
            $deskripsi .= '<li>Website: www.example.com</li>';
            $deskripsi .= '</ul>';
        }

        return $deskripsi;
    }
}