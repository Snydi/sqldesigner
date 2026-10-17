<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PlanLimitService;
use Illuminate\Console\Command;

class GenerateLlmsFiles extends Command
{
    protected $signature = 'seo:generate-llms {--check : Report stale files without changing them}';

    protected $description = 'Generate llms.txt and llms-full.txt from sitemap and blog-index metadata';

    public function handle(): int
    {
        $sitemap = $this->readSitemap();
        if ($sitemap === null) {
            return self::FAILURE;
        }

        $articles = $this->readBlogIndex($sitemap['urls']);
        if ($articles === null) {
            return self::FAILURE;
        }

        $lastUpdated = max(array_values($sitemap['lastmod']));
        $files = [
            public_path('llms.txt') => $this->buildSummary($articles, $lastUpdated),
            public_path('llms-full.txt') => $this->buildFullReference($articles, $lastUpdated),
        ];

        $stale = [];
        foreach ($files as $path => $content) {
            $current = file_get_contents($path);
            if ($current !== false && $this->normalize($current) === $this->normalize($content)) {
                continue;
            }

            $stale[] = basename($path);
            if (! $this->option('check') && file_put_contents($path, $content) === false) {
                $this->error("Unable to write {$path}");

                return self::FAILURE;
            }
        }

        if ($stale === []) {
            $this->info('AI-search reference files are current.');

            return self::SUCCESS;
        }

        if ($this->option('check')) {
            $this->error('Stale AI-search file(s): '.implode(', ', $stale));

            return self::FAILURE;
        }

        $this->info('Generated '.implode(' and ', $stale).' from current site metadata.');

        return self::SUCCESS;
    }

    /**
     * @return array{urls: string[], lastmod: array<string, string>}|null
     */
    private function readSitemap(): ?array
    {
        $xml = simplexml_load_file(public_path('sitemap.xml'));
        if ($xml === false) {
            $this->error('Unable to read public/sitemap.xml.');

            return null;
        }

        $urls = [];
        $lastmod = [];
        foreach ($xml->url as $entry) {
            $url = (string) $entry->loc;
            $path = parse_url($url, PHP_URL_PATH) ?: '/';
            $urls[] = $url;
            $lastmod[$path] = (string) $entry->lastmod;
        }

        if ($urls === [] || in_array('', $lastmod, true)) {
            $this->error('The sitemap must contain URLs with lastmod values.');

            return null;
        }

        return ['urls' => $urls, 'lastmod' => $lastmod];
    }

    /**
     * @param  string[]  $sitemapUrls
     * @return array<int, array{url: string, title: string, summary: string}>|null
     */
    private function readBlogIndex(array $sitemapUrls): ?array
    {
        $source = file_get_contents(resource_path('views/blog/index.blade.php'));
        if ($source === false) {
            $this->error('Unable to read the blog index template.');

            return null;
        }

        $pattern = '/<a class="post-card" href="(?<path>\/blog\/[^"?]+)">.*?<h2>(?<title>.*?)<\/h2>\s*<p>(?<summary>.*?)<\/p>\s*<\/a>/s';
        preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);

        $allowedUrls = array_flip($sitemapUrls);
        $articles = [];
        foreach ($matches as $match) {
            $url = 'https://sql-designer.com'.$match['path'];
            if (! isset($allowedUrls[$url])) {
                continue;
            }

            $articles[] = [
                'url' => $url,
                'title' => $this->plainText($match['title']),
                'summary' => $this->plainText($match['summary']),
            ];
        }

        $expectedBlogUrls = array_filter(
            $sitemapUrls,
            static fn (string $url): bool => str_starts_with($url, 'https://sql-designer.com/blog/'),
        );

        if (count($articles) !== count($expectedBlogUrls)) {
            $this->error(sprintf(
                'Blog metadata mismatch: found %d cards for %d sitemap routes.',
                count($articles),
                count($expectedBlogUrls),
            ));

            return null;
        }

        return $articles;
    }

    /**
     * @param  array<int, array{url: string, title: string, summary: string}>  $articles
     */
    private function buildSummary(array $articles, string $lastUpdated): string
    {
        $articleCount = count($articles);
        $diagramLimit = PlanLimitService::DIAGRAM_LIMIT;
        $exportLimit = PlanLimitService::EXPORT_WEEKLY_LIMIT;
        $doctorLimit = PlanLimitService::SCHEMA_DOCTOR_WEEKLY_LIMIT;

        return <<<TEXT
# SQL Designer

> Browser-based visual database schema designer with SQL import and export for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access.

SQL Designer lets users create tables, columns, constraints, and foreign-key relationships on a visual ERD canvas. The Free plan includes {$diagramLimit} saved diagram, {$exportLimit} combined exports per week, and {$doctorLimit} Schema Doctor scans per week. Pro costs $10 USD per month and removes usage limits.

## Primary sources

- [Try the editor](https://sql-designer.com/demo): Working ER diagram editor; no account is required to start.
- [Features](https://sql-designer.com/features): Supported design, import, export, sharing, and collaboration capabilities.
- [Pricing](https://sql-designer.com/pricing): Current Free and Pro plan limits and price.
- [About](https://sql-designer.com/about): Product history, operator, authorship, and editorial information.
- [Schema library](https://sql-designer.com/library): Community-shared database diagram examples.
- [Full reference](https://sql-designer.com/llms-full.txt): Detailed product, dialect, feature, and content summaries.

## Guide collections

- [ERD tools and database diagram guides](https://sql-designer.com/blog/erd-tools-guides): Tool selection, comparisons, online workflows, and schema examples.
- [Relational database design guides](https://sql-designer.com/blog/relational-database-design-guides): Normalization, cardinality, joins, and relationship design.
- [MySQL and PostgreSQL guides](https://sql-designer.com/blog/mysql-postgresql-guides): Data types, foreign keys, indexes, DDL, and engine differences.
- [All database design resources](https://sql-designer.com/blog): {$articleCount} current article and topic-hub pages.

## Operator, contact, and licensing

- Operator and author: Dmitriy Snyatkov.
- Contact: [dmitriy@sql-designer.com](mailto:dmitriy@sql-designer.com).
- Source: [Snydi/sqldesigner on GitHub](https://github.com/Snydi/sqldesigner).
- Application source code license: [GNU AGPL-3.0](https://github.com/Snydi/sqldesigner/blob/master/LICENSE).
- Website editorial content has no separate reuse license stated; contact the operator for reuse permission.

Last updated: {$lastUpdated}
TEXT;
    }

    /**
     * @param  array<int, array{url: string, title: string, summary: string}>  $articles
     */
    private function buildFullReference(array $articles, string $lastUpdated): string
    {
        $diagramLimit = PlanLimitService::DIAGRAM_LIMIT;
        $exportLimit = PlanLimitService::EXPORT_WEEKLY_LIMIT;
        $doctorLimit = PlanLimitService::SCHEMA_DOCTOR_WEEKLY_LIMIT;
        $articleEntries = collect($articles)->map(
            static fn (array $article): string => "### {$article['title']}\nURL: {$article['url']}\n{$article['summary']}",
        )->implode("\n\n");

        return <<<TEXT
# SQL Designer — Full Reference

> Browser-based ERD and database schema designer with a visual canvas, SQL import and export for six dialects, real-time collaboration, a Free plan, an optional Pro subscription, and AGPL-3.0 source code.

## Product overview

SQL Designer is a browser-based visual database schema designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access. Users place tables on a canvas, define columns with database-specific data types and constraints, connect foreign keys, and export runnable database definitions. No installation is required.

The source code uses Laravel 12, Vue 3, Vuex, Vue Flow, and PostgreSQL and is available at https://github.com/Snydi/sqldesigner under AGPL-3.0.

## Core features

- Visual drag-and-drop ERD canvas with crow's-foot relationship notation.
- SQL import from existing CREATE TABLE scripts.
- SQL export for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access.
- JSON, Laravel migration, and PNG export.
- Read-only, editable, and per-user sharing modes.
- Real-time multiplayer editing with live cursors.
- Interactive iframe embeds and a public community schema library.
- Cloud auto-save for account diagrams.

## Current pricing and limits

- Free: {$diagramLimit} saved diagram, {$exportLimit} combined SQL/JSON/migration/PNG exports per week, and {$doctorLimit} Schema Doctor scans per week.
- Pro: $10 USD per month for unlimited diagrams, exports, and Schema Doctor scans.
- Demo: the editor can be used without an account at https://sql-designer.com/demo; demo work is stored locally rather than in an account.

## Supported SQL dialects

- MySQL
- PostgreSQL
- SQLite
- Oracle Database
- Microsoft SQL Server
- Microsoft Access

## Database design resources

The following entries are generated from the same cards used by the public blog index and are limited to URLs present in the XML sitemap.

{$articleEntries}

## Key pages

- Home: https://sql-designer.com/
- Demo editor: https://sql-designer.com/demo
- Features: https://sql-designer.com/features
- Pricing: https://sql-designer.com/pricing
- Schema library: https://sql-designer.com/library
- Blog index: https://sql-designer.com/blog
- About: https://sql-designer.com/about
- Human-readable sitemap: https://sql-designer.com/sitemap
- XML sitemap: https://sql-designer.com/sitemap.xml
- Source repository: https://github.com/Snydi/sqldesigner

## Operator and licensing

- Operator and primary author: Dmitriy Snyatkov.
- Contact: dmitriy@sql-designer.com.
- Application source code license: GNU AGPL-3.0.
- Editorial content has no separate reuse license stated.

## Last updated

{$lastUpdated}
TEXT;
    }

    private function plainText(string $html): string
    {
        return preg_replace('/\s+/u', ' ', trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '';
    }

    private function normalize(string $content): string
    {
        return str_replace("\r\n", "\n", rtrim($content))."\n";
    }
}
