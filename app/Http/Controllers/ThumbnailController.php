<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ThumbnailController extends Controller
{
    public function generate(Request $request, $path)
    {
        $width = $request->get('w', 150);
        $height = $request->get('h', 150);
        $quality = $request->get('q', 80);

        // Decode path
        $imagePath = base64_decode($path);

        // Validasi path untuk keamanan
        if (!Storage::disk('public')->exists($imagePath)) {
            abort(404);
        }

        // Generate cache key
        $cacheKey = 'thumbnails/' . md5($imagePath . $width . $height . $quality) . '.jpg';

        // Cek apakah thumbnail sudah ada
        if (Storage::disk('public')->exists($cacheKey)) {
            return response()->file(Storage::disk('public')->path($cacheKey), [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000', // 1 year
            ]);
        }

        try {
            // Pastikan direktori thumbnails ada
            Storage::disk('public')->makeDirectory('thumbnails');

            // Buat thumbnail
            $image = Image::make(Storage::disk('public')->path($imagePath));
            $image->fit($width, $height, function ($constraint) {
                $constraint->upsize();
            });

            // Simpan sebagai JPEG dengan kualitas yang ditentukan
            $thumbnailPath = Storage::disk('public')->path($cacheKey);
            $image->save($thumbnailPath, $quality);

            return response()->file($thumbnailPath, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000',
            ]);

        } catch (\Exception $e) {
            // Jika gagal, return gambar asli
            return response()->file(Storage::disk('public')->path($imagePath));
        }
    }
}
