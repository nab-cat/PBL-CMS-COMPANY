<?php

namespace App\Console\Commands;

use App\Services\ApiCacheService;
use Illuminate\Console\Command;

class ClearApiCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:clear-api 
                            {endpoint? : Specific endpoint to clear cache for}
                            {--all : Clear all API cache}
                            {--stats : Show cache statistics}
                            {--warmup : Warm up popular endpoints}';

    /**
     * The console command description.
     */
    protected $description = 'Clear API cache or show cache statistics';

    protected ApiCacheService $cacheService;

    public function __construct(ApiCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('stats')) {
            $this->showStats();
            return 0;
        }

        if ($this->option('warmup')) {
            $this->warmupCache();
            return 0;
        }

        if ($this->option('all')) {
            $this->clearAllCache();
            return 0;
        }

        $endpoint = $this->argument('endpoint');
        if ($endpoint) {
            $this->clearEndpointCache($endpoint);
            return 0;
        }

        $this->showHelp();
        return 0;
    }

    protected function showStats(): void
    {
        $this->info('Fetching cache statistics...');

        try {
            $stats = $this->cacheService->getCacheStats();

            $this->table(['Metric', 'Value'], [
                ['Cache Driver', $stats['driver']],
                ['Total Cache Keys', $stats['total_keys']],
                ['API Cache Keys', $stats['api_cache_keys']],
            ]);
        } catch (\Exception $e) {
            $this->error('Failed to get cache statistics: ' . $e->getMessage());
        }
    }

    protected function clearAllCache(): void
    {
        $this->info('Clearing all API cache...');

        try {
            $this->cacheService->clearAllApiCache();
            $this->info('All API cache cleared successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to clear all cache: ' . $e->getMessage());
        }
    }

    protected function clearEndpointCache(string $endpoint): void
    {
        $this->info("Clearing cache for endpoint: {$endpoint}");

        try {
            $this->cacheService->clearEndpointCache($endpoint);
            $this->info("Cache cleared for endpoint: {$endpoint}");
        } catch (\Exception $e) {
            $this->error("Failed to clear cache for endpoint {$endpoint}: " . $e->getMessage());
        }
    }

    protected function warmupCache(): void
    {
        $this->info('Warming up cache for popular endpoints...');

        $popularEndpoints = [
            'artikel' => '/api/artikel',
            'artikel-categories' => '/api/artikel/categories',
            'event' => '/api/event',
            'produk' => '/api/produk',
            'profil-perusahaan' => '/api/profil-perusahaan',
            'media-sosial' => '/api/media-sosial',
            'konten-slider' => '/api/konten-slider',
            'feature-toggles' => '/api/feature-toggles'
        ];

        $progressBar = $this->output->createProgressBar(count($popularEndpoints));
        $progressBar->start();

        $warmedEndpoints = [];
        $failedEndpoints = [];

        foreach ($popularEndpoints as $name => $endpoint) {
            try {
                // Make internal request to warm up cache
                $response = app('Illuminate\Contracts\Http\Kernel')
                    ->handle(\Illuminate\Http\Request::create($endpoint, 'GET'));

                if ($response->getStatusCode() === 200) {
                    $warmedEndpoints[] = $name;
                } else {
                    $failedEndpoints[] = $name . ' (HTTP ' . $response->getStatusCode() . ')';
                }
            } catch (\Exception $e) {
                $failedEndpoints[] = $name . ' (Error: ' . $e->getMessage() . ')';
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        if (!empty($warmedEndpoints)) {
            $this->info('Successfully warmed up cache for: ' . implode(', ', $warmedEndpoints));
        }

        if (!empty($failedEndpoints)) {
            $this->warn('Failed to warm up cache for: ' . implode(', ', $failedEndpoints));
        }

        $this->info('Cache warmup completed!');
    }

    protected function showHelp(): void
    {
        $this->info('API Cache Management Commands:');
        $this->line('');
        $this->line('  php artisan cache:clear-api --all          Clear all API cache');
        $this->line('  php artisan cache:clear-api artikel        Clear cache for artikel endpoint');
        $this->line('  php artisan cache:clear-api --stats        Show cache statistics');
        $this->line('  php artisan cache:clear-api --warmup       Warm up popular endpoints');
    }
}
