<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'unreadNotifications' => $user ? $user->unreadNotifications()->count() : 0,
            ],
            'notifications' => [
                'unread' => $user ? $user->unreadNotifications()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => $notification->data['type'] ?? 'info',
                            'title' => $notification->data['title'] ?? 'Notification',
                            'message' => $notification->data['message'] ?? '',
                            'icon' => $notification->data['icon'] ?? 'bell',
                            'action_url' => $notification->data['action_url'] ?? null,
                            'action_text' => $notification->data['action_text'] ?? null,
                            'created_at' => $notification->created_at,
                            'read_at' => $notification->read_at,
                        ];
                    }) : [],
                'recent' => $user ? $user->notifications()
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => $notification->data['type'] ?? 'info',
                            'title' => $notification->data['title'] ?? 'Notification',
                            'message' => $notification->data['message'] ?? '',
                            'icon' => $notification->data['icon'] ?? 'bell',
                            'action_url' => $notification->data['action_url'] ?? null,
                            'action_text' => $notification->data['action_text'] ?? null,
                            'created_at' => $notification->created_at,
                            'read_at' => $notification->read_at,
                        ];
                    }) : [],
            ],
        ];
    }
}
