<?php

namespace App\Filament\Clusters\TestimoniCluster\Pages;

use App\Filament\Clusters\TestimoniCluster;
use App\Models\TestimoniProduk;
use App\Models\TestimoniLowongan;
use App\Models\TestimoniEvent;
use App\Models\TestimoniArtikel;
use Filament\Pages\Page;

class ManageTestimoni extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.clusters.testimoni-cluster.pages.manage-testimoni';

    protected static ?string $cluster = TestimoniCluster::class;

    protected static ?string $title = 'Dashboard Testimoni';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user
            ? (
                $user->can('view_any_testimoni::artikel')
                || $user->can('view_any_testimoni::event')
                || $user->can('view_any_testimoni::lowongan')
                || $user->can('view_any_testimoni::produk')
            )
            : false;
    }

    public function getStats(): array
    {
        return [
            [
                'label' => 'Testimoni Produk',
                'value' => TestimoniProduk::count(),
                'published' => TestimoniProduk::where('status', 'terpublikasi')->count(),
                'description' => 'Total testimoni produk',
            ],
            [
                'label' => 'Testimoni Lowongan',
                'value' => TestimoniLowongan::count(),
                'published' => TestimoniLowongan::where('status', 'terpublikasi')->count(),
                'description' => 'Total testimoni lowongan',
            ],
            [
                'label' => 'Testimoni Event',
                'value' => TestimoniEvent::count(),
                'published' => TestimoniEvent::where('status', 'terpublikasi')->count(),
                'description' => 'Total testimoni event',
            ],
            [
                'label' => 'Testimoni Artikel',
                'value' => TestimoniArtikel::count(),
                'published' => TestimoniArtikel::where('status', 'terpublikasi')->count(),
                'description' => 'Total testimoni artikel',
            ],
        ];
    }

    public function getRecentTestimoni(): array
    {
        $recent = [];

        // Get recent testimoni from each type
        $recentProduk = TestimoniProduk::latest()->take(3)->get();
        $recentLowongan = TestimoniLowongan::latest()->take(3)->get();
        $recentEvent = TestimoniEvent::latest()->take(3)->get();
        $recentArtikel = TestimoniArtikel::latest()->take(3)->get();

        foreach ($recentProduk as $testimoni) {
            $recent[] = [
                'type' => 'Produk',
                'nama' => $testimoni->nama,
                'rating' => $testimoni->rating,
                'created_at' => $testimoni->created_at,
                'status' => $testimoni->status,
            ];
        }

        foreach ($recentLowongan as $testimoni) {
            $recent[] = [
                'type' => 'Lowongan',
                'nama' => $testimoni->nama,
                'rating' => $testimoni->rating,
                'created_at' => $testimoni->created_at,
                'status' => $testimoni->status,
            ];
        }

        foreach ($recentEvent as $testimoni) {
            $recent[] = [
                'type' => 'Event',
                'nama' => $testimoni->nama,
                'rating' => $testimoni->rating,
                'created_at' => $testimoni->created_at,
                'status' => $testimoni->status,
            ];
        }

        foreach ($recentArtikel as $testimoni) {
            $recent[] = [
                'type' => 'Artikel',
                'nama' => $testimoni->nama,
                'rating' => $testimoni->rating,
                'created_at' => $testimoni->created_at,
                'status' => $testimoni->status,
            ];
        }

        // Sort by created_at descending and take 8
        usort($recent, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return array_slice($recent, 0, 8);
    }
}
