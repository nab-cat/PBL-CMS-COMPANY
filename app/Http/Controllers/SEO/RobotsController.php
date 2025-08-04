<?php

namespace App\Http\Controllers\SEO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class RobotsController extends Controller
{
    /**
     * Menghasilkan konten untuk file robots.txt secara dinamis.
     */
    public function index()
    {
        // Pastikan APP_URL di file .env Anda sudah diatur dengan benar
        // contoh: APP_URL=https://domain-anda.com
        $appUrl = config('app.url');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "Sitemap: " . $appUrl . "/sitemap.xml";

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}