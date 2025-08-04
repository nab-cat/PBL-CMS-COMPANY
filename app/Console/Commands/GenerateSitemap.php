<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SEO\SitemapController;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        try {
            $controller = new SitemapController();
            $controller->index();

            $this->info('✅ Sitemap generated successfully at public/sitemap.xml');
            $this->info('📍 Available at: ' . config('app.url') . '/sitemap.xml');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to generate sitemap: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
