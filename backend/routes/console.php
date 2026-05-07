<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('seo:indexnow', function () {
    $key  = 'd4e7a2f1b8c53690';
    $host = 'sql-designer.com';

    $xml  = simplexml_load_file(public_path('sitemap.xml'));
    $urls = collect($xml->url)->map(fn($u) => (string) $u->loc)->values()->all();

    $response = Http::post('https://api.indexnow.org/indexnow', [
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => "https://{$host}/{$key}.txt",
        'urlList'     => $urls,
    ]);

    if ($response->successful() || $response->status() === 202) {
        $this->info("Submitted " . count($urls) . " URLs to IndexNow (HTTP {$response->status()}).");
    } else {
        $this->error("IndexNow returned HTTP {$response->status()}: " . $response->body());
    }
})->purpose('Submit all sitemap URLs to IndexNow (Bing/Yandex instant indexing)');
