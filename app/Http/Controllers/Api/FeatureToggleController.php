<?php
namespace App\Http\Controllers\Api;

use App\Models\FeatureToggle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeatureToggleController extends Controller
{
    /**
     * Mengambil daftar fitur yang diaktifkan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $features = FeatureToggle::select('key', 'status_aktif')
            ->get()
            ->pluck('status_aktif', 'key');

        return response()->json([
            'data' => $features,
        ]);
    }
}
