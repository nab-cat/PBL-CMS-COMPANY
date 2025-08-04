<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProfilPerusahaan;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilPerusahaan\ProfilPerusahaanViewResource;
use App\Http\Resources\ProfilPerusahaan\ProfilPerusahaanNavbarResource;

class ProfilPerusahaanController extends Controller
{

    /**
     * Mengambil profil perusahaan
     * 
     * @param string Request $request
     * @return \App\Http\Resources\ProfilPerusahaan\ProfilPerusahaanViewResource|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $ProfilPerusahaan = ProfilPerusahaan::query()->firstOrFail();

            return new ProfilPerusahaanViewResource($ProfilPerusahaan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profil Perusahaan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil profil perusahaan untuk navbar
     * 
     * @param string Request $request
     * @return \App\Http\Resources\ProfilPerusahaan\ProfilPerusahaanNavbarResource|\Illuminate\Http\JsonResponse
     */
    public function getDataNavbar(Request $request)
    {
        try {
            $ProfilPerusahaan = ProfilPerusahaan::query()->firstOrFail();

            return new ProfilPerusahaanNavbarResource($ProfilPerusahaan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profil Perusahaan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
