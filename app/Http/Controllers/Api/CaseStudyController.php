<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use App\Models\Mitra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CaseStudy\CaseStudyViewResource;
use App\Http\Resources\CaseStudy\CaseStudyListResource;

class CaseStudyController extends Controller
{
    /**
     * Mengambil daftar case study
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = CaseStudy::with(['mitra'])
                ->where('status_case_study', ContentStatus::TERPUBLIKASI);

            // Filter by mitra if provided
            if ($request->has('mitra')) {
                $query->where('id_mitra', $request->mitra);
            }

            $caseStudy = $query->paginate(10);

            return CaseStudyListResource::collection($caseStudy);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Case Study',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil case study berdasarkan id
     * 
     * @param int $id
     * @return \App\Http\Resources\CaseStudy\CaseStudyViewResource|\Illuminate\Http\JsonResponse
     */
    public function getCaseStudyById($id)
    {
        try {
            $caseStudy = CaseStudy::with(['mitra'])
                ->where('case_study_id', $id)
                ->where('status_case_study', ContentStatus::TERPUBLIKASI)
                ->firstOrFail();

            return new CaseStudyViewResource($caseStudy);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Case Study',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil case study berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\CaseStudy\CaseStudyViewResource|\Illuminate\Http\JsonResponse
     */
    public function getCaseStudyBySlug($slug)
    {
        try {

            $caseStudy = CaseStudy::where('slug_case_study', $slug)
                ->where('status_case_study', ContentStatus::TERPUBLIKASI)
                ->firstOrFail();

            return new CaseStudyViewResource($caseStudy);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Case Study',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari case study berdasarkan judul atau mitra
     * 
     * @param Request $request Request dengan parameter:
     *   - query (string, optional): Kata kunci pencarian untuk judul case study
     *   - mitra_id (int, optional): ID mitra
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $mitraId = $request->input('mitra_id');

        // validasi input
        if (empty($query) && empty($mitraId)) {
            return $this->index($request);
        }


        $CaseStudyQuery = CaseStudy::with(['mitra'])
            ->where('status_case_study', ContentStatus::TERPUBLIKASI);

        // Jika ada query pencarian, artikel akan dicari berdasarkan judul
        if (!empty($query)) {
            $CaseStudyQuery->where(function ($q) use ($query) {
                $q->where('judul_case_study', 'LIKE', "%{$query}%");
            });
        }

        // Jika ada mitraId, case study akan dicari berdasarkan mitra
        if (!empty($mitraId)) {
            $CaseStudyQuery->where('id_mitra', $mitraId);
        }

        $caseStudies = $CaseStudyQuery->orderBy('created_at', 'desc')->paginate(10);

        return CaseStudyListResource::collection($caseStudies);
    }

    /**
     * Mengambil case study terbaru
     * 
     * @return \App\Http\Resources\CaseStudy\CaseStudyViewResource|\Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        try {
            $latestCaseStudy = CaseStudy::with(['mitra'])
                ->where('status_case_study', ContentStatus::TERPUBLIKASI)
                ->latest()
                ->first();

            if (!$latestCaseStudy) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada case study terbaru'
                ], 404);
            }

            return new CaseStudyViewResource($latestCaseStudy);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat case study terbaru',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil semua mitra yang aktif
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMitra()
    {
        try {
            $mitra = Mitra::where('status', 'aktif')
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $mitra
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat mitra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
