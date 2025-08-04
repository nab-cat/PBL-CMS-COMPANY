<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestimoniSlider;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    /**
     * Mengambil testimoni dengan data terpisah berdasarkan tipe
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get the single testimoni row with all relations
            $testimoni = TestimoniSlider::with([
                'testimoniProduk.user:id_user,name,foto_profil,email',
                'testimoniProduk.produk:id_produk,nama_produk,slug',
                'testimoniLowongan.user:id_user,name,foto_profil,email',
                'testimoniLowongan.lowongan:id_lowongan,judul_lowongan,slug',
                'testimoniEvent.user:id_user,name,foto_profil,email',
                'testimoniEvent.event:id_event,nama_event,slug',
                'testimoniArtikel.user:id_user,name,foto_profil,email',
                'testimoniArtikel.artikel:id_artikel,judul_artikel,slug'
            ])->first();

            if (!$testimoni) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Testimoni tidak ditemukan'
                ], 404);
            }

            // Prepare response data
            $responseData = [
                'id_testimoni_slider' => $testimoni->id_testimoni_slider,
                'created_at' => $testimoni->created_at,
                'updated_at' => $testimoni->updated_at
            ];

            // Add produk testimoni data if exists
            if ($testimoni->testimoniProduk) {
                $responseData['isi_testimoni_produk'] = $testimoni->testimoniProduk->isi_testimoni;
                $responseData['rating_produk'] = $testimoni->testimoniProduk->rating;
                $responseData['user_produk'] = [
                    'name' => $testimoni->testimoniProduk->user->name ?? 'Anonim',
                    'foto_profil' => $testimoni->testimoniProduk->user->foto_profil ?? null
                ];
                $responseData['produk_nama'] = $testimoni->testimoniProduk->produk->nama_produk ?? null;
                $responseData['produk_slug'] = $testimoni->testimoniProduk->produk->slug ?? null;
            }

            // Add lowongan testimoni data if exists
            if ($testimoni->testimoniLowongan) {
                $responseData['isi_testimoni_lowongan'] = $testimoni->testimoniLowongan->isi_testimoni;
                $responseData['rating_lowongan'] = $testimoni->testimoniLowongan->rating;
                $responseData['user_lowongan'] = [
                    'name' => $testimoni->testimoniLowongan->user->name ?? 'Anonim',
                    'foto_profil' => $testimoni->testimoniLowongan->user->foto_profil ?? null
                ];
                $responseData['lowongan_nama'] = $testimoni->testimoniLowongan->lowongan->judul_lowongan ?? null;
                $responseData['lowongan_slug'] = $testimoni->testimoniLowongan->lowongan->slug ?? null;
            }

            // Add event testimoni data if exists
            if ($testimoni->testimoniEvent) {
                $responseData['isi_testimoni_event'] = $testimoni->testimoniEvent->isi_testimoni;
                $responseData['rating_event'] = $testimoni->testimoniEvent->rating;
                $responseData['user_event'] = [
                    'name' => $testimoni->testimoniEvent->user->name ?? 'Anonim',
                    'foto_profil' => $testimoni->testimoniEvent->user->foto_profil ?? null
                ];
                $responseData['event_nama'] = $testimoni->testimoniEvent->event->nama_event ?? null;
                $responseData['event_slug'] = $testimoni->testimoniEvent->event->slug ?? null;
            }

            // Add artikel testimoni data if exists
            if ($testimoni->testimoniArtikel) {
                $responseData['isi_testimoni_artikel'] = $testimoni->testimoniArtikel->isi_testimoni;
                $responseData['rating_artikel'] = $testimoni->testimoniArtikel->rating;
                $responseData['user_artikel'] = [
                    'name' => $testimoni->testimoniArtikel->user->name ?? 'Anonim',
                    'foto_profil' => $testimoni->testimoniArtikel->user->foto_profil ?? null
                ];
                $responseData['artikel_nama'] = $testimoni->testimoniArtikel->artikel->judul_artikel ?? null;
                $responseData['artikel_slug'] = $testimoni->testimoniArtikel->artikel->slug ?? null;
            }

            // Add type summary
            $responseData['available_types'] = [];
            if ($testimoni->testimoniProduk)
                $responseData['available_types'][] = 'produk';
            if ($testimoni->testimoniLowongan)
                $responseData['available_types'][] = 'lowongan';
            if ($testimoni->testimoniEvent)
                $responseData['available_types'][] = 'event';
            if ($testimoni->testimoniArtikel)
                $responseData['available_types'][] = 'artikel';

            return response()->json([
                'status' => 'success',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat testimoni',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}