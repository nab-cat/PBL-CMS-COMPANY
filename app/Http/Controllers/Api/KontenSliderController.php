<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KontenSlider;
use Illuminate\Http\Request;
use App\Http\Resources\KontenSliderResource;

class KontenSliderController extends Controller
{
    /**
     * Menampilkan daftar konten slider
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $data = KontenSlider::with(['artikel', 'galeri', 'produk', 'event'])->get();

            return KontenSliderResource::collection($data);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data konten slider',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
