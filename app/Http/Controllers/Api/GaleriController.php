<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContentStatus;
use App\Models\Galeri;
use Illuminate\Http\Request;
use App\Models\KategoriGaleri;
use App\Http\Controllers\Controller;
use App\Http\Resources\Galeri\GaleriListResource;
use App\Http\Resources\Galeri\GaleriViewResource;
use App\Http\Resources\Galeri\GaleriDownloadResource;

class GaleriController extends Controller
{
    /**
     * Mengambil daftar galeri
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $query = Galeri::with(['kategoriGaleri', 'user:id_user,name,foto_profil'])
                ->where('status_galeri', ContentStatus::TERPUBLIKASI)
                ->orderBy('created_at', 'desc');

            $galeri = $query->paginate(5);

            return GaleriListResource::collection($galeri);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Galeri',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil galeri berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\Galeri\GaleriViewResource|\Illuminate\Http\JsonResponse
     */
    /**
     * Mengambil galeri berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\Galeri\GaleriViewResource|\Illuminate\Http\JsonResponse
     */
    public function getGaleriBySlug($slug)
    {
        try {
            $galeri = Galeri::with(['kategoriGaleri', 'user:id_user,name,foto_profil'])
                ->where('status_galeri', ContentStatus::TERPUBLIKASI)
                ->where('slug', $slug)
                ->firstOrFail();

            return new GaleriViewResource($galeri);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Galeri Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil galeri berdasarkan id
     * 
     * @param int $id
     * @return \App\Http\Resources\Galeri\GaleriViewResource|\Illuminate\Http\JsonResponse
     */
    public function getGaleriById($id)
    {
        try {
            $galeri = Galeri::with(relations: ['kategoriGaleri', 'user:id_user,name,foto_profil'])
                ->where('status_galeri', ContentStatus::TERPUBLIKASI)
                ->findOrFail(id: $id);

            return new GaleriViewResource($galeri);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Galeri Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil kategori galeri
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
    {
        try {
            $categories = KategoriGaleri::get();
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
     * Mencari galeri berdasarkan judul atau serta kategori
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $categoryId = $request->input('category_id');

            // validasi input, jika tidak ada query dan category_id maka kembalikan semua galeri (karna parameter kosong)
            if (empty($query) && empty($categoryId)) {
                return $this->index($request);
            }

            $galerisQuery = Galeri::with(['kategoriGaleri', 'user:id_user,name,foto_profil'])
                ->where('status_galeri', ContentStatus::TERPUBLIKASI);

            // Jika ada query pencarian, galeri akan dicari berdasarkan judul
            if (!empty($query)) {
                $galerisQuery->where(function ($q) use ($query) {
                    $q->where('judul_galeri', 'LIKE', "%{$query}%");
                });
            }

            // Jika ada category_id, galeri akan dicari berdasarkan kategori
            if (!empty($categoryId)) {
                $galerisQuery->where('id_kategori_galeri', $categoryId);
            }

            $galeries = $galerisQuery->orderBy('created_at', 'desc')->paginate(10);

            // Add a check if no galeries were found
            if ($galeries->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada galeri yang sesuai dengan pencarian',
                    'data' => []
                ], 200);
            }

            return GaleriListResource::collection($galeries);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari galeri',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendownload galeri dan menambah jumlah unduhan
     * 
     * @param int $id
     * @return \App\Http\Resources\Galeri\GaleriDownloadResource|\Illuminate\Http\JsonResponse
     */
    public function downloadGaleri($id)
    {
        try {
            $galeri = Galeri::where('status_galeri', ContentStatus::TERPUBLIKASI)
                ->findOrFail($id);

            // Increment the download counter
            $galeri->increment('jumlah_unduhan');

            return new GaleriDownloadResource($galeri);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Galeri tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
