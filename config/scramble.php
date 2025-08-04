<?php

use App\Http\Middleware\CustomDocsAccess;

$description = '
## Fitur Utama

### Manajemen Konten
- **Artikel**: Pengelolaan artikel dengan kategori, tag, dan optimasi SEO
- **Produk**: Katalog produk dengan kategori dan fitur pencarian
- **Galeri**: Manajemen galeri foto dengan kategorisasi
- **Event**: Pengelolaan acara perusahaan dengan sistem pendaftaran
- **Studi Kasus**: Showcase proyek dan studi kasus perusahaan
- **Unduhan**: Repository file untuk download publik

### Fitur Korporat  
- **Profil Perusahaan**: Informasi lengkap perusahaan (visi, misi, sejarah)
- **Struktur Organisasi**: Hierarki dan informasi karyawan
- **Lowongan Kerja**: Sistem posting lowongan dan aplikasi lamaran
- **Mitra**: Pengelolaan partner dan klien perusahaan

### Fitur Interaktif
- **Testimoni**: Sistem ulasan dan testimoni multi-platform
- **Umpan Balik**: Sistem feedback dan saran dari pengguna
- **Media Sosial**: Integrasi dan konfigurasi tautan media sosial
- **Newsletter**: Sistem konten slider dan konten unggulan

### Fitur Teknis
- **Caching**: Caching API lanjutan dengan Redis/Database
- **Feature Toggle**: Pengaktifan fitur secara dinamis
- **SEO**: Optimasi SEO bawaan untuk semua konten
- **Manajemen File**: Upload dan pengelolaan file dengan validasi

## Autentikasi & Keamanan
API ini menggunakan Laravel Sanctum untuk autentikasi. Beberapa endpoint memerlukan autentikasi, sementara yang lain dapat diakses secara publik.

## Pembatasan Rate
API menerapkan pembatasan rate untuk mencegah penyalahgunaan dan memastikan performa optimal.

## Strategi Caching
Implementasi caching untuk meningkatkan performa:
- Cache otomatis untuk endpoint read-only
- Invalidasi cache otomatis saat konten diperbarui
- Cache warming untuk endpoint populer

## Penanganan Error
Semua response error mengikuti format konsisten dengan HTTP status code yang tepat dan pesan error yang informatif.';

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0'),

        /*
         * Deskripsi yang ditampilkan di halaman utama dokumentasi API (`/docs/api`).
         */
        'description' => $description,
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Tentukan judul website dokumentasi. Nama aplikasi digunakan ketika config ini `null`.
         */
        'title' => "Dokumentasi API",

        /*
         * Define the theme of the documentation. Available options are `light` and `dark`.
         */
        'theme' => 'light',

        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * Hide the schemas in the Table of Contents. Enabled by default.
         */
        'hide_schemas' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '/favicon.ico',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',

        /*
         * There are three layouts for Elements:
         * - sidebar - (Elements default) Three-column design with a sidebar that can be resized.
         * - responsive - Like sidebar, except at small screen sizes it collapses the sidebar into a drawer that can be toggled open.
         * - stacked - Everything in a single column, making integrations with existing websites that have their own sidebar or other columns already.
         */
        'layout' => 'responsive',
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => null,

    /**
     * Determines how Scramble stores the descriptions of enum cases.
     * Available options:
     * - 'description' – Case descriptions are stored as the enum schema's description using table formatting.
     * - 'extension' – Case descriptions are stored in the `x-enumDescriptions` enum schema extension.
     *
     *    @see https://redocly.com/docs-legacy/api-reference-docs/specification-extensions/x-enum-descriptions
     * - false - Case descriptions are ignored.
     */
    'enum_cases_description_strategy' => 'description',

    'middleware' => [
        'web',
        CustomDocsAccess::class,
    ],

    'extensions' => [],
];
