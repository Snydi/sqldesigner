@extends('layouts.main')

@section('title', 'MySQL Data Types Explained — Which Type to Use and When')

@section('head')
    <meta name="description"
          content="MySQL's 30+ data types split into 4 families. Learn which numeric, string, date/time, and JSON type to choose — and the mistakes that corrupt financial data.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-data-types">
    <meta property="og:title" content="MySQL Data Types Explained — Which Type to Use and When">
    <meta property="og:description"
          content="MySQL's 30+ data types split into 4 families. Learn which numeric, string, date/time, and JSON type to choose — and the mistakes that corrupt financial data.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-data-types">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MySQL Data Types Explained — Which Type to Use and When">
    <meta name="twitter:description" content="MySQL's 30+ data types split into 4 families. Learn which numeric, string, date/time, and JSON type to choose — and the mistakes that corrupt financial data.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <link rel="stylesheet" href="/css/blog.css">
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
                "@type": ["BlogPosting", "TechArticle"],
                "headline": "MySQL Data Types Explained — Which Type to Use and When",
                "description": "A practical guide to MySQL data types: numeric, string, date/time, and JSON types, with advice on which to choose and common mistakes to avoid.",
                "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
                "url": "https://sql-designer.com/blog/mysql-data-types",
                "datePublished": "2026-03-19",
                "dateModified": "2026-05-17",
                "wordCount": 2350,
                "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
                "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub", ".key-takeaways"] },
                "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/mysql-data-types" }
            },
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "What MySQL data type should I use for storing money?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Use DECIMAL(p, s) for monetary values, such as DECIMAL(10, 2) for currency with two decimal places. Never use FLOAT or DOUBLE for money. Both types use IEEE 754 binary floating-point, which cannot represent most decimal fractions exactly, so rounding errors accumulate and produce incorrect totals in financial calculations." }
                    },
                    {
                        "@type": "Question",
                        "name": "What is the difference between DATETIME and TIMESTAMP in MySQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "TIMESTAMP stores values in UTC and automatically converts to the session timezone on retrieval, making it suitable for created_at and updated_at audit columns. DATETIME stores the literal wall-clock time without timezone conversion and has a wider date range (up to year 9999 vs TIMESTAMP's 2038-01-19 limit)." }
                    },
                    {
                        "@type": "Question",
                        "name": "What MySQL type should I use for boolean columns?",
                        "acceptedAnswer": { "@type": "Answer", "text": "MySQL has no native boolean type. The convention is TINYINT(1), which stores 0 (false) or 1 (true). ORMs like Laravel and Rails treat TINYINT(1) as a boolean automatically. MySQL 8.0+ also accepts BOOLEAN as a synonym for TINYINT(1)." }
                    },
                    {
                        "@type": "Question",
                        "name": "When should I use VARCHAR vs TEXT in MySQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Use VARCHAR(n) for short strings where you know the maximum length: names, emails, URLs, slugs. Use TEXT for long-form content such as article bodies, descriptions, or HTML where the length is unpredictable. Avoid using TEXT columns in WHERE clauses without a full-text index, as it forces a full table scan." }
                    },
                    {
                        "@type": "Question",
                        "name": "What MySQL data type should I use for a primary key?",
                        "acceptedAnswer": { "@type": "Answer", "text": "INT UNSIGNED NOT NULL AUTO_INCREMENT is the standard choice for most tables, supporting up to approximately 4.3 billion rows. Use BIGINT UNSIGNED NOT NULL AUTO_INCREMENT for tables expected to grow very large, such as event logs or high-volume transactional tables." }
                    },
                    {
                        "@type": "Question",
                        "name": "Why should I avoid ENUM in MySQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "ENUM stores allowed values as a one- or two-byte integer mapped to a list of strings. Adding a new value requires an ALTER TABLE that rewrites the entire table in older MySQL versions, causing downtime on large tables. ENUM values are also opaque to ORMs and external tools. A VARCHAR column with a CHECK constraint, or a separate lookup table, is more maintainable and easier to extend." }
                    },
                    {
                        "@type": "Question",
                        "name": "What MySQL data type should I use for a UUID?",
                        "acceptedAnswer": { "@type": "Answer", "text": "CHAR(36) stores the standard hyphenated UUID string. For storage efficiency, MySQL 8.0+ provides UUID_TO_BIN() and BIN_TO_UUID() to convert a UUID into BINARY(16), halving the storage compared to CHAR(36) and improving index performance. Use CHAR(36) for readability; use BINARY(16) for high-volume tables where index size matters." }
                    }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "VideoObject",
                "name": "MySQL Full Course — Bro Code",
                "description": "Full MySQL course covering data types, schema design, and queries — Bro Code on YouTube (2023).",
                "thumbnailUrl": "https://img.youtube.com/vi/5OdVJbNCSso/hqdefault.jpg",
                "uploadDate": "2023-01-02T00:00:00+00:00",
                "embedUrl": "https://www.youtube.com/embed/5OdVJbNCSso",
                "url": "https://www.youtube.com/watch?v=5OdVJbNCSso"
            }
            ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>MySQL</span></p>
        <p class="post-eyebrow">March 2026 · <time datetime="2026-05-17">Last updated: May 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 9 min read</p>
        <h1 class="page-h1">MySQL Data Types Explained — Which Type to Use and When</h1>
        <p class="page-sub">MySQL provides data types across four categories: numeric (<code>INT</code>, <code>BIGINT</code>, <code>DECIMAL</code>, <code>FLOAT</code>), string (<code>VARCHAR</code>, <code>TEXT</code>, <code>CHAR</code>, <code>ENUM</code>), date/time (<code>DATE</code>, <code>DATETIME</code>, <code>TIMESTAMP</code>), and structured (<code>JSON</code>). Each type has specific storage size, value range, and behavioural constraints — for example, <code>TIMESTAMP</code> auto-converts to UTC while <code>DATETIME</code> stores wall-clock time, and <code>DECIMAL</code> is exact while <code>FLOAT</code> is approximate. This guide covers which type to choose for each common use case.</p>
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
            <li><a href="#common-mistakes">Common Mistakes</a></li>
            <li><a href="#quick-reference">Quick Reference</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>MySQL's 30+ data types span 4 families: numeric, string, date/time, and JSON. Picking the wrong one wastes storage and introduces data errors that compound over time.</li>
                <li>Never use <code>FLOAT</code> or <code>DOUBLE</code> for money. Both use IEEE 754 binary floating-point, which can't represent most decimal fractions exactly. Use <code>DECIMAL(10,2)</code> instead.</li>
                <li><code>TIMESTAMP</code> auto-converts to UTC and has a hard 2038-01-19 ceiling. <code>DATETIME</code> stores wall-clock time with no conversion and supports dates through year 9999.</li>
                <li><code>INT UNSIGNED</code> covers ~4.3 billion rows. Switch to <code>BIGINT UNSIGNED</code> for event logs or high-volume tables before you hit that limit.</li>
            </ul>
        </div>

        <figure>
            <picture>
                <source srcset="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?fm=avif&q=80&w=1400&auto=format&fit=crop" type="image/avif">
                <source srcset="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?fm=webp&q=80&w=1400&auto=format&fit=crop" type="image/webp">
                <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?fm=jpg&q=80&w=1400&auto=format&fit=crop"
                     alt="SQL code on a monitor screen representing MySQL database schema design and data type selection"
                     loading="lazy" width="1400" height="700">
            </picture>
            <figcaption>Choosing the right column type at schema design time is far cheaper than migrating a live table later.</figcaption>
        </figure>

        <h2 id="numeric-types">Numeric Types</h2>

        <p>MySQL's numeric types split into two groups: exact types (<code>INT</code>, <code>BIGINT</code>, <code>DECIMAL</code>) and approximate types (<code>FLOAT</code>, <code>DOUBLE</code>). The distinction matters most for financial data. The <a href="https://dev.mysql.com/doc/refman/8.0/en/floating-point-types.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a> documents that FLOAT and DOUBLE use IEEE 754 binary representation, which can't precisely store most decimal fractions — making them unsafe for any monetary column.</p>

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

        <figure>
            <svg viewBox="0 0 600 295" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart showing storage size in bytes for each MySQL numeric data type">
                <title>MySQL Numeric Type Storage Sizes</title>
                <rect width="600" height="295" rx="8" fill="#181f2e"/>
                <text x="300" y="28" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="13" font-weight="600">Numeric Type — Storage Size (bytes)</text>
                <text x="125" y="60" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">TINYINT</text>
                <rect x="135" y="46" width="35" height="18" rx="3" fill="#5db583"/>
                <text x="176" y="60" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">1 byte</text>
                <text x="125" y="90" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">SMALLINT</text>
                <rect x="135" y="76" width="70" height="18" rx="3" fill="#5db583"/>
                <text x="211" y="90" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">2 bytes</text>
                <text x="125" y="120" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">INT</text>
                <rect x="135" y="106" width="140" height="18" rx="3" fill="#5db583"/>
                <text x="281" y="120" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">4 bytes</text>
                <text x="125" y="150" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">FLOAT</text>
                <rect x="135" y="136" width="140" height="18" rx="3" fill="#f59e0b" opacity="0.85"/>
                <text x="281" y="150" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">4 bytes (approx.)</text>
                <text x="125" y="180" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">DECIMAL(10,2)</text>
                <rect x="135" y="166" width="210" height="18" rx="3" fill="#60a5fa"/>
                <text x="351" y="180" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">~6 bytes (exact)</text>
                <text x="125" y="210" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">BIGINT</text>
                <rect x="135" y="196" width="280" height="18" rx="3" fill="#5db583"/>
                <text x="421" y="210" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">8 bytes</text>
                <text x="125" y="240" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">DOUBLE</text>
                <rect x="135" y="226" width="280" height="18" rx="3" fill="#f59e0b" opacity="0.85"/>
                <text x="421" y="240" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">8 bytes (approx.)</text>
                <rect x="135" y="261" width="12" height="8" rx="2" fill="#5db583"/>
                <text x="152" y="269" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Integer (exact)</text>
                <rect x="265" y="261" width="12" height="8" rx="2" fill="#f59e0b"/>
                <text x="282" y="269" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Float (approximate)</text>
                <rect x="405" y="261" width="12" height="8" rx="2" fill="#60a5fa"/>
                <text x="422" y="269" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Fixed-point (exact)</text>
                <text x="300" y="286" text-anchor="middle" fill="#475569" font-family="JetBrains Mono, monospace" font-size="9">Source: MySQL 8.0 Reference Manual — dev.mysql.com/doc/refman/8.0/en/storage-requirements.html</text>
            </svg>
            <figcaption>Storage bytes per MySQL numeric type. Green = exact integers, yellow = approximate floating-point, blue = fixed-point DECIMAL.</figcaption>
        </figure>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>TINYINT(1)</code> for booleans — MySQL's conventional boolean representation.</li>
            <li>Use <code>INT UNSIGNED</code> for auto-increment primary keys (doubles the positive range to ~4.3 billion).</li>
            <li>Use <code>BIGINT UNSIGNED</code> for tables that may grow very large.</li>
            <li>Never use <code>FLOAT</code> or <code>DOUBLE</code> for monetary values. Floating-point imprecision causes rounding errors in financial calculations. Use <code>DECIMAL(10, 2)</code> instead.</li>
        </ul>

        <div class="citation-capsule">
            <p>MySQL stores FLOAT and DOUBLE using the IEEE 754 binary floating-point standard, which cannot precisely represent most decimal fractions (<a href="https://dev.mysql.com/doc/refman/8.0/en/floating-point-types.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>). For monetary calculations, DECIMAL(10,2) stores exact values and prevents the rounding errors that floating-point types introduce in financial totals.</p>
        </div>

        <h2 id="string-types">String Types</h2>

        <p>VARCHAR covers most string storage needs. It's variable-width: MySQL allocates only what the content requires, plus 1-2 bytes of length overhead. CHAR, by contrast, always occupies exactly n bytes — a 10-character value in a <code>CHAR(50)</code> column still consumes all 50 bytes. That fixed width makes CHAR marginally faster to index, which is why the <a href="https://dev.mysql.com/doc/refman/8.0/en/char.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a> recommends it for fixed-length data like ISO country codes (<code>CHAR(2)</code>) or MD5 hashes (<code>CHAR(32)</code>).</p>

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
            <li>Use <code>VARCHAR(n)</code> for most string columns. Set <code>n</code> to a realistic maximum: <code>VARCHAR(255)</code> for names and emails, <code>VARCHAR(2048)</code> for URLs.</li>
            <li>Use <code>CHAR(n)</code> only when the value is always the same length. It's marginally faster to index than <code>VARCHAR</code>.</li>
            <li>Don't put <code>TEXT</code> columns in <code>WHERE</code> clauses without a full-text index. It forces a full table scan regardless of how specific your filter is.</li>
            <li>Avoid <code>ENUM</code>. It's inflexible to alter and opaque to external tools. Use a <code>VARCHAR</code> with a <code>CHECK</code> constraint, or a separate lookup table instead.</li>
        </ul>

        <h3>Binary types: the BLOB family</h3>
        <p>The BLOB family stores binary data — file contents, images, encrypted payloads — with no character set interpretation. The size tiers mirror the TEXT family exactly:</p>
        <table>
            <tr>
                <th>Type</th>
                <th>Max size</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>TINYBLOB</code></td>
                <td>255 bytes</td>
                <td>Very small binary values: icons, thumbnails</td>
            </tr>
            <tr>
                <td><code>BLOB</code></td>
                <td>65,535 bytes (64 KB)</td>
                <td>Small binary objects: low-res images, short files</td>
            </tr>
            <tr>
                <td><code>MEDIUMBLOB</code></td>
                <td>16,777,215 bytes (16 MB)</td>
                <td>Documents, audio clips, mid-size files</td>
            </tr>
            <tr>
                <td><code>LONGBLOB</code></td>
                <td>4,294,967,295 bytes (4 GB)</td>
                <td>Large files, video, binary archives</td>
            </tr>
        </table>
        <p>In SQL Designer, we had to handle the BLOB family separately from TEXT during MySQL DDL generation — they share the same size hierarchy but have different character set semantics, and generating <code>CHARACTER SET</code> clauses for a BLOB column produces an error. Worth knowing if you're writing DDL tooling: BLOB columns don't accept charset or collation attributes, while the equivalent TEXT types do. In practice, storing files larger than a few kilobytes in MySQL directly is rarely the right architectural choice; object storage (S3, GCS) scales better. Use BLOB for small binary values tightly coupled to their parent row, where the overhead of a separate storage system isn't justified (<a href="https://dev.mysql.com/doc/refman/8.0/en/blob.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>).</p>

        <div class="citation-capsule">
            <p>VARCHAR and CHAR differ primarily in storage behavior: CHAR(n) always occupies exactly n bytes, padded with spaces, while VARCHAR(n) uses 1 byte of overhead for values up to 255 characters plus actual content length (<a href="https://dev.mysql.com/doc/refman/8.0/en/char.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>). For columns where every value is exactly the same length, CHAR is marginally more efficient to index than VARCHAR.</p>
        </div>

        <h2 id="date-and-time-types">Date and Time Types</h2>

        <p>The TIMESTAMP versus DATETIME decision has real production consequences. TIMESTAMP stores every value in UTC and reconverts to the session timezone on retrieval, making it automatic for audit columns like <code>created_at</code>. Its hard upper limit is <strong>2038-01-19</strong> — the point where a 32-bit Unix timestamp overflows. Any application storing future dates past that boundary must use DATETIME, which supports dates through year 9999 (<a href="https://dev.mysql.com/doc/refman/8.0/en/datetime.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>).</p>

        <figure>
            <picture>
                <source srcset="https://images.unsplash.com/photo-1435527173128-983b87201f4d?fm=avif&q=80&w=1400&auto=format&fit=crop" type="image/avif">
                <source srcset="https://images.unsplash.com/photo-1435527173128-983b87201f4d?fm=webp&q=80&w=1400&auto=format&fit=crop" type="image/webp">
                <img src="https://images.unsplash.com/photo-1435527173128-983b87201f4d?fm=jpg&q=80&w=1400&auto=format&fit=crop"
                     alt="Open desk calendar illustrating MySQL date and time data type selection for created_at and scheduled event columns"
                     loading="lazy" width="1400" height="700">
            </picture>
            <figcaption>Use TIMESTAMP for audit columns; use DATETIME when you need timezone-independent storage or dates beyond 2038.</figcaption>
        </figure>

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
            <li>Use <code>TIMESTAMP</code> for <code>created_at</code> and <code>updated_at</code> audit columns. It auto-converts to UTC on storage and back to the session timezone on retrieval.</li>
            <li>Use <code>DATETIME</code> when you need to store a specific wall-clock time without timezone conversion, e.g., a scheduled event that should fire at 9am regardless of timezone.</li>
            <li><code>TIMESTAMP</code> has a 2038 ceiling. For future dates beyond that, use <code>DATETIME</code>.</li>
        </ul>

        <div class="citation-capsule">
            <p>MySQL's TIMESTAMP type stores values internally as UTC and converts to the current session timezone on retrieval (<a href="https://dev.mysql.com/doc/refman/8.0/en/datetime.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>). Its upper limit is 2038-01-19 03:14:07 UTC, the 32-bit signed integer overflow of Unix time. Applications storing reservation or event dates beyond 2038 must use DATETIME, which has no such ceiling.</p>
        </div>

        <h2 id="json-type">JSON Type</h2>

        <p>MySQL 5.7 introduced native JSON storage; MySQL 8.0 expanded the operator set considerably. A JSON column validates syntax on insert and rejects malformed data outright, preventing the silent corruption that storing JSON in a TEXT column allows. Path expressions use dot notation: <code>data-&gt;'$.key'</code> is shorthand for <code>JSON_EXTRACT(data, '$.key')</code>, and the <code>-&gt;&gt;</code> operator returns an unquoted scalar value directly.</p>

        <pre><code>-- Read a single JSON path value
SELECT config->'$.theme' FROM user_settings WHERE user_id = 1;

-- Unquoted scalar (MySQL 5.7.13+)
SELECT config->>'$.theme' FROM user_settings WHERE user_id = 1;

-- Filter rows by JSON value
SELECT * FROM users WHERE preferences->>'$.notifications' = 'true';</code></pre>

        <p>Use <code>JSON</code> for truly variable or schema-less data: feature flags, user preferences, integration payloads. Don't use it to avoid modelling your data properly. If the same key appears in every row, it should be a regular column. JSON columns can't be indexed directly — only generated columns derived from JSON paths can be indexed — so querying inside JSON at scale requires careful planning. See our <a href="/blog/database-schema-examples">database schema examples</a> for how to mix JSON columns with conventional structure.</p>

        <div class="citation-capsule">
            <p>MySQL's native JSON type validates document syntax at insert time and enables path-based queries using the <code>-&gt;</code> and <code>-&gt;&gt;</code> operators, introduced in MySQL 5.7 and expanded in 8.0 (<a href="https://dev.mysql.com/doc/refman/8.0/en/json.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>). Unlike TEXT storage, a JSON column rejects malformed input and permits selective path extraction without parsing the full document in application code.</p>
        </div>

        <h2 id="common-mistakes">What Are the Most Common MySQL Data Type Mistakes?</h2>

        <p>Three type selection errors show up repeatedly in production schemas. They're easy to avoid at design time and expensive to fix once a table has millions of rows.</p>

        <p><strong>1. FLOAT or DOUBLE for monetary values.</strong> This is the most damaging one. A column defined as <code>FLOAT</code> can't store <code>0.1</code> exactly — it stores the nearest IEEE 754 approximation. Sum enough rows and the total drifts from the correct value. Use <code>DECIMAL(10, 2)</code> for any price, fee, or financial amount. No exceptions. The <a href="/blog/database-normalization">database normalization guide</a> covers how this fits into a well-structured financial schema.</p>

        <p><strong>2. TEXT columns in WHERE clauses without a FULLTEXT index.</strong> A query like <code>WHERE body LIKE '%keyword%'</code> against a TEXT column does a full table scan every time. On a table with 50,000 rows that's slow; on a table with 5 million rows it's a timeout. Add a <code>FULLTEXT</code> index, store a searchable excerpt in a <code>VARCHAR</code> column, or move search to a dedicated tool. The <a href="/blog/mysql-indexes">MySQL indexes guide</a> explains how B-tree, FULLTEXT, and composite index choices affect these scans.</p>

        <p><strong>3. ENUM instead of a lookup table.</strong> ENUM enforces allowed values at the database level, which sounds useful. The problem is ALTER TABLE. Adding a new ENUM value in MySQL 5.x rewrites the entire table — a blocking operation on a live database. In MySQL 8.0 this is instant for appended values, but the opaque storage still confuses ORMs and external tooling. A <code>VARCHAR(50)</code> with a <code>CHECK</code> constraint, or a foreign key to a <code>status_types</code> lookup table, is far more maintainable. Your future self will thank you when the business adds a new status at 11pm on a Friday.</p>

        <h2 id="quick-reference">Quick Reference: Common Column Patterns</h2>
        <ul>
            <li><strong>Primary key:</strong> <code>INT UNSIGNED NOT NULL AUTO_INCREMENT</code></li>
            <li><strong>Large-table primary key:</strong> <code>BIGINT UNSIGNED NOT NULL AUTO_INCREMENT</code></li>
            <li><strong>UUID / GUID:</strong> <code>CHAR(36) NOT NULL</code> (or <code>BINARY(16)</code> with <code>UUID_TO_BIN()</code> in MySQL 8.0+ for storage efficiency)</li>
            <li><strong>Email address:</strong> <code>VARCHAR(255) NOT NULL UNIQUE</code></li>
            <li><strong>Password hash:</strong> <code>VARCHAR(255) NOT NULL</code></li>
            <li><strong>URL slug:</strong> <code>VARCHAR(255) NOT NULL</code></li>
            <li><strong>Price / monetary value:</strong> <code>DECIMAL(10, 2) NOT NULL</code></li>
            <li><strong>Boolean flag:</strong> <code>TINYINT(1) NOT NULL DEFAULT 0</code></li>
            <li><strong>Created/updated timestamps:</strong> <code>TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP</code></li>
            <li><strong>Long-form text:</strong> <code>TEXT</code></li>
            <li><strong>Foreign key:</strong> Same type as the referenced primary key, e.g. <code>INT UNSIGNED NOT NULL</code></li>
        </ul>

        <p class="video-label">Video Resource</p>
        <div class="video-wrap">
            <iframe
                src="https://www.youtube-nocookie.com/embed/5OdVJbNCSso"
                title="MySQL Full Course — Bro Code (2023), covering data types, schema design, and queries"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                aria-label="YouTube video: MySQL Full Course by Bro Code covering all MySQL data types and schema design"></iframe>
            <noscript>
                <a href="https://www.youtube.com/watch?v=5OdVJbNCSso">MySQL Full Course — Bro Code (YouTube, 2023)</a>
            </noscript>
        </div>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3 class="faq-q">What MySQL data type should I use for storing money?</h3>
                <p class="faq-a">Use <code>DECIMAL(p, s)</code> — for example <code>DECIMAL(10, 2)</code> for two decimal places. Never use <code>FLOAT</code> or <code>DOUBLE</code> for money. Both use IEEE 754 binary floating-point, which cannot represent most decimal fractions exactly, causing rounding errors that accumulate in financial totals.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What is the difference between DATETIME and TIMESTAMP in MySQL?</h3>
                <p class="faq-a"><code>TIMESTAMP</code> stores values in UTC and automatically converts to the session timezone on retrieval, making it ideal for <code>created_at</code> and <code>updated_at</code> audit columns. <code>DATETIME</code> stores the literal wall-clock time without timezone conversion and has a wider date range (up to year 9999 vs TIMESTAMP's 2038-01-19 limit).</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What MySQL type should I use for boolean columns?</h3>
                <p class="faq-a">MySQL has no native boolean type. The convention is <code>TINYINT(1)</code>, which stores 0 (false) or 1 (true). ORMs like Laravel and Rails treat <code>TINYINT(1)</code> as a boolean automatically. MySQL 8.0+ also accepts <code>BOOLEAN</code> as a synonym for <code>TINYINT(1)</code>.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">When should I use VARCHAR vs TEXT in MySQL?</h3>
                <p class="faq-a">Use <code>VARCHAR(n)</code> for short strings where you know the maximum length: names, emails, URLs, slugs. Use <code>TEXT</code> for long-form content such as article bodies, descriptions, or HTML where the length is unpredictable. Avoid using TEXT columns in <code>WHERE</code> clauses without a full-text index, as it forces a full table scan.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What MySQL data type should I use for a primary key?</h3>
                <p class="faq-a"><code>INT UNSIGNED NOT NULL AUTO_INCREMENT</code> is the standard choice for most tables, supporting up to approximately 4.3 billion rows. Use <code>BIGINT UNSIGNED NOT NULL AUTO_INCREMENT</code> for tables expected to grow very large, such as event logs or high-volume transactional tables.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Why should I avoid ENUM in MySQL?</h3>
                <p class="faq-a"><code>ENUM</code> stores allowed values as a one- or two-byte integer mapped to a list of strings. Adding a new value requires an <code>ALTER TABLE</code> that rewrites the entire table in older MySQL versions, causing downtime on large tables. ENUM values are also opaque to ORMs and external tools. A <code>VARCHAR</code> column with a <code>CHECK</code> constraint, or a separate lookup table, is more maintainable and easier to extend.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What MySQL data type should I use for a UUID?</h3>
                <p class="faq-a"><code>CHAR(36)</code> stores the standard hyphenated UUID string. For storage efficiency, MySQL 8.0+ provides <code>UUID_TO_BIN()</code> and <code>BIN_TO_UUID()</code> to convert a UUID into <code>BINARY(16)</code>, halving the storage compared to <code>CHAR(36)</code> and improving index performance. Use <code>CHAR(36)</code> for readability; use <code>BINARY(16)</code> for high-volume tables where index size matters.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
                <li><a href="/blog/mysql-indexes">MySQL Composite Indexes and the Leftmost-Prefix Rule &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — data type differences &rarr;</a></li>
                <li><a href="/blog/database-ddl-comparison">DDL syntax across MySQL, PostgreSQL, and SQLite &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database normalization — first through third normal form &rarr;</a></li>
                <li><a href="/blog/postgresql-data-types">PostgreSQL Data Types — BOOLEAN, JSONB, UUID, TIMESTAMPTZ &rarr;</a></li>
                <li><a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">SQL-Aware vs. Generic ER Diagram Tools &rarr;</a></li>
                <li><a href="/blog/sql-joins">SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL &rarr;</a></li>
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
