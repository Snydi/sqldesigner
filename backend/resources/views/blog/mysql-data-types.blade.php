@extends('layouts.main')

@section('title', 'MySQL Data Types Explained — Which to Use and When')

@section('head')
    <meta name="description"
          content="A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-data-types">
    <meta property="og:title" content="MySQL Data Types Explained — Which to Use and When">
    <meta property="og:description"
          content="A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-data-types">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MySQL Data Types Explained — Which to Use and When">
    <meta name="twitter:description" content="A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <script type="application/ld+json">
        @verbatim
            [
            {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": [
                    { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sql-designer.com/" },
                    { "@type": "ListItem", "position": 2, "name": "Blog", "item": "https://sql-designer.com/blog" },
                    { "@type": "ListItem", "position": 3, "name": "MySQL Data Types Explained", "item": "https://sql-designer.com/blog/mysql-data-types" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "MySQL Data Types Explained — Which to Use and When",
                "description": "A practical guide to MySQL data types: numeric, string, date/time, and JSON types.",
                "image": "https://sql-designer.com/images/screenshot.png",
                "url": "https://sql-designer.com/blog/mysql-data-types",
                "datePublished": "2026-03-19",
                "dateModified": "2026-03-24",
                "author": { "@type": "Organization", "name": "SQL Designer" },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
            }
            ]
        @endverbatim
    </script>
    <style>
        body { overflow-y: auto; }

        .page-intro {
            padding: clamp(2rem, 4vw, 3.5rem) var(--gutter, 2rem) clamp(1.5rem, 3vw, 2.5rem);
            border-bottom: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }
        .page-intro::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(var(--border-light) 1px, transparent 1px),
                linear-gradient(90deg, var(--border-light) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: radial-gradient(ellipse 60% 70% at 30% 0%, black 30%, transparent 75%);
            opacity: 0.45;
            pointer-events: none;
        }
        .intro-inner { max-width: 900px; margin: 0 auto; position: relative; }
        .breadcrumb {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 1rem;
        }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--color-primary-text); }
        .breadcrumb .sep { margin: 0 0.4rem; color: var(--border-strong); }
        .post-eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 0.8rem;
        }
        h1.page-h1 {
            font-size: clamp(1.6rem, 3.5vw, 2.4rem);
            line-height: 1.15;
            letter-spacing: -0.02em;
            font-weight: 600;
            margin: 0 0 1rem;
            text-wrap: balance;
            color: var(--text-primary);
        }
        .page-sub {
            font-size: 1rem;
            color: var(--text-secondary);
            margin: 0;
            max-width: 66ch;
            line-height: 1.7;
            text-wrap: pretty;
            border-left: 3px solid var(--color-primary);
            padding-left: 1.2rem;
        }

        .article-layout {
            display: grid;
            grid-template-columns: 200px minmax(0, 1fr);
            gap: clamp(2rem, 5vw, 4rem);
            max-width: 1060px;
            margin: 0 auto;
            padding: clamp(2rem, 4vw, 3rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
            align-items: start;
        }
        @media (max-width: 760px) {
            .article-layout { grid-template-columns: 1fr; gap: 1.5rem; padding-top: 1.5rem; }
        }
        .article-sidebar { position: sticky; top: 5rem; }
        @media (max-width: 760px) { .article-sidebar { position: static; } }
        .sidebar-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0 0 0.8rem;
        }
        .sidebar-nav { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.05rem; }
        .sidebar-nav a {
            display: block;
            font-size: 0.82rem;
            color: var(--text-muted);
            padding: 0.35rem 0.7rem;
            border-left: 2px solid var(--border-color);
            transition: color 120ms, border-color 120ms;
            text-decoration: none;
            line-height: 1.4;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }
        @media (max-width: 760px) {
            .sidebar-nav { flex-direction: row; flex-wrap: wrap; gap: 0.4rem; }
            .sidebar-nav a { border-left: none; border-bottom: 2px solid var(--border-color); padding: 0.3rem 0.5rem; font-size: 0.78rem; }
        }

        .article-body { min-width: 0; }
        .article-body h2 {
            font-size: clamp(1.1rem, 2vw, 1.35rem);
            letter-spacing: -0.01em;
            font-weight: 600;
            margin: 2.5rem 0 0.8rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            scroll-margin-top: 5rem;
        }
        .article-body h2:first-child { margin-top: 0; }
        .article-body p { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin: 0 0 1rem; text-wrap: pretty; }
        .article-body ul, .article-body ol { margin: 0 0 1rem 1.5rem; padding: 0; }
        .article-body li { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin-bottom: 0.35rem; }
        .article-body code { background: var(--bg-elevated); padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.83em; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }
        .article-body pre { background: #181f2e; color: #e2e8f0; border-radius: 8px; padding: 1.2rem 1.5rem; overflow-x: auto; margin: 1rem 0 1.5rem; font-size: 0.875rem; line-height: 1.65; border: 1px solid var(--border-color); }
        .article-body pre code { background: none; padding: 0; color: inherit; font-size: inherit; font-family: 'JetBrains Mono', monospace; }
        .article-body table { width: 100%; border-collapse: collapse; margin: 0 0 1.5rem; font-size: 0.85rem; }
        .article-body th { background: var(--bg-elevated); color: var(--text-primary); padding: 0.6rem 1rem; text-align: left; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid var(--border-strong); font-family: 'JetBrains Mono', monospace; }
        .article-body td { padding: 0.55rem 1rem; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); vertical-align: top; }
        .article-body tr:nth-child(even) td { background: var(--bg-elevated); }
        .article-body .verdict { background: rgba(93,181,131,0.08); border-left: 3px solid var(--color-primary-text); padding: 0.9rem 1.2rem; border-radius: 0 6px 6px 0; margin: 1.2rem 0; }
        .article-body .verdict p { margin: 0; font-size: 0.88rem; }
        .article-body a { color: var(--color-primary-text); }
        .article-body strong { color: var(--text-primary); }

        .related-nav { margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); }
        .related-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.8rem; }
        .related-nav ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.4rem; }
        .related-nav a { color: var(--color-primary-text); font-size: 0.88rem; text-decoration: none; }
        .related-nav a:hover { text-decoration: underline; }

        .docs-cta { margin: 0 auto; max-width: 1060px; padding: clamp(2rem, 4vw, 2.8rem) var(--gutter, 2rem); border-top: 1px solid var(--border-color); text-align: center; }
        .docs-cta h2 { font-size: clamp(1.2rem, 2.2vw, 1.6rem); letter-spacing: -0.02em; margin: 0 0 0.6rem; font-weight: 600; color: var(--text-primary); text-transform: none; }
        .docs-cta p { color: var(--text-secondary); margin: 0 auto 1.2rem; max-width: 52ch; font-size: 0.93rem; }
        .docs-cta .actions { display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; }
    </style>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>MySQL</span></p>
        <p class="post-eyebrow">March 2026 · 7 min read</p>
        <h1 class="page-h1">MySQL Data Types Explained — Which to Use and When</h1>
        <p class="page-sub">Choosing the right data type for each column is one of the most important decisions in database design. The wrong choice costs you storage, hurts query performance, and can introduce subtle data integrity bugs. This guide covers the most important MySQL data types and when to reach for each one.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#numeric-types">Numeric Types</a></li>
            <li><a href="#string-types">String Types</a></li>
            <li><a href="#date-and-time-types">Date and Time Types</a></li>
            <li><a href="#json-type">JSON Type</a></li>
            <li><a href="#a-quick-reference-common-use-cases">Common Use Cases</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <h2 id="numeric-types">Numeric Types</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Storage</th>
                <th>Range (signed)</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>TINYINT</code></td>
                <td>1 byte</td>
                <td>-128 to 127</td>
                <td>Booleans, small status flags</td>
            </tr>
            <tr>
                <td><code>SMALLINT</code></td>
                <td>2 bytes</td>
                <td>-32,768 to 32,767</td>
                <td>Small counters, age, year</td>
            </tr>
            <tr>
                <td><code>INT</code></td>
                <td>4 bytes</td>
                <td>-2.1B to 2.1B</td>
                <td>General-purpose IDs and counts</td>
            </tr>
            <tr>
                <td><code>BIGINT</code></td>
                <td>8 bytes</td>
                <td>±9.2 quintillion</td>
                <td>High-volume tables, snowflake IDs</td>
            </tr>
            <tr>
                <td><code>DECIMAL(p,s)</code></td>
                <td>Variable</td>
                <td>Exact</td>
                <td>Money, measurements requiring precision</td>
            </tr>
            <tr>
                <td><code>FLOAT</code> / <code>DOUBLE</code></td>
                <td>4 / 8 bytes</td>
                <td>Approximate</td>
                <td>Scientific values where small errors are acceptable</td>
            </tr>
        </table>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>TINYINT(1)</code> for booleans — MySQL's conventional boolean representation.</li>
            <li>Use <code>INT UNSIGNED</code> for auto-increment primary keys (doubles the positive range to ~4.3B).</li>
            <li>Use <code>BIGINT UNSIGNED</code> for tables that may grow very large.</li>
            <li>Never use <code>FLOAT</code> or <code>DOUBLE</code> for monetary values — floating-point imprecision will cause rounding errors in financial calculations. Use <code>DECIMAL(10, 2)</code> instead.</li>
        </ul>

        <h2 id="string-types">String Types</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Max size</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>CHAR(n)</code></td>
                <td>255 chars</td>
                <td>Fixed-length strings: country codes, hashes, status enums</td>
            </tr>
            <tr>
                <td><code>VARCHAR(n)</code></td>
                <td>65,535 bytes</td>
                <td>Variable-length strings: names, emails, URLs, titles</td>
            </tr>
            <tr>
                <td><code>TEXT</code></td>
                <td>65,535 bytes</td>
                <td>Long content: descriptions, comments, HTML</td>
            </tr>
            <tr>
                <td><code>MEDIUMTEXT</code></td>
                <td>16 MB</td>
                <td>Large documents, article bodies</td>
            </tr>
            <tr>
                <td><code>LONGTEXT</code></td>
                <td>4 GB</td>
                <td>Very large content (logs, serialised data)</td>
            </tr>
        </table>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>VARCHAR(n)</code> for most string columns. Set <code>n</code> to a realistic maximum — <code>VARCHAR(255)</code> is common for names and emails, <code>VARCHAR(2048)</code> for URLs.</li>
            <li>Use <code>CHAR(n)</code> only when the value is always the same length (e.g., ISO country codes <code>CHAR(2)</code>, MD5 hashes <code>CHAR(32)</code>). It's marginally faster to index than <code>VARCHAR</code>.</li>
            <li>Don't put <code>TEXT</code> columns in <code>WHERE</code> clauses without a full-text index — it forces a table scan.</li>
            <li>Avoid <code>ENUM</code> — it's inflexible to alter and opaque to external tools. Use a <code>VARCHAR</code> with a <code>CHECK</code> constraint, or a separate lookup table.</li>
        </ul>

        <h2 id="date-and-time-types">Date and Time Types</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Range</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>DATE</code></td>
                <td>1000-01-01 to 9999-12-31</td>
                <td>Dates without time: birthdays, deadlines</td>
            </tr>
            <tr>
                <td><code>DATETIME</code></td>
                <td>1000-01-01 to 9999-12-31</td>
                <td>Timestamps stored in application timezone</td>
            </tr>
            <tr>
                <td><code>TIMESTAMP</code></td>
                <td>1970-01-01 to 2038-01-19</td>
                <td>Audit timestamps stored in UTC, auto-converted on retrieval</td>
            </tr>
            <tr>
                <td><code>TIME</code></td>
                <td>-838:59:59 to 838:59:59</td>
                <td>Durations, time-of-day without date</td>
            </tr>
            <tr>
                <td><code>YEAR</code></td>
                <td>1901 to 2155</td>
                <td>Year-only values</td>
            </tr>
        </table>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>TIMESTAMP</code> for <code>created_at</code> and <code>updated_at</code> audit columns — it auto-converts to UTC on storage and back to the session timezone on retrieval.</li>
            <li>Use <code>DATETIME</code> when you need to store a specific wall-clock time without timezone conversion (e.g., a scheduled event that should fire at 9am regardless of timezone).</li>
            <li><code>TIMESTAMP</code> has a 2038 limit — for future dates beyond that, use <code>DATETIME</code>.</li>
        </ul>

        <h2 id="json-type">JSON Type</h2>
        <p>
            MySQL 5.7+ supports a native <code>JSON</code> column type that validates JSON on insert and enables path-based queries with <code>JSON_EXTRACT()</code> and the <code>-&gt;</code> operator.
        </p>
        <pre><code>SELECT config->'$.theme' FROM user_settings WHERE user_id = 1;</code></pre>
        <p>
            Use <code>JSON</code> for truly variable or schema-less data — feature flags, user preferences, integration payloads. Don't use it as a way to avoid modelling your data properly: if the same key appears in every row, it should be a column.
        </p>

        <h2 id="a-quick-reference-common-use-cases">A Quick Reference: Common Use Cases</h2>
        <ul>
            <li><strong>Primary key:</strong> <code>INT UNSIGNED NOT NULL AUTO_INCREMENT</code></li>
            <li><strong>Email address:</strong> <code>VARCHAR(255) NOT NULL UNIQUE</code></li>
            <li><strong>Password hash:</strong> <code>VARCHAR(255) NOT NULL</code></li>
            <li><strong>Price / monetary value:</strong> <code>DECIMAL(10, 2) NOT NULL</code></li>
            <li><strong>Boolean flag:</strong> <code>TINYINT(1) NOT NULL DEFAULT 0</code></li>
            <li><strong>Created/updated timestamps:</strong> <code>TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP</code></li>
            <li><strong>Long-form text:</strong> <code>TEXT</code></li>
            <li><strong>Foreign key:</strong> Same type as the referenced primary key, e.g. <code>INT UNSIGNED NOT NULL</code></li>
        </ul>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
                <li><a href="/blog/how-to-design-mysql-database-schema">How to Design a MySQL Database Schema &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization Explained &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Apply these data types in your schema</h2>
    <p>SQL Designer lets you pick MySQL data types from a dropdown as you design your tables visually. Free, no installation required.</p>
    <div class="actions">
        <a class="btn btn-solid btn-lg" href="/demo">Open the demo</a>
        <a class="btn btn-outline btn-lg" href="/register">Create free account</a>
    </div>
</section>

<script>
    (function () {
        const links = document.querySelectorAll('.sidebar-nav a[href^="#"]');
        const headings = document.querySelectorAll('.article-body h2[id]');
        function update() {
            let current = '';
            const y = window.scrollY + 100;
            headings.forEach(el => { if (el.offsetTop <= y) current = el.id; });
            links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + current));
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    })();
</script>
@endsection
