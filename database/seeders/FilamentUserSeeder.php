<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;

class FilamentUserSeeder extends Seeder
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

        $files = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Fungsi ambil dan simpan 1 gambar acak
        $getRandomProfilePicture = function () use ($files, $sourcePath, $targetPath) {
            $originalFile = $files[array_rand($files)];
            $newFileName = Str::random(12) . '-' . $originalFile;
            Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);
            return $targetPath . '/' . $newFileName;
        };

        try {
            // Check if admin user already exists - use User model properly
            $adminEmail = 'admin@example.com';
            $adminUser = User::where('email', $adminEmail)->first();

            if (!$adminUser) {
                // Admin user - only create if doesn't exist
                $adminUser = User::create([
                    'name' => 'John Admin',
                    'email' => $adminEmail,
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            // Pastikan $adminUser adalah instance User model yang valid
            if ($adminUser && $adminUser instanceof User && $adminUser->exists) {
                $adminUser->assignRole('super_admin');
            }

            // Director user - check if exists first
            $directorEmail = 'director@example.com';
            $directorUser = User::where('email', $directorEmail)->first();

            if (!$directorUser) {
                $directorUser = User::create([
                    'name' => 'John Director',
                    'email' => $directorEmail,
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            // Pastikan $directorUser adalah instance User model yang valid
            if ($directorUser && $directorUser instanceof User && $directorUser->exists) {
                $directorUser->assignRole('Director');
            }

            // Content Management users - create if they don't exist
            $editor1 = User::where('email', 'editor1@example.com')->first();
            if (!$editor1) {
                $editor1 = User::create([
                    'name' => 'John Editor',
                    'email' => 'editor1@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            $editor2 = User::where('email', 'editor2@example.com')->first();
            if (!$editor2) {
                $editor2 = User::create([
                    'name' => 'Johny Editor',
                    'email' => 'editor2@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            $editor3 = User::where('email', 'editor3@example.com')->first();
            if (!$editor3) {
                $editor3 = User::create([
                    'name' => 'Johnes Editor',
                    'email' => 'editor3@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'status' => 'nonaktif',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            // Assign roles dengan validasi
            if ($editor1 && $editor1 instanceof User && $editor1->exists) {
                $editor1->assignRole('Content Management');
            }
            if ($editor2 && $editor2 instanceof User && $editor2->exists) {
                $editor2->assignRole('Content Management');
            }
            if ($editor3 && $editor3 instanceof User && $editor3->exists) {
                $editor3->assignRole('Content Management');
            }

            // Customer Service users - create if they don't exist
            $cs1 = User::where('email', 'cs1@example.com')->first();
            if (!$cs1) {
                $cs1 = User::create([
                    'name' => 'John Customer Service',
                    'email' => 'cs1@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }
            $cs2 = User::where('email', 'cs2@example.com')->first();
            if (!$cs2) {
                $cs2 = User::create([
                    'name' => 'Johny Customer Service',
                    'email' => 'cs2@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'status' => 'nonaktif',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }
            $cs3 = User::where('email', 'cs3@example.com')->first();
            if (!$cs3) {
                $cs3 = User::create([
                    'name' => 'Johnes Customer Service',
                    'email' => 'cs3@example.com',
                    'password' => bcrypt('password123'),
                    'status_kepegawaian' => 'Tetap',
                    'email_verified_at' => now(),
                    'foto_profil' => $getRandomProfilePicture(),
                ]);
            }

            // Assign roles dengan validasi
            if ($cs1 && $cs1 instanceof User && $cs1->exists) {
                $cs1->assignRole('Customer Service');
            }
            if ($cs2 && $cs2 instanceof User && $cs2->exists) {
                $cs2->assignRole('Customer Service');
            }
            if ($cs3 && $cs3 instanceof User && $cs3->exists) {
                $cs3->assignRole('Customer Service');
            }

        } catch (\Exception $e) {
            // Log error but don't stop the seeding process
            \Log::warning('FilamentUserSeeder error: ' . $e->getMessage());

            // If there's an error, at least try to create a basic admin user
            if (!User::where('email', 'admin@example.com')->exists()) {
                try {
                    $basicAdmin = User::create([
                        'name' => 'Admin User',
                        'email' => 'admin@example.com',
                        'password' => bcrypt('password123'),
                        'status_kepegawaian' => 'Tetap',
                        'email_verified_at' => now(),
                    ]);
                    $basicAdmin->assignRole('super_admin');
                } catch (\Exception $fallbackError) {
                    \Log::error('Failed to create fallback admin user: ' . $fallbackError->getMessage());
                }
            }
        }
    }
}
