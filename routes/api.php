<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MitraController;
use App\Http\Controllers\ImageMetaController;
use App\Http\Controllers\Api\GaleriController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\LamaranController;
use App\Http\Controllers\Api\UnduhanController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\LowonganController;
use App\Http\Controllers\Api\CaseStudyController;
use App\Http\Controllers\Api\TestimoniController;
use App\Http\Controllers\Api\MediaSosialController;
use App\Http\Controllers\Api\KontenSliderController;
use App\Http\Controllers\Api\FeatureToggleController;
use App\Http\Controllers\Api\TestimoniEventController;
use App\Http\Controllers\Api\TestimoniProdukController;
use App\Http\Controllers\Api\ProfilPerusahaanController;
use App\Http\Controllers\Api\TestimoniArtikelController;
use App\Http\Controllers\Api\TestimoniLowonganController;
use App\Http\Controllers\Api\StrukturOrganisasiController;

Route::middleware('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Post feedback (DENGAN AUTENTIKASI)
Route::prefix('feedback')->group(function () {
    Route::get('/', [FeedbackController::class, 'index']);

    // POST route memerlukan autentikasi
    Route::middleware(['web', 'auth'])->group(function () {
        Route::post('/', [FeedbackController::class, 'store']);
    });
});

// Lamaran routes (SEMUA MEMERLUKAN AUTENTIKASI)
Route::middleware(['web', 'auth'])->prefix('lamaran')->group(function () {
    Route::get('/user/{userId}', [LamaranController::class, 'getByUserId']);
    Route::get('/check/{userId}/{lowonganId}', [LamaranController::class, 'checkUserApplication']);
    Route::get('/{id}', [LamaranController::class, 'show']);
    Route::post('/', [LamaranController::class, 'store']);
});


// Artikel
Route::prefix('artikel')->group(function () {

    // Untuk mengambil semua artikel
    Route::get('/', [ArtikelController::class, 'index']);

    // Untuk mengambil semua kategori artikel
    Route::get('/categories', [ArtikelController::class, 'getCategories']);

    // untuk search artikel berdasarkan judul atau isi artikel
    Route::get('/search', [ArtikelController::class, 'search']);

    // untuk mengambil artikel dengan view terbanyak
    Route::get('/most-viewed', [ArtikelController::class, 'getArticleByMostView']);

    // untuk mengambil artikel terbaru
    Route::get('/featured', [ArtikelController::class, 'getFeaturedArticles']);

    // untuk mengambil artikel berdasarkan id
    Route::get('/id/{id}', [ArtikelController::class, 'getArticleById']);

    // untuk mengambil artikel berdasarkan slug
    Route::get('/{slug}', [ArtikelController::class, 'getArticleBySlug']);
});

// Case Study
Route::prefix('case-study')->group(function () {

    // Untuk mengambil semua case study
    Route::get('/', [CaseStudyController::class, 'index']);

    // Untuk mengambil case study berdasarkan id
    Route::get('/id/{id}', [CaseStudyController::class, 'getCaseStudyById']);

    // Untuk mengambil case study terbaru
    Route::get('/latest', [CaseStudyController::class, 'latest']);

    // Untuk mengambil semua mitra aktif
    Route::get('/mitra', [CaseStudyController::class, 'getAllMitra']);

    // Untuk mencari case study
    Route::get('/search', [CaseStudyController::class, 'search']);

    // Untuk mengambil case study berdasarkan slug
    Route::get('/{slug}', [CaseStudyController::class, 'getCaseStudyBySlug']);
});

// Event
Route::prefix('event')->group(function () {

    // Untuk mengambil semua event
    Route::get('/', [EventController::class, 'index']);

    // untuk mengambil event yang baru saja dibuat
    Route::get('/newest', [EventController::class, 'getMostRecentEvent']);

    // untuk mengambil event terbaru untuk navbar
    Route::get('/navbar', [EventController::class, 'getNavbarRecentEvent']);

    // untuk search event berdasarkan nama atau lokasi
    Route::get('/search', [EventController::class, 'search']);

    // untuk mengambil event berdasarkan id
    Route::get('/id/{id}', [EventController::class, 'getEventById']);

    // Event registration routes (require authentication) - MUST come before /{slug} route
    Route::middleware(['web', 'auth'])->group(function () {
        Route::post('/{slug}/register', [EventController::class, 'register']);
        Route::delete('/{slug}/register', [EventController::class, 'unregister']);
        Route::get('/{slug}/check-registration', [EventController::class, 'checkRegistration']);
    });

    Route::get('/{slug}', [EventController::class, 'getEventBySlug']);
});

// Galeri
Route::prefix('galeri')->group(function () {

    // Untuk mengambil semua galeri
    Route::get('/', [GaleriController::class, 'index']);

    // Untuk mengambil semua kategori galeri
    Route::get('/categories', [GaleriController::class, 'getCategories']);

    // untuk search artikel berdasarkan judul atau isi galeri
    Route::get('/search', [GaleriController::class, 'search']);

    // Untuk mengunduh galeri dan menambah jumlah unduhan
    Route::get('/download/{id}', [GaleriController::class, 'downloadGaleri']);

    // untuk mengambil galeri berdasarkan id
    Route::get('/id/{id}', [GaleriController::class, 'getGaleriById']);

    // Untuk mengambil galeri berdasarkan slug
    Route::get('/{slug}', [GaleriController::class, 'getGaleriBySlug']);
});

Route::get('/feature-toggles', [FeatureToggleController::class, 'index']);

// Media Sosial
Route::get('/media-sosial', [MediaSosialController::class, 'index']);

// Testimoni
Route::get('/testimoni/produk/{produkId}', [TestimoniProdukController::class, 'index']);
Route::get('/testimoni/artikel/{artikelId}', [TestimoniArtikelController::class, 'index']);
Route::get('/testimoni/event/{eventId}', [TestimoniEventController::class, 'index']);
Route::get('/testimoni/lowongan/{lowonganId}', [TestimoniLowonganController::class, 'index']);

// Testimoni POST routes require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/testimoni/produk/{produk}', [TestimoniProdukController::class, 'store']);
    Route::post('/testimoni/artikel/{artikel}', [TestimoniArtikelController::class, 'store']);
    Route::post('/testimoni/event/{event}', [TestimoniEventController::class, 'store']);
    Route::post('/testimoni/lowongan/{lowonganId}', [TestimoniLowonganController::class, 'store']);
});

// Testimoni unified endpoint
Route::get('/testimoni', [TestimoniController::class, 'index']);
Route::get('/testimoni/show', [TestimoniController::class, 'show']);


// Mitra
Route::prefix('mitra')->group(function () {
    // Untuk mengambil semua mitra yang aktif
    Route::get('/', [MitraController::class, 'index']);

    // Untuk search mitra berdasarkan nama
    Route::get('/search', [MitraController::class, 'search']);

    // Untuk mengambil mitra berdasarkan id
    Route::get('/{id}', [MitraController::class, 'getMitraById']);
});

// Struktur Organisasi
Route::get('/struktur-organisasi', [StrukturOrganisasiController::class, 'index']);

// Profil Perusahaan
Route::prefix('profil-perusahaan')->group(function () {

    // Untuk mengambil profil perusahaan
    Route::get('/', [ProfilPerusahaanController::class, 'index']);

    // Untuk mengambil profil perusahaan untuk navbar
    Route::get('/navbar', [ProfilPerusahaanController::class, 'getDataNavbar']);
});

// Konten Slider
Route::get('/konten-slider', [KontenSliderController::class, 'index']);


// Produk
Route::prefix('produk')->group(function () {

    // Untuk mengambil semua produk
    Route::get('/', [ProdukController::class, 'index']);

    // untuk search produk berdasarkan nama atau deskripsi
    Route::get('/search', [ProdukController::class, 'search']);

    // untuk mengambil produk berdasarkan id
    Route::get('/id/{id}', [ProdukController::class, 'getProdukById']);

    // untuk mengambil kategori produk
    Route::get('/categories', [ProdukController::class, 'getCategories']);

    // untuk mengambil produk terbaru
    Route::get('/latest', [ProdukController::class, 'latest']);

    // untuk mengambil produk berdasarkan slug
    Route::get('/{slug}', [ProdukController::class, 'getProdukBySlug']);
});



// lowongan
Route::prefix('lowongan')->group(function () {

    // Untuk mengambil semua lowongan
    Route::get('/', [LowonganController::class, 'index']);

    // untuk mengambil lowongan terbaru
    Route::get('/newest', [LowonganController::class, 'getMostRecentLowongan']);

    // untuk search lowongan
    Route::get('/search', [LowonganController::class, 'search']);

    // untuk mengambil lowongan berdasarkan id
    Route::get('/id/{id}', [LowonganController::class, 'getLowonganById']);

    // untuk mengambil lowongan berdasarkan slug
    Route::get('/{slug}', [LowonganController::class, 'getLowonganBySlug']);
});

// Case Study
Route::prefix('case-study')->group(function () {
    // Untuk mengambil semua case study (published)
    Route::get('/', [CaseStudyController::class, 'index']);

    // Untuk search case study
    Route::get('/search', [CaseStudyController::class, 'search']);

    // Untuk mengambil case study berdasarkan id
    Route::get('/id/{id}', [CaseStudyController::class, 'getCaseStudyById']);

    // Untuk mengambil case study berdasarkan slug (termasuk menambah view)
    Route::get('/{slug}', [CaseStudyController::class, 'getCaseStudyBySlug']);
});

// Unduhan
Route::prefix('unduhan')->group(function () {
    // Untuk mengambil semua unduhan yang terpublikasi
    Route::get('/', [UnduhanController::class, 'index']);

    // Untuk mengambil semua kategori unduhan
    Route::get('/categories', [UnduhanController::class, 'getCategories']);

    // Untuk search unduhan berdasarkan nama atau kategori
    Route::get('/search', [UnduhanController::class, 'search']);

    // Untuk mengambil unduhan dengan jumlah download terbanyak
    Route::get('/most-downloaded', [UnduhanController::class, 'getMostDownloaded']);

    // Untuk mengunduh unduhan dan menambah jumlah unduhan
    Route::get('/download/{id}', [UnduhanController::class, 'downloadUnduhan']);

    // Untuk mengambil unduhan berdasarkan id
    Route::get('/id/{id}', [UnduhanController::class, 'getUnduhanById']);

    // Untuk mengambil unduhan berdasarkan slug
    Route::get('/{slug}', [UnduhanController::class, 'getUnduhanBySlug']);
});

// Cache Management (for admin use only)
Route::prefix('cache')->middleware(['web', 'auth'])->group(function () {
    Route::get('/stats', [CacheController::class, 'stats']);
    Route::post('/clear', [CacheController::class, 'clearAll']);
    Route::post('/clear-endpoint', [CacheController::class, 'clearEndpoint']);
    Route::post('/warmup', [CacheController::class, 'warmup']);
});

// Image Metadata
Route::prefix('image-meta')->group(function () {
    // Get metadata for a single image
    Route::get('/{imagePath}', [ImageMetaController::class, 'getImageMeta'])
        ->where('imagePath', '.*'); // Allow paths with slashes and special characters

    // Get metadata for multiple images (bulk)
    Route::post('/bulk', [ImageMetaController::class, 'getBulkImageMeta']);
});
