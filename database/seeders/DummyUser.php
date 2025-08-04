<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class DummyUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Siapkan gambar dari folder seeder
        $sourcePath = database_path('seeders/seeder_image_foto_profil');
        $targetPath = 'profile-photos';
        Storage::disk('public')->makeDirectory($targetPath);

        // Ambil daftar file gambar di folder source
        $files = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Fungsi ambil dan simpan 1 gambar acak, return path-nya
        $getRandomProfilePicture = function () use ($files, $sourcePath, $targetPath) {
            $originalFile = $files[array_rand($files)];
            $newFileName = Str::random(12) . '-' . $originalFile;
            Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);
            return $targetPath . '/' . $newFileName;
        };

        // Buat user pertama manual (bisa pakai foto profil juga jika ingin)
        User::create([
            'name' => 'Test User',
            'email' => 'usertest@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'foto_profil' => $getRandomProfilePicture(), // tambahkan foto profil random
        ]);

        $faker = \Faker\Factory::create();

        // Buat 20 user dummy dengan foto profil random
        for ($i = 0; $i < 20; $i++) {
            // Generate unique email
            $email = $faker->unique()->safeEmail();

            // Skip if this email already exists in the database
            if (User::where('email', $email)->exists()) {
                continue;
            }

            User::create([
                'name' => $faker->name(),
                'email' => $email,
                'password' => Hash::make('password123'),
                'status_kepegawaian' => $faker->randomElement(['Kontrak', 'Magang']),
                'email_verified_at' => now(),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'foto_profil' => $getRandomProfilePicture(), // disini juga
            ]);
        }
    }
}
