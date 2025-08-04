<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Http\Resources\Lowongan\LowonganListResource;
use App\Http\Resources\Lowongan\LowonganViewResource;
use App\Enums\ContentStatus;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    /**
     * Mengambil daftar lowongan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 6);
            $lowongan = Lowongan::where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->orderBy('tanggal_dibuka', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Data lowongan berhasil diambil',
                'data' => LowonganListResource::collection($lowongan->items()),
                'meta' => [
                    'current_page' => $lowongan->currentPage(),
                    'last_page' => $lowongan->lastPage(),
                    'per_page' => $lowongan->perPage(),
                    'total' => $lowongan->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lowongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari lowongan berdasarkan query
     * Implementasi sederhana untuk menghindari error
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            $lowonganQuery = Lowongan::where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->where(function ($q) use ($query) {
                    $q->where('judul_lowongan', 'LIKE', "%{$query}%")
                        ->orWhere('deskripsi_pekerjaan', 'LIKE', "%{$query}%");
                    // Hapus bagian lokasi jika kolom tidak ada
                    // ->orWhere('lokasi', 'LIKE', "%{$query}%");
                });

            $lowongan = $lowonganQuery->paginate(10);

            // Return response dengan menggunakan resource
            return response()->json([
                'status' => 'success',
                'message' => 'Hasil pencarian lowongan',
                'data' => LowonganListResource::collection($lowongan->items()),
                'meta' => [
                    'current_page' => $lowongan->currentPage(),
                    'last_page' => $lowongan->lastPage(),
                    'per_page' => $lowongan->perPage(),
                    'total' => $lowongan->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari lowongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil detail lowongan berdasarkan slug
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowonganBySlug($slug)
    {
        try {
            $lowongan = Lowongan::where('slug', $slug)
                ->where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->first();

            if (!$lowongan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lowongan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail lowongan berhasil diambil',
                'data' => new LowonganViewResource($lowongan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail lowongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil detail lowongan berdasarkan ID
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowonganById($id)
    {
        try {
            $lowongan = Lowongan::where('id_lowongan', $id)
                ->where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->first();

            if (!$lowongan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lowongan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail lowongan berhasil diambil',
                'data' => new LowonganViewResource($lowongan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail lowongan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil lowongan terbaru
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostRecentLowongan(Request $request)
    {
        try {
            $limit = $request->query('limit', 1);
            $lowongan = Lowongan::where('status_lowongan', ContentStatus::TERPUBLIKASI)
                ->orderBy('tanggal_dibuka', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data lowongan terbaru berhasil diambil',
                'data' => LowonganListResource::collection($lowongan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lowongan terbaru',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}