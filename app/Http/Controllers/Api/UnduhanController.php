<?php

namespace App\Http\Controllers\Api;

use App\Models\Unduhan;
use App\Enums\ContentStatus;
use Illuminate\Http\Request;
use App\Models\KategoriUnduhan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Unduhan\UnduhanListResource;
use App\Http\Resources\Unduhan\UnduhanViewResource;
use App\Http\Resources\Unduhan\UnduhanDownloadResource;

class UnduhanController extends Controller
{
    /**
     * Mengambil daftar unduhan
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $query = Unduhan::with(['kategoriUnduhan', 'user:id_user,name,foto_profil'])
                ->where('status_unduhan', ContentStatus::TERPUBLIKASI)
                ->orderBy('created_at', 'desc');

            // Filter berdasarkan kategori jika ada parameter category_id
            if ($request->has('category_id')) {
                $query->where('id_kategori_unduhan', $request->category_id);
            }

            $unduhan = $query->paginate(10);

            return UnduhanListResource::collection($unduhan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil unduhan berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\Unduhan\UnduhanViewResource|\Illuminate\Http\JsonResponse
     */
    public function getUnduhanBySlug($slug)
    {
        try {
            $unduhan = Unduhan::with(['kategoriUnduhan', 'user:id_user,name,foto_profil'])
                ->where('status_unduhan', ContentStatus::TERPUBLIKASI)
                ->where('slug', $slug)
                ->firstOrFail();

            return new UnduhanViewResource($unduhan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unduhan Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil unduhan berdasarkan ID
     * 
     * @param int $id
     * @return \App\Http\Resources\Unduhan\UnduhanViewResource|\Illuminate\Http\JsonResponse
     */
    public function getUnduhanById($id)
    {
        try {
            $unduhan = Unduhan::with(['kategoriUnduhan', 'user:id_user,name,foto_profil'])
                ->where('status_unduhan', ContentStatus::TERPUBLIKASI)
                ->findOrFail($id);

            return new UnduhanViewResource($unduhan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unduhan Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil semua kategori unduhan
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
    {
        try {
            $categories = KategoriUnduhan::get();
            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari unduhan berdasarkan nama atau kategori
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $categoryId = $request->input('category_id');

            // validasi input, jika tidak ada query dan category_id maka kembalikan semua unduhan (karena parameter kosong)
            if (empty($query) && empty($categoryId)) {
                return $this->index($request);
            }

            $unduhanQuery = Unduhan::with(['kategoriUnduhan', 'user:id_user,name,foto_profil'])
                ->where('status_unduhan', ContentStatus::TERPUBLIKASI);

            // Jika ada query pencarian, unduhan akan dicari berdasarkan nama
            if (!empty($query)) {
                $unduhanQuery->where(function ($q) use ($query) {
                    $q->where('nama_unduhan', 'LIKE', "%{$query}%");
                });
            }

            // Jika ada category_id, unduhan akan dicari berdasarkan kategori
            if (!empty($categoryId)) {
                $unduhanQuery->where('id_kategori_unduhan', $categoryId);
            }

            $unduhan = $unduhanQuery->orderBy('created_at', 'desc')->paginate(10);

            // Add a check if no unduhan were found
            if ($unduhan->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada unduhan yang sesuai dengan pencarian',
                    'data' => []
                ], 200);
            }

            return UnduhanListResource::collection($unduhan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil unduhan terpopuler (dengan jumlah unduhan terbanyak)
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getMostDownloaded()
    {
        try {
            $unduhan = Unduhan::with(['kategoriUnduhan', 'user:id_user,name,foto_profil'])
                ->where('status_unduhan', ContentStatus::TERPUBLIKASI)
                ->orderBy('jumlah_unduhan', 'desc')
                ->take(5)
                ->get();

            return UnduhanListResource::collection($unduhan);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Unduhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendownload unduhan dan menambah jumlah unduhan
     * 
     * @param int $id
     * @return \App\Http\Resources\Unduhan\UnduhanDownloadResource|\Illuminate\Http\JsonResponse
     */

    public function downloadUnduhan($id)
    {
        try {
            $unduhan = Unduhan::where('status_unduhan', ContentStatus::TERPUBLIKASI)
                ->findOrFail($id);

            $unduhan->increment('jumlah_unduhan');

            $filePath = $unduhan->lokasi_file; // misal 'unduhan-files/namafile.pdf'

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json(['message' => 'File tidak ditemukan.'], 404);
            }

            // Mengirim file sebagai response download
            return Storage::disk('public')->download($filePath, $unduhan->nama_unduhan . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unduhan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
