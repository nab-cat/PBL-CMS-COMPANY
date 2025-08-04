<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContentStatus;
use Illuminate\Http\Request;
use App\Models\TestimoniProduk;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TestimoniProdukResource;

class TestimoniProdukController extends Controller
{
    /**
     * Mengambil semua testimoni untuk produk tertentu (yang terpublikasi)
     * 
     * @param int $produkId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($produkId)
    {
        $testimoni = TestimoniProduk::with('user:id_user,name,foto_profil,email')
            ->where('id_produk', $produkId)
            ->where('status', 'terpublikasi')
            ->orderBy('created_at', 'desc')
            ->get();

        return TestimoniProdukResource::collection($testimoni);
    }

    /**
     * Menyimpan testimoni baru untuk produk
     * 
     * @param Request $request Request dengan data:
     *   - isi_testimoni (string, required): Isi testimoni
     *   - rating (int, required): Rating 1-5
     *   - id_user (int, required): ID user yang memberikan testimoni
     * @param int $produkId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $produkId)
    {
        $request->validate([
            'isi_testimoni' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'id_user' => 'required|exists:users,id_user',
        ]);

        $testimoni = new TestimoniProduk();
        $testimoni->id_produk = $produkId;
        $testimoni->isi_testimoni = $request->isi_testimoni;
        $testimoni->rating = $request->rating;
        $testimoni->id_user = $request->id_user;
        $testimoni->status = 'terpublikasi';
        $testimoni->save();

        return response()->json(['message' => 'Testimoni berhasil dikirim!']);
    }
}
