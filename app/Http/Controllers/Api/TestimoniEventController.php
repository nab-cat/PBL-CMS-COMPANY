<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContentStatus;
use Illuminate\Http\Request;
use App\Models\TestimoniEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TestimoniEventResource;

class TestimoniEventController extends Controller
{
    /**
     * Mengambil semua testimoni untuk event tertentu (yang terpublikasi)
     * 
     * @param int $eventId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($eventId)
    {
        $testimoni = TestimoniEvent::with('user:id_user,name,foto_profil,email')
            ->where('id_event', $eventId)
            ->where('status', 'terpublikasi')
            ->orderBy('created_at', 'desc')
            ->get();

        return TestimoniEventResource::collection($testimoni);
    }

    /**
     * Menyimpan testimoni baru untuk event
     * 
     * @param Request $request Request dengan data:
     *   - isi_testimoni (string, required): Isi testimoni
     *   - rating (int, required): Rating 1-5
     *   - id_user (int, required): ID user yang memberikan testimoni
     * @param int $eventId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'isi_testimoni' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'id_user' => 'required|exists:users,id_user',
        ]);

        $testimoni = new TestimoniEvent();
        $testimoni->id_event = $eventId;
        $testimoni->isi_testimoni = $request->isi_testimoni;
        $testimoni->rating = $request->rating;
        $testimoni->id_user = $request->id_user;
        $testimoni->status = 'terpublikasi';
        $testimoni->save();

        return response()->json(['message' => 'Testimoni berhasil dikirim!']);
    }
}
