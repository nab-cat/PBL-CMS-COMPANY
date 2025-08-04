<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageMetaController extends Controller
{
    /**
     * Get metadata for a single image
     * 
     * @param string $imagePath
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImageMeta($imagePath)
    {
        try {
            // Decode the path parameter that might have special characters
            $imagePath = urldecode($imagePath);

            // Handle different path formats for gallery images stored in storage/app/public/galeri-thumbnails
            if (strpos($imagePath, 'galeri-thumbnails/') === 0) {
                // Gallery images in storage/app/public/galeri-thumbnails
                $fullPath = Storage::disk('public')->path($imagePath);
            } elseif (strpos($imagePath, 'public/galeri-thumbnails/') === 0) {
                // Path already includes 'public/' prefix
                $fullPath = Storage::disk('public')->path(str_replace('public/', '', $imagePath));
            } elseif (strpos($imagePath, 'public/') === 0) {
                // Other public storage paths
                $fullPath = Storage::disk('public')->path(str_replace('public/', '', $imagePath));
            } else {
                // Default to storage/app/public
                $fullPath = Storage::disk('public')->path($imagePath);
            }

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Image not found',
                    'path' => $imagePath
                ], 404);
            }

            $imageInfo = getimagesize($fullPath);
            if (!$imageInfo) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Invalid image file'
                ], 400);
            }

            $fileSize = filesize($fullPath);
            $fileCreated = filectime($fullPath);
            $fileModified = filemtime($fullPath);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'type' => $imageInfo['mime'],
                    'size' => $fileSize,
                    'size_formatted' => $this->formatBytes($fileSize),
                    'resolution' => $imageInfo[0] . 'x' . $imageInfo[1],
                    'aspect_ratio' => round($imageInfo[0] / $imageInfo[1], 2),
                    'bits' => $imageInfo['bits'] ?? null,
                    'channels' => $imageInfo['channels'] ?? null,
                    'file_created' => date('Y-m-d H:i:s', $fileCreated),
                    'file_modified' => date('Y-m-d H:i:s', $fileModified),
                    'path' => $imagePath
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to retrieve image metadata',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get metadata for multiple images from a gallery
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBulkImageMeta(Request $request)
    {
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|string'
            ]);

            $images = $request->input('images');
            $metadata = [];

            foreach ($images as $index => $imagePath) {
                try {
                    // Decode the path parameter
                    $imagePath = urldecode($imagePath);

                    // Handle different path formats for gallery images stored in storage/app/public/galeri-thumbnails
                    if (strpos($imagePath, 'galeri-thumbnails/') === 0) {
                        // Gallery images in storage/app/public/galeri-thumbnails
                        $fullPath = Storage::disk('public')->path($imagePath);
                    } elseif (strpos($imagePath, 'public/galeri-thumbnails/') === 0) {
                        // Path already includes 'public/' prefix
                        $fullPath = Storage::disk('public')->path(str_replace('public/', '', $imagePath));
                    } elseif (strpos($imagePath, 'public/') === 0) {
                        // Other public storage paths
                        $fullPath = Storage::disk('public')->path(str_replace('public/', '', $imagePath));
                    } else {
                        // Default to storage/app/public
                        $fullPath = Storage::disk('public')->path($imagePath);
                    }

                    if (!file_exists($fullPath)) {
                        $metadata[$index] = [
                            'error' => 'Image not found',
                            'path' => $imagePath
                        ];
                        continue;
                    }

                    $imageInfo = getimagesize($fullPath);
                    if (!$imageInfo) {
                        $metadata[$index] = [
                            'error' => 'Invalid image file',
                            'path' => $imagePath
                        ];
                        continue;
                    }

                    $fileSize = filesize($fullPath);
                    $fileCreated = filectime($fullPath);
                    $fileModified = filemtime($fullPath);

                    $metadata[$index] = [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1],
                        'type' => $imageInfo['mime'],
                        'size' => $fileSize,
                        'size_formatted' => $this->formatBytes($fileSize),
                        'resolution' => $imageInfo[0] . 'x' . $imageInfo[1],
                        'aspect_ratio' => round($imageInfo[0] / $imageInfo[1], 2),
                        'bits' => $imageInfo['bits'] ?? null,
                        'channels' => $imageInfo['channels'] ?? null,
                        'file_created' => date('Y-m-d H:i:s', $fileCreated),
                        'file_modified' => date('Y-m-d H:i:s', $fileModified),
                        'path' => $imagePath
                    ];
                } catch (\Exception $e) {
                    $metadata[$index] = [
                        'error' => 'Failed to process image',
                        'message' => $e->getMessage(),
                        'path' => $imagePath
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $metadata
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to retrieve bulk image metadata',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     * 
     * @param int $size
     * @param int $precision
     * @return string
     */
    private function formatBytes($size, $precision = 2)
    {
        if ($size <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        $unitIndex = min(floor($base), count($units) - 1);

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[$unitIndex];
    }
}