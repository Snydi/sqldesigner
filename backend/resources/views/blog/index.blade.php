@extends('layouts.main')

@section('title', 'SQL Designer Blog — Database Design Guides')

@section('head')
    <meta name="description" content="Database design guides written by Dmitriy Snyatkov, the developer behind SQL Designer — ERD, normalization, SQL dialects, and more.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog">
    <meta property="og:title" content="Blog — SQL Designer">
    <meta property="og:description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Blog — SQL Designer">
    <meta name="twitter:description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <script type="application/ld+json">
    @verbatim
    [
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sql-designer.com/" },
            { "@type": "ListItem", "position": 2, "name": "Blog", "item": "https://sql-designer.com/blog" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "Blog",
        "name": "SQL Designer Blog",
        "url": "https://sql-designer.com/blog",
        "description": "Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.",
        "isPartOf": { "@type": "WebSite", "name": "SQL Designer", "url": "https://sql-designer.com" },
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
            mask-image: linear-gradient(to bottom, black 0%, transparent 75%);
            opacity: 0.45;
            pointer-events: none;
        }
        .intro-inner { max-width: 900px; margin: 0 auto; position: relative; }
        .breadcrumb {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 1rem;
        }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--color-primary-text); }
        .breadcrumb .sep { margin: 0 0.4rem; color: var(--border-strong); }
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

        .blog-grid {
            max-width: 1060px;
            margin: 0 auto;
            padding: clamp(2rem, 4vw, 3rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.2rem;
        }
        .post-card {
            display: block;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.4rem 1.6rem;
            text-decoration: none;
            transition: border-color 150ms, background 150ms;
        }
        .post-card:hover { border-color: var(--border-strong); background: var(--bg-elevated); }
        .card-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 0.5rem;
        }
        .post-card h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.5rem;
            line-height: 1.35;
            letter-spacing: -0.005em;
            text-transform: none;
        }
        .post-card p { font-size: 1rem; color: var(--text-secondary); line-height: 1.65; margin: 0; }
    </style>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><span>Blog</span></p>
        <h1 class="page-h1">Database Design Blog</h1>
        <p class="page-sub">Guides and tutorials on MySQL and PostgreSQL schema design, ER diagrams, and database modelling. Written by Dmitriy Snyatkov, creator of SQL Designer — a free, open-source database design tool.</p>
    </div>
</section>

<div class="blog-grid">
    <a class="post-card" href="/blog/postgresql-indexes">
        <p class="card-meta">July 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 9 min read</p>
        <h2>PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, and GiST</h2>
        <p>PostgreSQL's six index types, composite/partial/covering indexes, when the planner picks a sequential scan over an index, and reading EXPLAIN ANALYZE output.</p>
    </a>
    <a class="post-card" href="/blog/mysql-indexes">
        <p class="card-meta">July 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 9 min read</p>
        <h2>MySQL Indexes Explained — B-Tree, Composite, and EXPLAIN</h2>
        <p>How B-tree indexes work, CREATE INDEX syntax, composite indexes and the leftmost prefix rule, reading EXPLAIN output, and the mistakes that leave indexes unused.</p>
    </a>
    <a class="post-card" href="/blog/sql-joins">
        <p class="card-meta">July 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 9 min read</p>
        <h2>SQL JOIN Types Explained — INNER, LEFT, RIGHT, and FULL</h2>
        <p>A complete guide to SQL joins: INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax, NULL handling, join algorithms, and the mistakes that cause duplicate or missing rows.</p>
    </a>
    <a class="post-card" href="/blog/database-ddl-comparison">
        <p class="card-meta">May 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 12 min read</p>
        <h2>DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</h2>
        <p>Side-by-side comparison of CREATE TABLE syntax, primary keys, data types, CHECK constraints, and ALTER TABLE across five major relational databases — with code examples for each.</p>
    </a>
    <a class="post-card" href="/blog/crowfoot-notation">
        <p class="card-meta">April 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 6 min read</p>
        <h2>Crow&rsquo;s Foot Notation — ER Diagram Cardinality Explained</h2>
        <p>Learn the crow's foot symbols for one-to-one, one-to-many, and many-to-many relationships, how optionality works, and how the notation maps to real foreign key constraints.</p>
    </a>
    <a class="post-card" href="/blog/er-diagram-maker-online">
        <p class="card-meta">Updated August 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 7 min read</p>
        <h2>ER Diagram Tools: SQL-Aware vs. Generic Editors</h2>
        <p>Learn how SQL-aware schema tools differ from generic drawing editors, which capabilities matter for database work, and when each approach fits.</p>
    </a>
    <a class="post-card" href="/blog/create-database-schema-online">
        <p class="card-meta">June 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 7 min read</p>
        <h2>How to Create a Database Schema Online — Step-by-Step Guide</h2>
        <p>Create a relational database schema online in 5 steps using a free browser-based tool. Design tables, define columns, draw foreign key relationships, and export a complete CREATE TABLE script — no install required.</p>
    </a>
    <a class="post-card" href="/blog/best-free-erd-tools">
        <p class="card-meta">May 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 10 min read</p>
        <h2>10 Best Free Online ERD Tools in 2026 — Tested and Compared</h2>
        <p>We tested 10 free ERD tools: SQL Designer, DrawSQL, dbdiagram.io, draw.io, ChartDB, ERDPlus, QuickDBD, Lucidchart, DB Designer, and DBeaver — with honest strengths, real limits, pricing, and use-case guidance.</p>
    </a>
    <a class="post-card" href="/blog/database-designer">
        <p class="card-meta">Updated August 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 8 min read</p>
        <h2>How to Choose Database Design Software for SQL Teams</h2>
        <p>Compare SQL dialect support, schema import, relationship modeling, collaboration, and export features before choosing a database design tool.</p>
    </a>
    <a class="post-card" href="/blog/database-schema-examples">
        <p class="card-meta">April 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 9 min read</p>
        <h2>Database Schema Examples — MySQL &amp; PostgreSQL Templates</h2>
        <p>Five real-world database schema templates — e-commerce, blog, SaaS, task tracker, and messaging — with complete MySQL and PostgreSQL CREATE TABLE scripts you can copy or build visually.</p>
    </a>
    <a class="post-card" href="/blog/mysql-foreign-key">
        <p class="card-meta">March 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 6 min read</p>
        <h2>MySQL Foreign Key — Syntax, Examples, and Best Practices</h2>
        <p>A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples for e-commerce schemas, and common mistakes to avoid.</p>
    </a>
    <a class="post-card" href="/blog/mysql-data-types">
        <p class="card-meta">March 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 7 min read</p>
        <h2>MySQL Data Types Explained — Which to Use and When</h2>
        <p>A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case and what to avoid.</p>
    </a>
    <a class="post-card" href="/blog/database-normalization">
        <p class="card-meta">March 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 8 min read</p>
        <h2>Database Normalization Explained — 1NF, 2NF, and 3NF with Examples</h2>
        <p>Learn database normalization with concrete before-and-after examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.</p>
    </a>
    <a class="post-card" href="/blog/mysql-vs-postgresql">
        <p class="card-meta">March 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 7 min read</p>
        <h2>MySQL vs PostgreSQL — Key Differences for Schema Design</h2>
        <p>Comparing MySQL and PostgreSQL for database schema design: data types, constraints, auto-increment, JSON support, and which to choose for your next project.</p>
    </a>
    <a class="post-card" href="/blog/postgresql-data-types">
        <p class="card-meta">May 2026 · by <span style="color:var(--color-primary-text);">Dmitriy Snyatkov</span> · 10 min read</p>
        <h2>PostgreSQL Data Types Explained — Which to Use and When</h2>
        <p>A practical guide to PostgreSQL's 42+ built-in types: numeric, text, boolean, TIMESTAMPTZ, JSONB, arrays, UUID, and identity columns — with CREATE TABLE examples and MySQL comparisons.</p>
    </a>
</div>

@endsection
