<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\User;
use App\Http\Resources\LamaranResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\LamaranSubmissionNotification; // Add this import

class LamaranController extends Controller
{
    /**
     * Menyimpan lamaran baru
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\LamaranResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus login untuk mengirim lamaran'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'id_lowongan' => 'required|exists:lowongan,id_lowongan|integer',
                'surat_lamaran' => 'required|file|mimes:pdf,doc,docx|max:5120',
                'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'portfolio' => 'nullable|file|mimes:pdf,doc,docx,zip|max:5120',
                'pesan_pelamar' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify the lowongan exists and is active
            $lowongan = Lowongan::findOrFail($request->id_lowongan);
            $now = now();

            if ($now < $lowongan->tanggal_dibuka || $now > $lowongan->tanggal_ditutup) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lowongan ini sudah ditutup atau belum dibuka'
                ], 422);
            }

            // Use authenticated user's ID (security fix - don't trust frontend)
            $userId = auth()->id();

            // Cek apakah user sudah pernah melamar untuk lowongan ini
            $existingApplication = Lamaran::where('id_user', $userId)
                ->where('id_lowongan', $request->id_lowongan)
                ->first();

            if ($existingApplication) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah pernah melamar untuk lowongan ini'
                ], 422);
            }

            $data = [
                'id_user' => $userId, // Use authenticated user's ID
                'id_lowongan' => $request->id_lowongan,
                'pesan_pelamar' => $request->pesan_pelamar,
                'status_lamaran' => 'Diproses'
            ];

            // Upload surat lamaran with additional security checks
            if ($request->hasFile('surat_lamaran')) {
                $file = $request->file('surat_lamaran');
                // Additional security: check file content
                if (!$this->isValidFile($file)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File surat lamaran tidak valid'
                    ], 422);
                }
                $suratPath = $file->store('lamaran/surat-lamaran', 'public');
                $data['surat_lamaran'] = $suratPath;
            }

            // Upload CV with additional security checks
            if ($request->hasFile('cv')) {
                $file = $request->file('cv');
                if (!$this->isValidFile($file)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File CV tidak valid'
                    ], 422);
                }
                $cvPath = $file->store('lamaran/cv', 'public');
                $data['cv'] = $cvPath;
            }

            // Upload portfolio with additional security checks
            if ($request->hasFile('portfolio')) {
                $file = $request->file('portfolio');
                if (!$this->isValidFile($file)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'File portfolio tidak valid'
                    ], 422);
                }
                $portfolioPath = $file->store('lamaran/portfolio', 'public');
                $data['portfolio'] = $portfolioPath;
            }

            $lamaran = Lamaran::create($data);

            // Send notification to user (fix the parameter issue)
            $user = auth()->user();
            $user->notify(new LamaranSubmissionNotification($lamaran, $lowongan));

            return (new LamaranResource($lamaran))
                ->additional([
                    'status' => 'success',
                    'message' => 'Lamaran berhasil dikirim'
                ])->response()->setStatusCode(201);
        } catch (\Exception $e) {
            \Log::error('Lamaran submission error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim lamaran. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Mengambil lamaran berdasarkan user ID
     * 
     * @param int $userId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getByUserId($userId)
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus login untuk mengakses data ini'
                ], 401);
            }

            // Authorization check - users can only access their own data
            if (auth()->id() != $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk melihat data lamaran ini'
                ], 403);
            }

            $lamaran = Lamaran::with('lowongan')
                ->where('id_user', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return LamaranResource::collection($lamaran)
                ->additional([
                    'status' => 'success',
                    'message' => 'Lamaran berhasil diambil'
                ]);
        } catch (\Exception $e) {
            \Log::error('Get lamaran by user error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lamaran'
            ], 500);
        }
    }

    /**
     * Mengambil detail lamaran berdasarkan ID
     * 
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus login untuk mengakses data ini'
                ], 401);
            }

            $lamaran = Lamaran::with('lowongan', 'user')
                ->findOrFail($id);

            // Authorization check - users can only view their own applications
            if (auth()->id() != $lamaran->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk melihat lamaran ini'
                ], 403);
            }

            return (new LamaranResource($lamaran))
                ->additional([
                    'status' => 'success',
                    'message' => 'Detail lamaran berhasil diambil'
                ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Get lamaran detail error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail lamaran'
            ], 500);
        }
    }

    /**
     * Mengecek apakah user sudah melamar untuk lowongan tertentu
     * 
     * @param int $userId
     * @param int $lowonganId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserApplication($userId, $lowonganId)
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda harus login untuk mengakses data ini'
                ], 401);
            }

            // Authorization check - users can only check their own applications
            if (auth()->id() != $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk memeriksa data lamaran ini'
                ], 403);
            }

            $application = Lamaran::with('lowongan')
                ->where('id_user', $userId)
                ->where('id_lowongan', $lowonganId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($application) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User sudah pernah melamar',
                    'data' => new LamaranResource($application),
                    'has_applied' => true
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User belum pernah melamar',
                    'data' => null,
                    'has_applied' => false
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Check user application error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengecek status lamaran'
            ], 500);
        }
    }

    /**
     * Validate uploaded file for security
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    private function isValidFile($file)
    {
        // Check file size
        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB limit
            return false;
        }

        // Check file extension
        $allowedExtensions = ['pdf', 'doc', 'docx', 'zip'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // Basic MIME type check
        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip'
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        return true;
    }
}
