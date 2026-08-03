<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncSitemapLastmod extends Command
{
    protected $signature = 'seo:sync-sitemap-lastmod {--check : Report stale dates without changing the sitemap}';

    protected $description = 'Synchronize sitemap lastmod values from page JSON-LD dateModified values';

    public function handle(): int
    {
        $sitemapPath = public_path('sitemap.xml');
        $sitemap = file_get_contents($sitemapPath);

        if ($sitemap === false) {
            $this->error("Unable to read {$sitemapPath}");

            return self::FAILURE;
        }

        $mismatches = [];
        $updatedSitemap = preg_replace_callback(
            '/<url>.*?<loc>https:\/\/sql-designer\.com(?<path>\/[^<]*)<\/loc>.*?<lastmod>(?<lastmod>\d{4}-\d{2}-\d{2})<\/lastmod>.*?<\/url>/s',
            function (array $matches) use (&$mismatches): string {
                $viewPath = $this->viewPathForUrl($matches['path']);

                if ($viewPath === null || ! file_exists($viewPath)) {
                    return $matches[0];
                }

                $view = file_get_contents($viewPath);
                if ($view === false || preg_match('/"dateModified"\s*:\s*"(?<date>\d{4}-\d{2}-\d{2})"/', $view, $dateMatch) !== 1) {
                    return $matches[0];
                }

                if ($matches['lastmod'] === $dateMatch['date']) {
                    return $matches[0];
                }

                $mismatches[$matches['path']] = [
                    'sitemap' => $matches['lastmod'],
                    'schema' => $dateMatch['date'],
                ];

                return preg_replace(
                    '/<lastmod>\d{4}-\d{2}-\d{2}<\/lastmod>/',
                    '<lastmod>'.$dateMatch['date'].'</lastmod>',
                    $matches[0],
                    1,
                ) ?? $matches[0];
            },
            $sitemap,
        );

        if ($updatedSitemap === null) {
            $this->error('Unable to parse sitemap URL entries.');

            return self::FAILURE;
        }

        if ($mismatches === []) {
            $this->info('Sitemap lastmod values already match page schema dates.');

            return self::SUCCESS;
        }

        foreach ($mismatches as $path => $dates) {
            $this->line("{$path}: {$dates['sitemap']} -> {$dates['schema']}");
        }

        if ($this->option('check')) {
            $this->error(count($mismatches).' stale sitemap lastmod value(s) found.');

            return self::FAILURE;
        }

        if (file_put_contents($sitemapPath, $updatedSitemap) === false) {
            $this->error("Unable to write {$sitemapPath}");

            return self::FAILURE;
        }

        $this->info(count($mismatches).' sitemap lastmod value(s) synchronized from page schemas.');

        return self::SUCCESS;
    }

    private function viewPathForUrl(string $path): ?string
    {
        $view = match (true) {
            $path === '/' => 'home',
            $path === '/blog' => 'blog/index',
            str_starts_with($path, '/blog/') => 'blog/'.substr($path, strlen('/blog/')),
            default => ltrim($path, '/'),
        };

        if ($view === '' || str_contains($view, '..')) {
            return null;
        }

        return resource_path('views/'.$view.'.blade.php');
    }
}
