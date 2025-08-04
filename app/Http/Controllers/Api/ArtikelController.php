<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Http\Resources\Articles\ArticleListResource;
use App\Http\Resources\Articles\ArticleViewResource;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    /**
     * Mengambil daftar artikel
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $query = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI)
                ->orderBy('created_at', 'desc');

            // Filter berdasarkan kategori jika ada parameter category_id
            if ($request->has('category_id')) {
                $query->where('id_kategori_artikel', $request->category_id);
            }

            $articles = $query->paginate(10);

            return ArticleListResource::collection($articles);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil artikel berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\Articles\ArticleViewResource|\Illuminate\Http\JsonResponse
     */
    public function getArticleBySlug($slug)
    {
        try {
            $article = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI)
                ->where('slug', $slug)
                ->firstOrFail();

            $article->increment('jumlah_view');
            return new ArticleViewResource($article);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil artikel berdasarkan ID
     * 
     * @param int $id
     * @return \App\Http\Resources\Articles\ArticleViewResource|\Illuminate\Http\JsonResponse
     */
    public function getArticleById($id)
    {
        try {
            $article = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI)
                ->findOrFail($id);

            return new ArticleViewResource($article);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil semua kategori artikel
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories()
    {
        try {
            $categories = KategoriArtikel::get();
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
     * Mencari artikel berdasarkan judul atau kategori
     * 
     * @param Request $request Request dengan parameter:
     *   - query (string, optional): Kata kunci pencarian untuk judul artikel
     *   - category_id (int, optional): ID kategori artikel
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $categoryId = $request->input('category_id');

            // validasi input, jika tidak ada query dan category_id maka kembalikan semua artikel (karna parameter kosong)
            if (empty($query) && empty($categoryId)) {
                return $this->index($request);
            }

            $articlesQuery = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI);

            // Jika ada query pencarian, artikel akan dicari berdasarkan judul
            if (!empty($query)) {
                $articlesQuery->where(function ($q) use ($query) {
                    $q->where('judul_artikel', 'LIKE', "%{$query}%");
                });
            }

            // Jika ada category_id, artikel akan dicari berdasarkan kategori
            if (!empty($categoryId)) {
                $articlesQuery->where('id_kategori_artikel', $categoryId);
            }

            $articles = $articlesQuery->orderBy('created_at', 'desc')->paginate(10);

            // Add a check if no articles were found
            if ($articles->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada artikel yang sesuai dengan pencarian',
                    'data' => []
                ], 200);
            }

            return ArticleListResource::collection($articles);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil artikel dengan jumlah view terbanyak
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getArticleByMostView()
    {
        try {
            $articles = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI)
                ->orderBy('jumlah_view', 'desc')
                ->take(1)
                ->get();

            return ArticleListResource::collection($articles);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Mengambil artikel unggulan berdasarkan view terbanyak
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getFeaturedArticles()
    {
        try {
            $articles = Artikel::with(['kategoriArtikel', 'user'])
                ->where('status_artikel', ContentStatus::TERPUBLIKASI)
                ->orderBy('jumlah_view', 'desc')
                ->take(4)
                ->get();

            return ArticleListResource::collection($articles);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Artikel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
