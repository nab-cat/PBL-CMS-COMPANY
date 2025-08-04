<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckFeatureToggle;
use App\Http\Controllers\ThumbnailController;
use App\Http\Controllers\SEO\RobotsController;
use App\Http\Controllers\SEO\SitemapController;




// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/login', function () {
//     return Inertia::render('Login');
// })->name('login');

Route::get('/login', function () {
    session()->put('url.intended', url()->previous());
    return redirect('/admin/login');
})->name('login');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/', function () {
    return Inertia::render('Home');
})->middleware('checkInstallation')->name('home');

// Notification routes
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/{notification}/read', function ($notificationId) {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');

    Route::get('/notifications', function () {
        return Inertia::render('Notifications/Show');
    })->name('notifications.index');
});

Route::get('/example', function () {
    return Inertia::render('Example');
});


// Rute group untuk artikel

Route::prefix('artikel')
    ->middleware(CheckFeatureToggle::class . ':artikel_module')
    ->group(function () {

        Route::get('/', function () {
            return Inertia::render('Artikel/ListView');
        })->name('artikel.list');

        Route::get('/{slug}', function ($slug) {
            return Inertia::render('Artikel/Show', ['slug' => $slug]);
        })->name('artikel.show');
    });


// Rute group untuk studi kasus
Route::prefix('case-study')
    ->middleware(CheckFeatureToggle::class . ':case_study_module')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('CaseStudy/ListView');
        })->name('studi-kasus.list');

        Route::get('/{slug}', function ($slug) {
            return Inertia::render('CaseStudy/Show', ['slug' => $slug]);
        })->name('studi-kasus.show');
    });

// Rute group untuk event
Route::prefix('event')
    ->middleware(CheckFeatureToggle::class . ':event_module')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('Event/ListView');
        })->name('event.list');

        Route::get('/{slug}', function ($slug) {
            return Inertia::render('Event/Show', ['slug' => $slug]);
        })->name('event.show');
    });


// Rute group untuk galeri
Route::prefix('galeri')
    ->middleware(CheckFeatureToggle::class . ':galeri_module')
    ->group(function () {
        Route::get('/', action: function () {
            return Inertia::render('Galeri/ListView');
        });

        Route::get('/{slug}', action: function ($slug) {
            return Inertia::render('Galeri/Show', ['slug' => $slug]);
        });
    });


// Rute group feedback
Route::prefix('feedback')
    ->middleware(CheckFeatureToggle::class . ':feedback_module')
    ->group(function () {
        Route::get('/', action: function () {
            return Inertia::render('Feedback/Main');
        });
    });

// Rute group Profil Perusahaan
Route::prefix('profil-perusahaan')->group(function () {
    Route::get('/', action: function () {
        return Inertia::render('ProfilPerusahaan/Main');
    });
});

// Rute group untuk Visi Misi
Route::prefix('visi-misi')->group(function () {
    Route::get('/', action: function () {
        return Inertia::render('VisiMisiPerusahaan/Main');
    });
});

// Rute group untuk Sejarah Perusahaan
Route::prefix('sejarah-perusahaan')->group(function () {
    Route::get('/', action: function () {
        return Inertia::render('SejarahPerusahaan/Main');
    });
});

// Rute group untuk Struktur Organisasi
Route::prefix('struktur-organisasi')->group(function () {
    Route::get('/', action: function () {
        return Inertia::render('StrukturOrganisasi/Main');
    });
});

// Rute group untuk unduhan
Route::prefix('unduhan')
    ->middleware(CheckFeatureToggle::class . ':unduhan_module')
    ->group(function () {
        Route::get('/', action: function () {
            return Inertia::render('Unduhan/Main');
        });
    });


// Rute group untuk lowongan
Route::prefix('lowongan')
    ->middleware(CheckFeatureToggle::class . ':lowongan_module')
    ->group(function () {
        Route::get('/', action: function () {
            return Inertia::render('Lowongan/ListView');
        });

        Route::get('/{slug}', action: function ($slug) {
            return Inertia::render('Lowongan/Show', ['slug' => $slug]);
        });
    });


// Rute group untuk produk
Route::prefix('produk')
    ->middleware(CheckFeatureToggle::class . ':produk_module')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('Produk/ListView');
        })->name('produk.list');

        Route::get('/{slug}', function ($slug) {
            return Inertia::render('Produk/Show', ['slug' => $slug]);
        })->name('produk.show');
    });


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::prefix('error')->group(function () {
//     Route::get('/404', function () {
//         abort(404);
//     })->name('error.404');

//     Route::get('/403', function () {
//         abort(403);
//     })->name('error.403');

//     Route::get('/500', function () {
//         abort(500);
//     })->name('error.500');

//     Route::get('/503', function () {
//         abort(503);
//     })->name('error.503');

//     Route::get('/401', function () {
//         abort(401);
//     })->name('error.401');

//     Route::get('/419', function () {
//         abort(419);
//     })->name('error.419');

//     Route::get('/429', function () {
//         abort(429);
//     })->name('error.429');

//     Route::get('/custom', function () {
//         throw new \Exception('This is a custom error for testing purposes');
//     })->name('error.custom');
// });

Route::get('/thumbnail/{path}', [ThumbnailController::class, 'generate'])->name('thumbnail');

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::post('/sitemap/clear-cache', [SitemapController::class, 'clearCache'])->middleware('auth');

Route::get('/robots.txt', [RobotsController::class, 'index']);

require __DIR__ . '/auth.php';
