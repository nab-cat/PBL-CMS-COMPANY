<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use App\Enums\ContentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Menyimpan feedback baru
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\FeedbackResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subjek_feedback' => 'required|string|max:200',
                'isi_feedback' => 'required|string',
                'tingkat_kepuasan' => 'required|integer|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $feedback = Feedback::create([
                'id_user' => auth()->id(),
                'subjek_feedback' => $request->subjek_feedback,
                'isi_feedback' => $request->isi_feedback,
                'tingkat_kepuasan' => $request->tingkat_kepuasan,
            ]);

            return (new FeedbackResource($feedback))
                ->additional([
                    'status' => 'success',
                    'message' => 'Feedback berhasil dikirim',
                ])->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {

        try {
            $query = Feedback::with('user:id_user,name,foto_profil,email')
                ->where('status_feedback', ContentStatus::TERPUBLIKASI)
                ->orderBy('created_at', 'desc');

            $feedback = $query->paginate(10);

            return FeedbackResource::collection($feedback);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
