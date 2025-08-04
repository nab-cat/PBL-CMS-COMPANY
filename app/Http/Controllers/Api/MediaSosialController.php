<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaSosial;
use Illuminate\Http\Request;

class MediaSosialController extends Controller
{
    /**
     * Mengambil daftar media sosial yang aktif
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $mediaSosial = MediaSosial::select('nama_media_sosial', 'link', 'status_aktif')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->nama_media_sosial => [
                        'active' => $item->status_aktif,
                        'link' => $item->link
                    ]
                ];
            });

        return response()->json([
            'data' => $mediaSosial,
        ]);
    }
}
