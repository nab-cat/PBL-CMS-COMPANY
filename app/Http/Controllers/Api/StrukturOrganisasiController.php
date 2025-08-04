<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StrukturOrganisasi;
use App\Http\Resources\StrukturOrganisasiResource;
use Illuminate\Http\Request;

class StrukturOrganisasiController extends Controller
{
    /**
     * Mengambil daftar struktur organisasi yang aktif
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = StrukturOrganisasi::with([
                'user:id_user,name,foto_profil,email,no_hp,status_kepegawaian,status',
                'user.roles:id,name'
            ]);

            // Hanya tampilkan karyawan dengan status aktif dan posisi yang masih aktif
            $query->withActiveUsers()->active();

            $struktur = $query->ordered()->get();

            return StrukturOrganisasiResource::collection($struktur);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat struktur organisasi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan internal'
            ], 500);
        }
    }
}
