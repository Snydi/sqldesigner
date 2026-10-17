<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SeoIndexNow extends Command
{
    protected $signature = 'seo:indexnow';

    protected $description = 'Synchronize the sitemap and submit all URLs to IndexNow';

    public function handle(): int
    {
        if ($this->call('seo:sync-sitemap-lastmod') !== self::SUCCESS) {
            $this->error('IndexNow submission stopped because the sitemap could not be synchronized.');

            return self::FAILURE;
        }

        $xml = simplexml_load_file(public_path('sitemap.xml'));
        $urls = collect(iterator_to_array($xml->url, false))->map(fn ($u) => (string) $u->loc)->values()->all();
        $this->submitToIndexNow($urls);

        // Ordinary product and content pages are discovered through the XML sitemap.
        // Google's Indexing API is restricted to qualifying job and livestream pages.
        return self::SUCCESS;
    }

    /**
     * @param  string[]  $urls
     */
    private function submitToIndexNow(array $urls): void
    {
        $key = config('app.indexnow_key');
        $host = 'sql-designer.com';

        $response = Http::post('https://api.indexnow.org/indexnow', [
            'host' => $host,
            'key' => $key,
            'keyLocation' => "https://{$host}/{$key}.txt",
            'urlList' => $urls,
        ]);

        if ($response->successful() || $response->status() === 202) {
            $this->info('IndexNow: submitted '.count($urls)." URLs (HTTP {$response->status()}).");
        } else {
            $this->error("IndexNow: HTTP {$response->status()}: ".$response->body());
        }
    }

}
