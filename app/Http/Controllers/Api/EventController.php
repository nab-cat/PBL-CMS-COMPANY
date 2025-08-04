<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Resources\Events\EventListResource;
use App\Http\Resources\Events\EventViewResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EventRegistrationNotification;
use App\Notifications\EventCancellationNotification;
use App\Notifications\EventReminderNotification;

class EventController extends Controller
{
    /**
     * Mengambil daftar event
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $events = Event::with('users')
                ->where('waktu_start_event', '>', Carbon::now())
                ->orderBy('waktu_start_event', 'asc')
                ->paginate(10);

            return EventListResource::collection($events);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil daftar event berdasarkan slug
     * 
     * @param string $slug
     * @return \App\Http\Resources\Events\EventViewResource|\Illuminate\Http\JsonResponse
     */
    public function getEventBySlug($slug)
    {
        try {
            // Load users to determine registration status
            $event = Event::with('users')
                ->where('slug', $slug)
                ->firstOrFail();
            return new EventViewResource($event);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /** 
     * Mengambil daftar event berdasarkan id
     * 
     * @param int $id
     * @return \App\Http\Resources\Events\EventViewResource|\Illuminate\Http\JsonResponse
     */
    public function getEventById($id)
    {
        try {
            // Load users to determine registration status
            $event = Event::with('users')->findOrFail($id);
            return new EventViewResource($event);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event Tidak Ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil event terbaru
     * 
     * @return \App\Http\Resources\Events\EventListResource|\Illuminate\Http\JsonResponse
     */
    public function getMostRecentEvent()
    {
        try {
            $event = Event::with('users')->orderBy('waktu_start_event', 'desc')->first();

            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada event tersedia'
                ], 404);
            }

            return new EventListResource($event);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil event terbaru untuk navbar
     * 
     * @return \App\Http\Resources\Events\EventListResource|\Illuminate\Http\JsonResponse
     */
    public function getNavbarRecentEvent()
    {
        try {
            $event = Event::with('users')->orderBy('waktu_start_event', 'desc')->first();

            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada event tersedia'
                ], 404);
            }

            return new EventListResource($event);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Memuat Event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari event berdasarkan judul atau lokasi
     * 
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            // validasi input, jika tidak ada query maka kembalikan semua event
            if (empty($query)) {
                return $this->index();
            }

            $eventsQuery = Event::with('users')->where(function ($q) use ($query) {
                $q->where('nama_event', 'LIKE', '%' . $query . '%')
                    ->orWhere('lokasi_event', 'LIKE', '%' . $query . '%')
                    ->orWhere('deskripsi_event', 'LIKE', '%' . $query . '%');
            });

            $events = $eventsQuery->orderBy('waktu_start_event', 'asc')->paginate(10);

            // Check if no events were found
            if ($events->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada event yang sesuai dengan pencarian',
                    'data' => []
                ], 200);
            }

            return EventListResource::collection($events);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendaftarkan user yang terautentikasi ke event
     * 
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, $slug)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $event = Event::where('slug', $slug)->firstOrFail();

        if ($event->isUserRegistered($user->id_user)) {
            return response()->json(['status' => 'error', 'message' => 'Sudah terdaftar'], 400);
        }

        $event->users()->attach($user->id_user);
        $event->increment('jumlah_pendaftar');
        $user->notify(new EventRegistrationNotification($event));

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mendaftar event',
            'jumlah_pendaftar' => $event->fresh()->jumlah_pendaftar,
            'is_registered' => true,
        ]);
    }

    /**
     * Membatalkan pendaftaran user yang terautentikasi dari event
     * 
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregister(Request $request, $slug)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $event = Event::where('slug', $slug)->firstOrFail();

        if (!$event->isUserRegistered($user->id_user)) {
            return response()->json(['status' => 'error', 'message' => 'Belum terdaftar'], 400);
        }

        $event->users()->detach($user->id_user);
        $event->decrement('jumlah_pendaftar');
        $user->notify(new EventCancellationNotification($event));

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran dibatalkan',
            'jumlah_pendaftar' => $event->fresh()->jumlah_pendaftar,
            'is_registered' => false,
        ]);
    }

    /**
     * Mengecek apakah user yang terautentikasi sudah terdaftar di event
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRegistration($slug)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'is_registered' => false
            ], 401);
        }

        try {
            $event = Event::where('slug', $slug)->firstOrFail();
            $isRegistered = $event->isUserRegistered($user->id_user);

            return response()->json([
                'status' => 'success',
                'is_registered' => $isRegistered
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found',
                'is_registered' => false
            ], 404);
        }
    }
}
