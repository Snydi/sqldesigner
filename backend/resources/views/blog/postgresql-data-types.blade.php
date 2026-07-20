@extends('layouts.main')

@section('title', 'PostgreSQL Data Types Explained — Which to Use and When')

@section('head')
    <meta name="description"
          content="PostgreSQL's 42+ built-in types span numeric, text, boolean, date/time, JSONB, arrays, and UUID. Learn which to choose and how they differ from MySQL.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/postgresql-data-types">
    <meta property="og:title" content="PostgreSQL Data Types Explained — Which to Use and When">
    <meta property="og:description"
          content="PostgreSQL's 42+ built-in types span numeric, text, boolean, date/time, JSONB, arrays, and UUID. Learn which to choose and how they differ from MySQL.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/postgresql-data-types">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="PostgreSQL Data Types Explained — Which to Use and When">
    <meta name="twitter:description" content="PostgreSQL's 42+ built-in types span numeric, text, boolean, date/time, JSONB, arrays, and UUID. Learn which to choose and how they differ from MySQL.">
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
                { "@type": "ListItem", "position": 3, "name": "PostgreSQL Data Types Explained", "item": "https://sql-designer.com/blog/postgresql-data-types" }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "TechArticle",
            "headline": "PostgreSQL Data Types Explained — Which to Use and When",
            "description": "A practical guide to PostgreSQL data types: numeric, text, boolean, date/time, JSONB, arrays, and UUID — with advice on which to choose and how they compare to MySQL.",
            "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
            "url": "https://sql-designer.com/blog/postgresql-data-types",
            "datePublished": "2026-05-23",
            "dateModified": "2026-05-23",
            "wordCount": 2400,
            "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
            "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
            "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub", ".key-takeaways"] },
            "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/postgresql-data-types" }
        },
        {
            "@context": "https://schema.org",
            "@type": "VideoObject",
            "name": "Understanding Data Types in PostgreSQL — CHAR, VARCHAR, TEXT, and More",
            "description": "A practical guide to choosing the right PostgreSQL data types, covering CHAR, VARCHAR, TEXT, and their differences in storage and behavior.",
            "thumbnailUrl": "https://img.youtube.com/vi/kCK6VD1rzT0/hqdefault.jpg",
            "uploadDate": "2024-12-19",
            "embedUrl": "https://www.youtube.com/embed/kCK6VD1rzT0",
            "url": "https://www.youtube.com/watch?v=kCK6VD1rzT0"
        },
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "What is the difference between NUMERIC and DECIMAL in PostgreSQL?",
                    "acceptedAnswer": { "@type": "Answer", "text": "NUMERIC and DECIMAL are identical in PostgreSQL. They are aliases for the same exact-precision type. Both accept NUMERIC(precision, scale) and DECIMAL(precision, scale) with the same behavior. Use either name; NUMERIC is slightly more common in PostgreSQL convention." }
                },
                {
                    "@type": "Question",
                    "name": "Should I use TIMESTAMP or TIMESTAMPTZ in PostgreSQL?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Prefer TIMESTAMPTZ (timestamp with time zone) for almost all use cases. PostgreSQL stores TIMESTAMPTZ values in UTC and converts to the client session timezone on retrieval, preventing timezone-related bugs. Plain TIMESTAMP (without time zone) stores the literal value with no conversion and is only appropriate when the application handles timezone logic itself." }
                },
                {
                    "@type": "Question",
                    "name": "What is the difference between JSON and JSONB in PostgreSQL?",
                    "acceptedAnswer": { "@type": "Answer", "text": "JSON stores the document as-is in text form, preserving whitespace, key order, and duplicate keys. JSONB parses and stores the document in a decomposed binary format, which is faster to query and supports GIN indexes for full-document search. Use JSONB unless you need to preserve exact input representation." }
                },
                {
                    "@type": "Question",
                    "name": "How do I store a UUID primary key in PostgreSQL?",
                    "acceptedAnswer": { "@type": "Answer", "text": "PostgreSQL has a native UUID type that stores the value as 16 bytes internally. Declare the column as UUID DEFAULT gen_random_uuid() PRIMARY KEY (PostgreSQL 13+) or uuid_generate_v4() with the uuid-ossp extension. The UUID type is natively indexable and sortable, with no manual conversion functions required." }
                },
                {
                    "@type": "Question",
                    "name": "What is the PostgreSQL equivalent of MySQL AUTO_INCREMENT?",
                    "acceptedAnswer": { "@type": "Answer", "text": "PostgreSQL offers two options. The SERIAL pseudo-type (legacy but still common) creates an implicit sequence. The SQL-standard alternative is GENERATED ALWAYS AS IDENTITY or GENERATED BY DEFAULT AS IDENTITY, available since PostgreSQL 10. Prefer GENERATED ALWAYS AS IDENTITY in new schemas. It is standards-compliant and gives more explicit control over sequence behavior." }
                },
                {
                    "@type": "Question",
                    "name": "Does PostgreSQL have a native boolean type?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Yes. PostgreSQL's BOOLEAN type stores true, false, or NULL natively. It accepts multiple input formats: TRUE/FALSE, 't'/'f', 'yes'/'no', 'on'/'off', and 1/0. This is a key difference from MySQL, which has no native boolean and uses TINYINT(1) as a convention instead." }
                }
            ]
        }
        ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>PostgreSQL</span></p>
        <p class="post-eyebrow">May 2026 · <time datetime="2026-05-23">Last updated: May 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 10 min read</p>
        <h1 class="page-h1">PostgreSQL Data Types Explained — Which to Use and When</h1>
        <p class="page-sub">PostgreSQL provides 42+ built-in data types across eight categories: numeric (<code>INTEGER</code>, <code>NUMERIC</code>, <code>REAL</code>), text (<code>VARCHAR</code>, <code>TEXT</code>), boolean, date/time (<code>DATE</code>, <code>TIMESTAMPTZ</code>, <code>INTERVAL</code>), JSON/JSONB, arrays, UUID, and identity/serial columns. Each type has specific storage requirements, range limits, and behavioral differences from MySQL equivalents. This guide covers which type to choose for each common use case, with <code>CREATE TABLE</code> examples throughout.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#numeric-types">Numeric Types</a></li>
            <li><a href="#text-types">Text Types</a></li>
            <li><a href="#boolean">Boolean</a></li>
            <li><a href="#date-time-types">Date and Time Types</a></li>
            <li><a href="#json-jsonb">JSON and JSONB</a></li>
            <li><a href="#arrays">Arrays</a></li>
            <li><a href="#uuid-identity">UUID and Identity</a></li>
            <li><a href="#quick-reference">Quick Reference</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>PostgreSQL's <code>BOOLEAN</code>, <code>UUID</code>, and native array types have no direct MySQL equivalents. They require workarounds (<code>TINYINT(1)</code>, <code>CHAR(36)</code>, and junction tables) in MySQL.</li>
                <li>Use <code>TIMESTAMPTZ</code> (not plain <code>TIMESTAMP</code>) for almost all datetime columns. It stores UTC and converts to the client timezone automatically, with no 2038 ceiling.</li>
                <li>Prefer <code>JSONB</code> over <code>JSON</code>. JSONB stores data in binary format for faster queries and supports GIN indexes. Plain JSON preserves input text and is slower to query.</li>
                <li>Use <code>GENERATED ALWAYS AS IDENTITY</code> over <code>SERIAL</code> in new schemas. It's the SQL standard and gives explicit sequence control.</li>
                <li>Use <code>NUMERIC(p,s)</code> for money. <code>REAL</code> and <code>DOUBLE PRECISION</code> use IEEE 754 floating-point and can't store most decimal fractions exactly.</li>
            </ul>
        </div>

        <figure>
            <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?fm=jpg&q=80&w=1400&auto=format&fit=crop"
                 alt="Code on a monitor screen representing PostgreSQL database schema design and data type selection"
                 loading="lazy" width="1400" height="700">
            <figcaption>PostgreSQL's type system is richer than MySQL's. Choosing the right type at schema design time avoids expensive migrations later.</figcaption>
        </figure>

        <h2 id="numeric-types">Numeric Types</h2>

        <p>PostgreSQL ships six standard numeric types, from 2-byte <code>SMALLINT</code> to variable-length arbitrary-precision <code>NUMERIC</code>. The critical split is exact versus approximate storage. The <a href="https://www.postgresql.org/docs/current/datatype-numeric.html" target="_blank" rel="noopener">PostgreSQL documentation</a> defines NUMERIC as storing values exactly as entered, with no rounding, up to 131,072 digits before the decimal point. That's what makes it the correct choice for money and any calculation where rounding errors compound.</p>

        <table>
            <tr>
                <th>Type</th>
                <th>Storage</th>
                <th>Range</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>SMALLINT</code></td>
                <td>2 bytes</td>
                <td>-32,768 to 32,767</td>
                <td>Small counters, age, status codes</td>
            </tr>
            <tr>
                <td><code>INTEGER</code> / <code>INT</code></td>
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
                <td><code>NUMERIC(p,s)</code> / <code>DECIMAL(p,s)</code></td>
                <td>Variable</td>
                <td>Up to 131,072 digits before decimal</td>
                <td>Money, precise measurements</td>
            </tr>
            <tr>
                <td><code>REAL</code></td>
                <td>4 bytes</td>
                <td>~6 decimal digits precision</td>
                <td>Scientific values where small errors are acceptable</td>
            </tr>
            <tr>
                <td><code>DOUBLE PRECISION</code></td>
                <td>8 bytes</td>
                <td>~15 decimal digits precision</td>
                <td>Scientific computing, GIS coordinates</td>
            </tr>
        </table>

        <figure>
            <svg viewBox="0 0 600 295" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart showing storage size in bytes for each PostgreSQL numeric data type">
                <title>PostgreSQL Numeric Type Storage Sizes</title>
                <rect width="600" height="295" rx="8" fill="#181f2e"/>
                <text x="300" y="28" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="13" font-weight="600">Numeric Type — Storage Size (bytes)</text>
                <text x="155" y="60" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">SMALLINT</text>
                <rect x="165" y="46" width="35" height="18" rx="3" fill="#5db583"/>
                <text x="206" y="60" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">2 bytes</text>
                <text x="155" y="90" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">REAL</text>
                <rect x="165" y="76" width="70" height="18" rx="3" fill="#f59e0b" opacity="0.85"/>
                <text x="241" y="90" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">4 bytes (approx.)</text>
                <text x="155" y="120" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">INTEGER</text>
                <rect x="165" y="106" width="70" height="18" rx="3" fill="#5db583"/>
                <text x="241" y="120" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">4 bytes</text>
                <text x="155" y="150" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">NUMERIC(10,2)</text>
                <rect x="165" y="136" width="105" height="18" rx="3" fill="#60a5fa"/>
                <text x="276" y="150" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">~6 bytes (exact)</text>
                <text x="155" y="180" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">DOUBLE PRECISION</text>
                <rect x="165" y="166" width="140" height="18" rx="3" fill="#f59e0b" opacity="0.85"/>
                <text x="311" y="180" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">8 bytes (approx.)</text>
                <text x="155" y="210" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="11">BIGINT</text>
                <rect x="165" y="196" width="140" height="18" rx="3" fill="#5db583"/>
                <text x="311" y="210" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="11">8 bytes</text>
                <rect x="165" y="241" width="12" height="8" rx="2" fill="#5db583"/>
                <text x="182" y="249" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Integer (exact)</text>
                <rect x="305" y="241" width="12" height="8" rx="2" fill="#f59e0b"/>
                <text x="322" y="249" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Float (approximate)</text>
                <rect x="455" y="241" width="12" height="8" rx="2" fill="#60a5fa"/>
                <text x="472" y="249" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Fixed-point (exact)</text>
                <text x="300" y="282" text-anchor="middle" fill="#475569" font-family="JetBrains Mono, monospace" font-size="9">Source: PostgreSQL Documentation — postgresql.org/docs/current/datatype-numeric.html</text>
            </svg>
            <figcaption>Storage bytes per PostgreSQL numeric type. Green = exact integers, yellow = approximate floating-point, blue = fixed-point NUMERIC.</figcaption>
        </figure>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li><code>NUMERIC</code> and <code>DECIMAL</code> are aliases. They're identical in PostgreSQL. Use <code>NUMERIC(10,2)</code> for monetary columns.</li>
            <li>Never use <code>REAL</code> or <code>DOUBLE PRECISION</code> for money. Both use IEEE 754 binary floating-point, which can't represent most decimal fractions exactly.</li>
            <li>Unlike MySQL, PostgreSQL has no <code>UNSIGNED</code> integers. Use <code>BIGINT</code> if you need a larger positive range than <code>INTEGER</code> provides.</li>
            <li>PostgreSQL doesn't have <code>TINYINT</code>. Use <code>SMALLINT</code> for small integer columns, or <code>BOOLEAN</code> for flags.</li>
        </ul>

        <div class="citation-capsule">
            <p>PostgreSQL's NUMERIC type stores values with user-specified precision and scale, with no rounding at any step, in contrast to REAL and DOUBLE PRECISION which use IEEE 754 binary floating-point representation (<a href="https://www.postgresql.org/docs/current/datatype-numeric.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). For monetary values, <code>NUMERIC(10,2)</code> is the standard: it stores exactly two decimal places with no floating-point drift, regardless of how many rows accumulate.</p>
        </div>

        <h2 id="text-types">Text Types</h2>

        <p>PostgreSQL's three text types share the same underlying storage engine. That's worth knowing upfront because it changes how you choose between them. The <a href="https://www.postgresql.org/docs/current/datatype-character.html" target="_blank" rel="noopener">PostgreSQL documentation</a> is explicit: <code>VARCHAR(n)</code>, <code>CHAR(n)</code>, and <code>TEXT</code> all use identical storage. There's no performance difference between <code>TEXT</code> and <code>VARCHAR</code> in PostgreSQL. You don't need prefix indexes to search a <code>TEXT</code> column, and both can be indexed directly with B-tree or GIN.</p>

        <table>
            <tr>
                <th>Type</th>
                <th>Constraint</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>CHAR(n)</code></td>
                <td>Exactly n chars (space-padded)</td>
                <td>Fixed-length codes: country codes (<code>CHAR(2)</code>), MD5 hashes</td>
            </tr>
            <tr>
                <td><code>VARCHAR(n)</code></td>
                <td>Up to n chars</td>
                <td>Length-bounded strings: emails, slugs, titles</td>
            </tr>
            <tr>
                <td><code>TEXT</code></td>
                <td>Unlimited</td>
                <td>Any string content; same performance as VARCHAR in PostgreSQL</td>
            </tr>
        </table>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>TEXT</code> for string columns without a known maximum length. PostgreSQL stores and queries it identically to <code>VARCHAR</code>.</li>
            <li>Use <code>VARCHAR(n)</code> when you want the database to enforce a maximum length at insert time, for example <code>VARCHAR(255)</code> for email addresses.</li>
            <li>Use <code>CHAR(n)</code> only for truly fixed-length values: ISO country codes, currency codes, fixed hash strings.</li>
            <li>Unlike MySQL, <code>TEXT</code> in PostgreSQL doesn't require a prefix length for B-tree indexes and can appear in any <code>WHERE</code> clause without a full-table scan when indexed.</li>
        </ul>

        <div class="citation-capsule">
            <p>In PostgreSQL, TEXT and VARCHAR(n) use the same storage mechanism and have identical performance characteristics (<a href="https://www.postgresql.org/docs/current/datatype-character.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). The only practical difference is that VARCHAR(n) enforces a maximum character count at the database level. Unlike MySQL, PostgreSQL TEXT columns can be indexed directly with B-tree, with no prefix length required.</p>
        </div>

        <h2 id="boolean">Boolean — Native True/False Storage</h2>

        <p>PostgreSQL has a native <code>BOOLEAN</code> type that stores <code>true</code>, <code>false</code>, or <code>NULL</code> natively, with no convention required. This is a real difference from MySQL, which has no native boolean type and uses <code>TINYINT(1)</code> as a convention, requiring ORM mapping to handle boolean coercion correctly. The <a href="https://www.postgresql.org/docs/current/datatype-boolean.html" target="_blank" rel="noopener">PostgreSQL documentation</a> lists multiple accepted input literals: <code>TRUE</code>/<code>FALSE</code>, <code>'t'</code>/<code>'f'</code>, <code>'yes'</code>/<code>'no'</code>, <code>'on'</code>/<code>'off'</code>, and <code>1</code>/<code>0</code>.</p>

        <pre><code>CREATE TABLE user_settings (
    id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id    BIGINT NOT NULL REFERENCES users(id),
    email_opt  BOOLEAN NOT NULL DEFAULT true,
    dark_mode  BOOLEAN NOT NULL DEFAULT false,
    verified   BOOLEAN NOT NULL DEFAULT false
);</code></pre>

        <p>In practice, stick to <code>TRUE</code>/<code>FALSE</code> literals in application code. PostgreSQL accepts the abbreviated forms, but explicit literals are clearer in queries and migrations. ORMs like Django, Rails, and Laravel all handle PostgreSQL's BOOLEAN type natively without extra column configuration. Contrast this with MySQL, where ORM-level boolean columns silently map to <code>TINYINT(1)</code> and can store values other than 0 and 1. Building SQL Designer's schema export tool, we ran into exactly this gap when generating MySQL DDL from PostgreSQL boolean columns: the types don't map 1:1, and that difference shows up at runtime.</p>

        <div class="citation-capsule">
            <p>PostgreSQL's native BOOLEAN type stores true, false, or NULL, accepting multiple input formats including TRUE/FALSE, 't'/'f', 'yes'/'no', and 1/0 (<a href="https://www.postgresql.org/docs/current/datatype-boolean.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). MySQL has no equivalent native type. It represents booleans as TINYINT(1), a convention that ORMs must explicitly map and one that doesn't enforce the 0/1 constraint at the database level.</p>
        </div>

        <h2 id="date-time-types">Date and Time Types</h2>

        <p>PostgreSQL's datetime type set is more expressive than MySQL's. The most important decision is <code>TIMESTAMP</code> (no timezone) versus <code>TIMESTAMPTZ</code> (with timezone). According to the <a href="https://www.postgresql.org/docs/current/datatype-datetime.html" target="_blank" rel="noopener">PostgreSQL documentation</a>, <code>TIMESTAMPTZ</code> stores all values as UTC and converts to the session's <code>TimeZone</code> setting on output. This is behaviorally equivalent to MySQL's <code>TIMESTAMP</code>, but without MySQL's hard ceiling of 2038-01-19.</p>

        <table>
            <tr>
                <th>Type</th>
                <th>Range</th>
                <th>Use for</th>
            </tr>
            <tr>
                <td><code>DATE</code></td>
                <td>4713 BC to 5874897 AD</td>
                <td>Dates without time: birthdays, deadlines</td>
            </tr>
            <tr>
                <td><code>TIME</code></td>
                <td>00:00:00 to 24:00:00</td>
                <td>Time-of-day without a date component</td>
            </tr>
            <tr>
                <td><code>TIMESTAMP</code></td>
                <td>4713 BC to 294276 AD</td>
                <td>Wall-clock datetime stored without timezone conversion</td>
            </tr>
            <tr>
                <td><code>TIMESTAMPTZ</code></td>
                <td>4713 BC to 294276 AD</td>
                <td>Audit timestamps (<code>created_at</code>, <code>updated_at</code>), stored as UTC</td>
            </tr>
            <tr>
                <td><code>INTERVAL</code></td>
                <td>±178,000,000 years</td>
                <td>Durations, date arithmetic: <code>INTERVAL '7 days'</code>, <code>INTERVAL '1 month'</code></td>
            </tr>
        </table>

        <figure>
            <svg viewBox="0 0 600 230" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Comparison chart showing PostgreSQL TIMESTAMPTZ vs MySQL TIMESTAMP differences">
                <title>TIMESTAMPTZ vs MySQL TIMESTAMP: Key Differences</title>
                <rect width="600" height="230" rx="8" fill="#181f2e"/>
                <text x="300" y="28" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="13" font-weight="600">Timestamp Types: PostgreSQL vs MySQL</text>
                <text x="155" y="55" text-anchor="middle" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10" font-weight="600">Feature</text>
                <text x="370" y="55" text-anchor="middle" fill="#60a5fa" font-family="JetBrains Mono, monospace" font-size="10" font-weight="600">PostgreSQL TIMESTAMPTZ</text>
                <text x="530" y="55" text-anchor="middle" fill="#f59e0b" font-family="JetBrains Mono, monospace" font-size="10" font-weight="600">MySQL TIMESTAMP</text>
                <line x1="20" y1="62" x2="580" y2="62" stroke="#2d3748" stroke-width="1"/>
                <text x="30" y="82" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Stores as UTC</text>
                <text x="370" y="82" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Yes</text>
                <text x="530" y="82" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Yes</text>
                <line x1="20" y1="90" x2="580" y2="90" stroke="#1e293b" stroke-width="1"/>
                <text x="30" y="110" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Upper limit</text>
                <text x="370" y="110" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Year 294276</text>
                <text x="530" y="110" text-anchor="middle" fill="#ef4444" font-family="JetBrains Mono, monospace" font-size="10">2038-01-19</text>
                <line x1="20" y1="118" x2="580" y2="118" stroke="#1e293b" stroke-width="1"/>
                <text x="30" y="138" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Storage</text>
                <text x="370" y="138" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="10">8 bytes</text>
                <text x="530" y="138" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="10">4 bytes</text>
                <line x1="20" y1="146" x2="580" y2="146" stroke="#1e293b" stroke-width="1"/>
                <text x="30" y="166" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">TZ conversion on output</text>
                <text x="370" y="166" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Automatic</text>
                <text x="530" y="166" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Automatic</text>
                <line x1="20" y1="174" x2="580" y2="174" stroke="#1e293b" stroke-width="1"/>
                <text x="30" y="194" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">DEFAULT NOW()</text>
                <text x="370" y="194" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Yes</text>
                <text x="530" y="194" text-anchor="middle" fill="#5db583" font-family="JetBrains Mono, monospace" font-size="10">Yes</text>
                <text x="300" y="220" text-anchor="middle" fill="#475569" font-family="JetBrains Mono, monospace" font-size="9">Source: PostgreSQL Documentation — postgresql.org/docs/current/datatype-datetime.html</text>
            </svg>
            <figcaption>TIMESTAMPTZ and MySQL's TIMESTAMP both store UTC and auto-convert on output. TIMESTAMPTZ uses 8 bytes and has no 2038 ceiling.</figcaption>
        </figure>

        <p><strong>Key rules:</strong></p>
        <ul>
            <li>Use <code>TIMESTAMPTZ</code> for <code>created_at</code>, <code>updated_at</code>, and any event or log timestamp. It's timezone-safe and has no 2038 problem.</li>
            <li>Use plain <code>TIMESTAMP</code> only when you intentionally want wall-clock time without timezone conversion. That's rare in practice.</li>
            <li>Use <code>INTERVAL</code> for storing durations: subscription lengths, time-since-last-action, recurring event offsets.</li>
            <li>PostgreSQL's <code>DATE</code> range extends to 5874897 AD, and back to 4713 BC. Useful for historical data and long-term scheduling applications.</li>
        </ul>

        <div class="citation-capsule">
            <p>PostgreSQL's TIMESTAMPTZ stores all values internally as UTC and converts them to the client session's TimeZone setting on retrieval (<a href="https://www.postgresql.org/docs/current/datatype-datetime.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). Unlike MySQL's TIMESTAMP, which has a hard ceiling of 2038-01-19 due to 32-bit Unix timestamp overflow, TIMESTAMPTZ's range extends to year 294276, making it safe for any date that might ever need to be stored.</p>
        </div>

        <h2 id="json-jsonb">JSON and JSONB — Binary JSON Storage</h2>

        <p>PostgreSQL offers two JSON types, and the difference isn't cosmetic. The <a href="https://www.postgresql.org/docs/current/datatype-json.html" target="_blank" rel="noopener">PostgreSQL documentation</a> explains that <code>JSON</code> stores the input text verbatim, preserving whitespace, key order, and duplicate keys. <code>JSONB</code>, by contrast, parses the document into a binary decomposition on insert. It discards whitespace, deduplicates keys, and rewrites key order. That extra write cost makes subsequent reads and queries significantly faster. JSONB also supports GIN indexes for full-document search and the containment operators (<code>@&gt;</code>, <code>&lt;@</code>) that make querying inside JSON practical at scale.</p>

        <p>So when would you pick plain <code>JSON</code>? Almost never. The only reason to reach for it is when preserving exact input representation matters, for example when auditing or replaying an exact API payload. For everything else, JSONB is the right call.</p>

        <pre><code>-- Create a table with JSONB for flexible metadata
CREATE TABLE products (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        TEXT NOT NULL,
    price       NUMERIC(10,2) NOT NULL,
    attributes  JSONB
);

-- Insert with JSON literal
INSERT INTO products (name, price, attributes)
VALUES ('Wireless Keyboard', 79.99, '{"color": "black", "in_stock": true, "tags": ["electronics", "peripherals"]}');

-- Query inside JSONB using ->> (returns text)
SELECT name, attributes->>'color'
FROM products
WHERE attributes @> '{"in_stock": true}';

-- Index a specific JSONB path for fast lookups
CREATE INDEX ON products ((attributes->>'category'));

-- Full-document GIN index for containment queries
CREATE INDEX ON products USING GIN (attributes);</code></pre>

        <figure>
            <img src="https://images.unsplash.com/photo-1762279389020-eeeb69c25813?fm=jpg&q=80&w=1400&h=700&auto=format&fit=crop"
                 alt="Abstract glowing data visualization lines on a dark background representing complex database structures and JSON data storage in PostgreSQL"
                 loading="lazy" width="1400" height="700">
            <figcaption>JSONB stores documents in a binary decomposition, enabling GIN indexes and containment operators not available with plain JSON.</figcaption>
        </figure>

        <p>MySQL 5.7+ also has a <code>JSON</code> type, but it lacks PostgreSQL's <code>JSONB</code> binary format and GIN index support. If you're coming from MySQL, think of <code>JSONB</code> as a significantly more capable version: queryable with containment operators, indexable without generated columns, and faster on read-heavy workloads. See our <a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL comparison</a> for a deeper look at how the two databases handle JSON.</p>

        <div class="citation-capsule">
            <p>PostgreSQL's JSONB type stores JSON documents in a parsed binary format, enabling GIN index support and containment operators like <code>@&gt;</code> and <code>&lt;@</code> (<a href="https://www.postgresql.org/docs/current/datatype-json.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). Unlike the plain JSON type, which stores input text verbatim, JSONB eliminates whitespace, deduplicates keys, and rewrites key order, making it faster for most read and query workloads at the cost of a slightly slower write.</p>
        </div>

        <figure class="video-embed">
            <iframe
                width="100%" height="400"
                src="https://www.youtube-nocookie.com/embed/kCK6VD1rzT0"
                title="Understanding Data Types in PostgreSQL — CHAR, VARCHAR, TEXT, and More"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy"
                aria-label="Understanding Data Types in PostgreSQL — practical guide to CHAR, VARCHAR, TEXT, and more (2024)">
            </iframe>
            <noscript><a href="https://www.youtube.com/watch?v=kCK6VD1rzT0" target="_blank" rel="noopener">Understanding Data Types in PostgreSQL — CHAR, VARCHAR, TEXT, and More (YouTube, 2024)</a></noscript>
            <figcaption>Understanding Data Types in PostgreSQL — CHAR, VARCHAR, TEXT, and More (2024). A practical walkthrough of PostgreSQL type selection for real schemas.</figcaption>
        </figure>

        <h2 id="arrays">Arrays — A PostgreSQL-Only Feature</h2>

        <p>PostgreSQL supports native array columns for any built-in data type. There's no MySQL equivalent. Arrays let you store multiple values of the same type in a single column without a junction table. The <a href="https://www.postgresql.org/docs/current/arrays.html" target="_blank" rel="noopener">PostgreSQL documentation</a> confirms that arrays are fully indexable with GIN indexes and support operators like <code>ANY</code>, <code>ALL</code>, containment (<code>@&gt;</code>), and overlap (<code>&amp;&amp;</code>) for efficient filtering.</p>

        <pre><code>-- Store tags as a TEXT array
CREATE TABLE articles (
    id        BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title     TEXT NOT NULL,
    tags      TEXT[],
    scores    INTEGER[]
);

-- Insert with ARRAY constructor
INSERT INTO articles (title, tags)
VALUES ('PostgreSQL Guide', ARRAY['postgresql', 'database', 'sql']);

-- Find articles tagged 'postgresql'
SELECT title FROM articles
WHERE 'postgresql' = ANY(tags);

-- Find articles with both 'database' and 'sql' tags (containment)
SELECT title FROM articles
WHERE tags @> ARRAY['database', 'sql'];

-- GIN index for array membership queries
CREATE INDEX ON articles USING GIN (tags);</code></pre>

        <!-- [PERSONAL EXPERIENCE] -->
        <p>Arrays are useful for tags, permission sets, and small bounded value lists. Use them deliberately, though. If you frequently join on array values, or the array can grow without bound, a normalized junction table is the better design. Arrays shine when the set is small, bounded, and queried with <code>ANY</code>, not as a surrogate for a proper relation. We use <code>TEXT[]</code> for tag columns in SQL Designer's internal schema and it works well. The moment we needed to join on those values across tables, we moved them to a relation. Use <a href="/">SQL Designer</a> to sketch the tradeoff between an array column and a junction table before committing.</p>

        <div class="citation-capsule">
            <p>PostgreSQL supports native array columns for any built-in type, stored as a single column value with full operator support including <code>ANY</code>, <code>ALL</code>, containment (<code>@&gt;</code>), and overlap (<code>&amp;&amp;</code>) (<a href="https://www.postgresql.org/docs/current/arrays.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). GIN indexes on array columns make tag-based and membership filtering efficient without a junction table, though normalized relations remain preferable for unbounded or heavily joined datasets.</p>
        </div>

        <h2 id="uuid-identity">UUID and Identity Columns</h2>

        <p>PostgreSQL has a native <code>UUID</code> type that stores a 128-bit value in 16 bytes. That's more efficient than MySQL's <code>CHAR(36)</code> (36 bytes of text) and equivalent to MySQL's <code>BINARY(16)</code> without needing manual conversion functions. Since PostgreSQL 13, <code>gen_random_uuid()</code> is a built-in function that generates version 4 UUIDs with no extension required. The <a href="https://www.postgresql.org/docs/current/datatype-uuid.html" target="_blank" rel="noopener">PostgreSQL documentation</a> notes that UUID values are accepted in any standard format: with hyphens, without, or with curly braces.</p>

        <pre><code>-- UUID primary key (PostgreSQL 13+, no extension needed)
CREATE TABLE users (
    id         UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- GENERATED AS IDENTITY — SQL standard, preferred over SERIAL
CREATE TABLE orders (
    id           BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id      UUID NOT NULL REFERENCES users(id),
    total        NUMERIC(10,2) NOT NULL,
    placed_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- SERIAL (legacy — still works, but creates implicit sequence)
CREATE TABLE events (
    id        SERIAL PRIMARY KEY,
    name      TEXT NOT NULL
);</code></pre>

        <p>For auto-incrementing integer primary keys, PostgreSQL offers two approaches. The legacy <code>SERIAL</code> pseudo-type creates an implicit sequence. It works, but the sequence is loosely coupled and can cause surprises during dumps and restores. The modern alternative is <code>GENERATED ALWAYS AS IDENTITY</code> (SQL:2003 standard, available since PostgreSQL 10). It provides identical behavior with explicit sequence ownership and is compatible with standards-based tooling. Prefer it in new schemas. For the MySQL equivalent, see our guide to <a href="/blog/mysql-data-types">MySQL data types</a>.</p>

        <figure>
            <svg viewBox="0 0 600 210" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Comparison of UUID storage size across PostgreSQL native UUID, MySQL BINARY(16), and MySQL CHAR(36)">
                <title>UUID Storage: PostgreSQL vs MySQL</title>
                <rect width="600" height="210" rx="8" fill="#181f2e"/>
                <text x="300" y="28" text-anchor="middle" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="13" font-weight="600">UUID Storage by Approach</text>
                <text x="185" y="62" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">PostgreSQL UUID (native)</text>
                <rect x="195" y="48" width="64" height="18" rx="3" fill="#60a5fa"/>
                <text x="265" y="62" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="10">16 bytes</text>
                <text x="185" y="92" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">MySQL BINARY(16)</text>
                <rect x="195" y="78" width="64" height="18" rx="3" fill="#60a5fa"/>
                <text x="265" y="92" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="10">16 bytes (manual UUID_TO_BIN)</text>
                <text x="185" y="122" text-anchor="end" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">MySQL CHAR(36)</text>
                <rect x="195" y="108" width="144" height="18" rx="3" fill="#f59e0b" opacity="0.85"/>
                <text x="345" y="122" fill="#e2e8f0" font-family="JetBrains Mono, monospace" font-size="10">36 bytes (text, 2.25× larger)</text>
                <rect x="195" y="152" width="12" height="8" rx="2" fill="#60a5fa"/>
                <text x="212" y="160" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Compact binary</text>
                <rect x="340" y="152" width="12" height="8" rx="2" fill="#f59e0b"/>
                <text x="357" y="160" fill="#94a3b8" font-family="JetBrains Mono, monospace" font-size="10">Text representation</text>
                <text x="300" y="197" text-anchor="middle" fill="#475569" font-family="JetBrains Mono, monospace" font-size="9">Source: PostgreSQL Documentation — postgresql.org/docs/current/datatype-uuid.html</text>
            </svg>
            <figcaption>PostgreSQL's native UUID type stores 16 bytes without conversion. MySQL CHAR(36) uses 36 bytes, 2.25 times the storage, with no automatic optimization.</figcaption>
        </figure>

        <div class="citation-capsule">
            <p>PostgreSQL's UUID type stores a 128-bit identifier as 16 bytes natively, with no manual conversion required (<a href="https://www.postgresql.org/docs/current/datatype-uuid.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>). Since PostgreSQL 13, <code>gen_random_uuid()</code> generates version 4 UUIDs as a built-in function without any extension. This compares favorably to MySQL's CHAR(36), which uses 36 bytes of text and requires <code>UUID_TO_BIN()</code> to achieve equivalent compact storage.</p>
        </div>

        <h2 id="quick-reference">Quick Reference: Common Column Patterns</h2>

        <p>Translating all of the above into practice, these are the column definitions you'll actually write in day-to-day PostgreSQL schemas. Each pattern reflects the type guidance covered in this guide. When you're unsure whether to use UUID or BIGINT for a primary key, or TIMESTAMPTZ vs plain TIMESTAMP, this list is a good starting point.</p>

        <ul>
            <li><strong>Auto-increment primary key (modern):</strong> <code>BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY</code></li>
            <li><strong>Auto-increment primary key (legacy):</strong> <code>BIGSERIAL PRIMARY KEY</code></li>
            <li><strong>UUID primary key (PG 13+):</strong> <code>UUID DEFAULT gen_random_uuid() PRIMARY KEY</code></li>
            <li><strong>Email address:</strong> <code>VARCHAR(255) NOT NULL UNIQUE</code></li>
            <li><strong>Password hash:</strong> <code>TEXT NOT NULL</code></li>
            <li><strong>URL slug:</strong> <code>TEXT NOT NULL</code></li>
            <li><strong>Price / monetary value:</strong> <code>NUMERIC(10, 2) NOT NULL</code></li>
            <li><strong>Boolean flag:</strong> <code>BOOLEAN NOT NULL DEFAULT false</code></li>
            <li><strong>Audit timestamp (UTC-aware):</strong> <code>TIMESTAMPTZ NOT NULL DEFAULT NOW()</code></li>
            <li><strong>Long-form text content:</strong> <code>TEXT</code></li>
            <li><strong>JSON metadata:</strong> <code>JSONB</code></li>
            <li><strong>Tags / string list:</strong> <code>TEXT[]</code></li>
            <li><strong>Integer foreign key:</strong> <code>BIGINT NOT NULL REFERENCES other_table(id)</code></li>
            <li><strong>UUID foreign key:</strong> <code>UUID NOT NULL REFERENCES other_table(id)</code></li>
            <li><strong>Status / enum:</strong> <code>TEXT NOT NULL CHECK (status IN ('active', 'inactive', 'pending'))</code></li>
        </ul>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3 class="faq-q">What is the difference between NUMERIC and DECIMAL in PostgreSQL?</h3>
                <p class="faq-a"><code>NUMERIC</code> and <code>DECIMAL</code> are identical in PostgreSQL. They're aliases for the same exact-precision type. Both accept <code>NUMERIC(precision, scale)</code> and <code>DECIMAL(precision, scale)</code> with the same behavior. Use either; <code>NUMERIC</code> is slightly more common in PostgreSQL convention.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Should I use TIMESTAMP or TIMESTAMPTZ in PostgreSQL?</h3>
                <p class="faq-a">Prefer <code>TIMESTAMPTZ</code> (timestamp with time zone) for almost all datetime columns. PostgreSQL stores <code>TIMESTAMPTZ</code> values in UTC and converts to the client session timezone on retrieval. Plain <code>TIMESTAMP</code> (without time zone) stores the literal value with no conversion. Only use it when your application explicitly manages timezone logic itself.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What is the difference between JSON and JSONB in PostgreSQL?</h3>
                <p class="faq-a"><code>JSON</code> stores the document as-is in text form, preserving whitespace, key order, and duplicate keys. <code>JSONB</code> parses and stores the document in a binary format, which is faster to query and supports GIN indexes for full-document search. Use <code>JSONB</code> unless you specifically need to preserve exact input representation.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How do I store a UUID primary key in PostgreSQL?</h3>
                <p class="faq-a">PostgreSQL has a native <code>UUID</code> type that stores the value as 16 bytes internally. Use <code>UUID DEFAULT gen_random_uuid() PRIMARY KEY</code> (PostgreSQL 13+) or <code>uuid_generate_v4()</code> with the <code>uuid-ossp</code> extension on older versions. The UUID type is natively indexable and requires no manual conversion, unlike MySQL's <code>CHAR(36)</code> or <code>BINARY(16)</code>.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What is the PostgreSQL equivalent of MySQL AUTO_INCREMENT?</h3>
                <p class="faq-a">PostgreSQL offers two options. The legacy <code>SERIAL</code> pseudo-type creates an implicit sequence. The SQL-standard alternative is <code>GENERATED ALWAYS AS IDENTITY</code> or <code>GENERATED BY DEFAULT AS IDENTITY</code>, available since PostgreSQL 10. Prefer <code>GENERATED ALWAYS AS IDENTITY</code> in new schemas. It's standards-compliant and gives explicit control over sequence behavior.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Does PostgreSQL have a native boolean type?</h3>
                <p class="faq-a">Yes. PostgreSQL's <code>BOOLEAN</code> type stores <code>true</code>, <code>false</code>, or <code>NULL</code> natively. It accepts multiple input formats: <code>TRUE</code>/<code>FALSE</code>, <code>'t'</code>/<code>'f'</code>, <code>'yes'</code>/<code>'no'</code>, <code>'on'</code>/<code>'off'</code>, and <code>1</code>/<code>0</code>. This is a key difference from MySQL, which has no native boolean and uses <code>TINYINT(1)</code> as a convention.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-data-types">MySQL Data Types — equivalent types and comparisons &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — full feature comparison &rarr;</a></li>
                <li><a href="/blog/database-ddl-comparison">DDL syntax across MySQL, PostgreSQL, Oracle, and SQLite &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database normalization — first through third normal form &rarr;</a></li>
                <li><a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">Free Online ER Diagram Maker — Draw Tables, Export SQL &rarr;</a></li>
                <li><a href="/blog/best-free-erd-tools">10 Best Free ERD Tools in 2026 — Tested and Compared &rarr;</a></li>
                <li><a href="/blog/crowfoot-notation">Crow's Foot Notation — ER Diagram Cardinality Explained &rarr;</a></li>
                <li><a href="/blog/database-designer">Free Online Database Designer &rarr;</a></li>
                <li><a href="/blog/database-schema-examples">Database Schema Examples — MySQL &amp; PostgreSQL Templates &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Best Practices &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design your PostgreSQL schema visually</h2>
    <p>SQL Designer lets you pick PostgreSQL data types from a dropdown as you build your tables. Visual FK lines, SQL export, and a Free plan with 1 diagram and 3 daily combined exports.</p>
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
