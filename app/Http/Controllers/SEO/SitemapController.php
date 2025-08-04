<?php

namespace App\Http\Controllers\SEO;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Galeri;
use App\Models\Produk;
use App\Models\Artikel;
use App\Models\Lowongan;
use App\Models\CaseStudy;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function index()
    {
        // Check if we can use cached sitemap (cache for 30 minutes)
        $cacheKey = 'sitemap_xml_content';
        $sitemap = cache()->remember($cacheKey, 1800, function () {
            return $this->generateSitemap();
        });

        // Also write to file
        $sitemap->writeToFile(public_path('sitemap.xml'));

        // Return as response
        return $sitemap->toResponse(request());
    }

    /**
     * Generate the sitemap content
     */
    private function generateSitemap()
    {
        $sitemap = Sitemap::create()
            // Menambahkan halaman statis dari file web.php Anda
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/profil-perusahaan')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/visi-misi')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/sejarah-perusahaan')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/struktur-organisasi')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/feedback')->setPriority(0.6)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create(route('artikel.list'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create(route('studi-kasus.list'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create(route('event.list'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create('/galeri')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create('/unduhan')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
            ->add(Url::create('/lowongan')->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY))
            ->add(Url::create(route('produk.list'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));

        // Menambahkan Artikel
        Artikel::where('status_artikel', 'terpublikasi')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->each(function (Artikel $artikel) use ($sitemap) {
                try {
                    $sitemap->add(Url::create(route('artikel.show', $artikel->slug))
                        ->setLastModificationDate($artikel->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.9));
                } catch (\Exception $e) {
                    Log::warning("Skipping artikel with slug: {$artikel->slug} - Error: " . $e->getMessage());
                }
            });

        // Menambahkan Produk
        Produk::where('status_produk', 'terpublikasi')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->each(function (Produk $produk) use ($sitemap) {
                try {
                    $sitemap->add(Url::create(route('produk.show', $produk->slug))
                        ->setLastModificationDate($produk->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.9));
                } catch (\Exception $e) {
                    Log::warning("Skipping produk with slug: {$produk->slug} - Error: " . $e->getMessage());
                }
            });

        // Menambahkan Studi Kasus
        CaseStudy::where('status_case_study', 'terpublikasi')
            ->whereNotNull('slug_case_study')
            ->where('slug_case_study', '!=', '')
            ->get()
            ->each(function (CaseStudy $caseStudy) use ($sitemap) {
                try {
                    $sitemap->add(Url::create(route('studi-kasus.show', $caseStudy->slug_case_study))
                        ->setLastModificationDate($caseStudy->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.8));
                } catch (\Exception $e) {
                    Log::warning("Skipping case study with slug: {$caseStudy->slug_case_study} - Error: " . $e->getMessage());
                }
            });

        // Menambahkan Event (semua event, tidak hanya yang akan datang)
        Event::whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->each(function (Event $event) use ($sitemap) {
                try {
                    $sitemap->add(Url::create(route('event.show', $event->slug))
                        ->setLastModificationDate($event->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8));
                } catch (\Exception $e) {
                    Log::warning("Skipping event with slug: {$event->slug} - Error: " . $e->getMessage());
                }
            });

        // Menambahkan Galeri
        Galeri::where('status_galeri', 'terpublikasi')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->each(function (Galeri $galeri) use ($sitemap) {
                try {
                    $sitemap->add(Url::create("/galeri/{$galeri->slug}")
                        ->setLastModificationDate($galeri->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.7));
                } catch (\Exception $e) {
                    Log::warning("Skipping galeri with slug: {$galeri->slug} - Error: " . $e->getMessage());
                }
            });

        // Menambahkan Lowongan
        Lowongan::where('status_lowongan', 'terpublikasi')
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get()
            ->each(function (Lowongan $lowongan) use ($sitemap) {
                try {
                    $sitemap->add(Url::create("/lowongan/{$lowongan->slug}")
                        ->setLastModificationDate($lowongan->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.6));
                } catch (\Exception $e) {
                    Log::warning("Skipping lowongan with slug: {$lowongan->slug} - Error: " . $e->getMessage());
                }
            });

        return $sitemap;
    }

    /**
     * Clear the sitemap cache
     */
    public function clearCache()
    {
        cache()->forget('sitemap_xml_content');
        return response()->json(['message' => 'Sitemap cache cleared successfully']);
    }
}