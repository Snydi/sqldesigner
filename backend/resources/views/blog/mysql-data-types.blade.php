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
        body {
            overflow-y: auto;
        }

        .blog-post {
            max-width: 760px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
        }

        .blog-post .breadcrumb {
            font-size: 0.875rem;
            color: #767676;
            background-color: transparent;
            text-transform: none;
            margin-bottom: 1.5rem;
        }

        .blog-post .breadcrumb a {
            color: var(--color-primary);
        }

        .blog-post .post-meta {
            font-size: 0.875rem;
            color: #767676;
            background-color: transparent;
            text-transform: none;
            margin-bottom: 1rem;
        }

        .blog-post h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-primary);
            background-color: transparent;
            margin: 0 0 1rem;
            line-height: 1.3;
        }

        .blog-post .intro {
            font-size: 1rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            border-left: 3px solid var(--color-primary);
            padding-left: 1.2rem;
        }

        .blog-post h2 {
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-primary);
            background-color: transparent;
            margin: 2.5rem 0 0.8rem;
        }

        .blog-post p {
            font-size: 0.9rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin: 0 0 1rem;
        }

        .blog-post ul, .blog-post ol {
            margin: 0 0 1rem 1.5rem;
            padding: 0;
        }

        .blog-post li {
            font-size: 0.9rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin-bottom: 0.3rem;
        }

        .blog-post code {
            background: var(--bg-elevated);
            padding: 0.1em 0.4em;
            border-radius: 3px;
            font-size: 0.85em;
            color: var(--text-primary);
        }

        .blog-post pre {
            background: #1e293b;
            color: #e2e8f0;
            border-radius: 6px;
            padding: 1.2rem 1.5rem;
            overflow-x: auto;
            margin: 1rem 0 1.5rem;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .blog-post pre code {
            background: none;
            padding: 0;
            color: inherit;
            font-size: inherit;
        }

        .blog-post table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 1.5rem;
            font-size: 0.85rem;
        }

        .blog-post th {
            background: #1e293b;
            color: #e2e8f0;
            padding: 0.6rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .blog-post td {
            padding: 0.55rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
            vertical-align: top;
        }

        .blog-post tr:nth-child(even) td {
            background: var(--bg-elevated);
        }

        .blog-post .cta-box {
            background: var(--color-primary-hover);
            color: #fff;
            border-radius: 6px;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        .blog-post .cta-box h3 {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 0.8rem;
        }

        .blog-post .cta-box p {
            color: #fff;
            margin: 0 0 1.2rem;
            font-size: 0.85rem;
        }

        .blog-post .btn-cta {
            background: var(--bg-surface);
            color: var(--color-primary);
            padding: 0.6rem 1.8rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    <article class="blog-post">
        <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; MySQL</p>
        <p class="post-meta"><time datetime="2026-03-19">March 2026</time> &mdash; 7 min read</p>
        <h1>MySQL Data Types Explained — Which to Use and When</h1>

        <p class="intro">
            Choosing the right data type for each column is one of the most important decisions in database design. The
            wrong choice costs you storage, hurts query performance, and can introduce subtle data integrity bugs. This
            guide covers the most important MySQL data types and when to reach for each one.
        </p>

        <h2>Numeric Types</h2>
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
            <li>Use <code>INT UNSIGNED</code> for auto-increment primary keys (doubles the positive range to ~4.3B).
            </li>
            <li>Use <code>BIGINT UNSIGNED</code> for tables that may grow very large.</li>
            <li>Never use <code>FLOAT</code> or <code>DOUBLE</code> for monetary values — floating-point imprecision
                will cause rounding errors in financial calculations. Use <code>DECIMAL(10, 2)</code> instead.
            </li>
        </ul>

        <h2>String Types</h2>
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
            <li>Use <code>VARCHAR(n)</code> for most string columns. Set <code>n</code> to a realistic maximum — <code>VARCHAR(255)</code>
                is common for names and emails, <code>VARCHAR(2048)</code> for URLs.
            </li>
            <li>Use <code>CHAR(n)</code> only when the value is always the same length (e.g., ISO country codes <code>CHAR(2)</code>,
                MD5 hashes <code>CHAR(32)</code>). It's marginally faster to index than <code>VARCHAR</code>.
            </li>
            <li>Don't put <code>TEXT</code> columns in <code>WHERE</code> clauses without a full-text index — it forces
                a table scan.
            </li>
            <li>Avoid <code>ENUM</code> — it's inflexible to alter and opaque to external tools. Use a
                <code>VARCHAR</code> with a <code>CHECK</code> constraint, or a separate lookup table.
            </li>
        </ul>

        <h2>Date and Time Types</h2>
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
            <li>Use <code>TIMESTAMP</code> for <code>created_at</code> and <code>updated_at</code> audit columns — it
                auto-converts to UTC on storage and back to the session timezone on retrieval.
            </li>
            <li>Use <code>DATETIME</code> when you need to store a specific wall-clock time without timezone conversion
                (e.g., a scheduled event that should fire at 9am regardless of timezone).
            </li>
            <li><code>TIMESTAMP</code> has a 2038 limit — for future dates beyond that, use <code>DATETIME</code>.</li>
        </ul>

        <h2>JSON Type</h2>
        <p>
            MySQL 5.7+ supports a native <code>JSON</code> column type that validates JSON on insert and enables
            path-based queries with <code>JSON_EXTRACT()</code> and the <code>-&gt;</code> operator.
        </p>
        <pre><code>SELECT config->'$.theme' FROM user_settings WHERE user_id = 1;</code></pre>
        <p>
            Use <code>JSON</code> for truly variable or schema-less data — feature flags, user preferences, integration
            payloads. Don't use it as a way to avoid modelling your data properly: if the same key appears in every row,
            it should be a column.
        </p>

        <h2>A Quick Reference: Common Use Cases</h2>
        <ul>
            <li><strong>Primary key:</strong> <code>INT UNSIGNED NOT NULL AUTO_INCREMENT</code></li>
            <li><strong>Email address:</strong> <code>VARCHAR(255) NOT NULL UNIQUE</code></li>
            <li><strong>Password hash:</strong> <code>VARCHAR(255) NOT NULL</code></li>
            <li><strong>Price / monetary value:</strong> <code>DECIMAL(10, 2) NOT NULL</code></li>
            <li><strong>Boolean flag:</strong> <code>TINYINT(1) NOT NULL DEFAULT 0</code></li>
            <li><strong>Created/updated timestamps:</strong> <code>TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP</code>
            </li>
            <li><strong>Long-form text:</strong> <code>TEXT</code></li>
            <li><strong>Foreign key:</strong> Same type as the referenced primary key, e.g. <code>INT UNSIGNED NOT
                    NULL</code></li>
        </ul>

        <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border-color);">
            <p style="font-size:0.875rem; text-transform:uppercase; letter-spacing:0.06em; color:#767676; margin:0 0 0.8rem;">
                Related Articles</p>
            <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
                <li><a href="/blog/mysql-foreign-key"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">MySQL Foreign Key —
                        Syntax and Examples &rarr;</a></li>
                <li><a href="/blog/how-to-design-mysql-database-schema"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">How to Design a
                        MySQL Database Schema &rarr;</a></li>
                <li><a href="/blog/database-normalization"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Database
                        Normalization Explained &rarr;</a></li>
            </ul>
        </nav>

        <div class="cta-box">
            <h3>Apply these data types in your schema</h3>
            <p>SQL Designer lets you pick MySQL data types from a dropdown as you design your tables visually. Free, no
                installation required.</p>
            <a class="btn-cta" href="/register">Create a Free Account</a>
        </div>
    </article>
@endsection
