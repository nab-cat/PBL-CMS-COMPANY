<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Http\Resources\MitraResource;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    /**
     * Mengambil daftar mitra yang aktif
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $mitra = Mitra::where('status', 'aktif')
                ->orderBy('nama', 'asc')
                ->get();

            return MitraResource::collection($mitra);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // /**
    //  * Mengambil mitra berdasarkan ID
    //  * 
    //  * @param int $id
    //  * @return \App\Http\Resources\MitraResource|\Illuminate\Http\JsonResponse
    //  */
    // public function getMitraById($id)
    // {
    //     try {
    //         $mitra = Mitra::where('status', 'aktif')
    //             ->findOrFail($id);

    //         return new MitraResource($mitra);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Mitra Tidak Ditemukan',
    //             'error' => $e->getMessage()
    //         ], 404);
    //     }
    // }

    // /**
    //  * Mencari mitra berdasarkan nama
    //  * 
    //  * @param Request $request
    //  * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
    //  */
    // public function search(Request $request)
    // {
    //     try {
    //         $query = $request->input('query');

    //         // validasi input, jika tidak ada query maka kembalikan semua mitra
    //         if (empty($query)) {
    //             return $this->index();
    //         }

    //         $mitra = Mitra::where('status', 'aktif')
    //             ->where('nama', 'LIKE', '%' . $query . '%')
    //             ->orderBy('nama', 'asc')
    //             ->get();

    //         // Check if no mitra were found
    //         if ($mitra->isEmpty()) {
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Tidak ada mitra yang sesuai dengan pencarian',
    //                 'data' => []
    //             ], 200);
    //         }

    //         return MitraResource::collection($mitra);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Gagal mencari mitra',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
