@extends('layouts.main')

@section('title', 'Blog — SQL Designer')

@section('head')
    <meta name="description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices. Written by the SQL Designer team.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog">
    <meta property="og:title" content="Blog — SQL Designer">
    <meta property="og:description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/blog">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2557">
    <meta property="og:image:height" content="1269">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Blog — SQL Designer">
    <meta name="twitter:description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
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
            font-size: 0.72rem;
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
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 0.5rem;
        }
        .post-card h2 {
            font-size: 0.97rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.5rem;
            line-height: 1.35;
            letter-spacing: -0.005em;
            text-transform: none;
        }
        .post-card p { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.65; margin: 0; }
    </style>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><span>Blog</span></p>
        <h1 class="page-h1">Blog</h1>
        <p class="page-sub">Guides and tutorials on MySQL and PostgreSQL schema design, ER diagrams, and database modelling.</p>
    </div>
</section>

<div class="blog-grid">
    <a class="post-card" href="/blog/database-ddl-comparison">
        <p class="card-meta">May 2026 · 12 min read</p>
        <h2>DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</h2>
        <p>Side-by-side comparison of CREATE TABLE syntax, primary keys, data types, CHECK constraints, and ALTER TABLE across five major relational databases — with code examples for each.</p>
    </a>
    <a class="post-card" href="/blog/erd-maker">
        <p class="card-meta">April 2026 · 5 min read</p>
        <h2>Free ERD Maker Online — Create ER Diagrams in Your Browser</h2>
        <p>What an ERD maker is, how it differs from a generic diagram tool, and how to use SQL Designer to create ER diagrams and export working SQL for free.</p>
    </a>
    <a class="post-card" href="/blog/sql-to-erd">
        <p class="card-meta">April 2026 · 5 min read</p>
        <h2>SQL to ERD — Generate an ER Diagram from a SQL Script</h2>
        <p>Import an existing CREATE TABLE script and generate a visual ER diagram automatically — tables, columns, constraints, and foreign key relationships rendered instantly.</p>
    </a>
    <a class="post-card" href="/blog/crowfoot-notation">
        <p class="card-meta">April 2026 · 6 min read</p>
        <h2>Crow&rsquo;s Foot Notation — ER Diagram Cardinality Explained</h2>
        <p>Learn the crow's foot symbols for one-to-one, one-to-many, and many-to-many relationships, how optionality works, and how the notation maps to real foreign key constraints.</p>
    </a>
    <a class="post-card" href="/blog/best-erd-tools">
        <p class="card-meta">April 2026 · 9 min read</p>
        <h2>Best Free ERD Tools in 2026 — Honest Comparison</h2>
        <p>Honest comparison of 7 ERD tools: SQL Designer, DrawSQL, dbdiagram.io, draw.io, DB Designer, DBeaver, and ChartDB — with real strengths, real weaknesses, and clear use-case guidance.</p>
    </a>
    <a class="post-card" href="/blog/share-database-diagram">
        <p class="card-meta">April 2026 · 5 min read</p>
        <h2>How to Share a Database Diagram Online — Shareable Links &amp; Embeds</h2>
        <p>Share your database schema with a live link that always shows the current version, or embed it as an interactive iframe in your docs. Control access with read-only, editable, or approval-based permissions.</p>
    </a>
    <a class="post-card" href="/blog/database-designer">
        <p class="card-meta">April 2026 · 6 min read</p>
        <h2>Free Online Database Designer — Visual Schema Builder for MySQL &amp; PostgreSQL</h2>
        <p>What to look for in a free online database designer, how visual schema building compares to writing DDL by hand, and how to go from blank canvas to exported SQL.</p>
    </a>
    <a class="post-card" href="/blog/free-erd-tool">
        <p class="card-meta">April 2026 · 6 min read</p>
        <h2>Free ERD Tool Online — Visual Entity Relationship Diagram Editor</h2>
        <p>What makes a genuinely free ERD tool, the difference between SQL-aware and generic diagram editors, and how to use SQL Designer to draw ER diagrams and export SQL for free.</p>
    </a>
    <a class="post-card" href="/blog/postgres-db-designer">
        <p class="card-meta">April 2026 · 6 min read</p>
        <h2>Postgres Designer Online — Free Visual PostgreSQL DB Designer</h2>
        <p>Design PostgreSQL schemas visually in your browser — with PostgreSQL-specific types, foreign key relationships, and one-click DDL export. Free, no install required.</p>
    </a>
    <a class="post-card" href="/blog/mysql-db-designer">
        <p class="card-meta">April 2026 · 6 min read</p>
        <h2>MySQL Designer Online — Free Visual MySQL DB Designer</h2>
        <p>Design MySQL databases visually — drag-and-drop tables, define columns with MySQL types, draw foreign key relationships, and export a CREATE TABLE script in one click.</p>
    </a>
    <a class="post-card" href="/blog/database-schema-examples">
        <p class="card-meta">April 2026 · 9 min read</p>
        <h2>Database Schema Examples — MySQL &amp; PostgreSQL Templates</h2>
        <p>Five real-world database schema templates — e-commerce, blog, SaaS, task tracker, and messaging — with complete MySQL and PostgreSQL CREATE TABLE scripts you can copy or build visually.</p>
    </a>
    <a class="post-card" href="/blog/how-to-design-mysql-database-schema">
        <p class="card-meta">March 2026 · 7 min read</p>
        <h2>How to Design a MySQL Database Schema — A Step-by-Step Guide</h2>
        <p>A practical walkthrough covering entity identification, column types, primary keys, foreign key relationships, and normalization — with tips on visualising your schema before writing any SQL.</p>
    </a>
    <a class="post-card" href="/blog/er-diagram-tool-online">
        <p class="card-meta">March 2026 · 5 min read</p>
        <h2>Free ER Diagram Tool Online for MySQL — No Download Required</h2>
        <p>What entity-relationship diagrams are, why they matter, and how to create one for your MySQL database entirely in the browser — for free.</p>
    </a>
    <a class="post-card" href="/blog/mysql-workbench-alternative">
        <p class="card-meta">March 2026 · 5 min read</p>
        <h2>MySQL Workbench Alternative Online — Free &amp; No Install Required</h2>
        <p>MySQL Workbench is powerful but heavy. If you need to design a schema quickly without installing anything, here are your options — including a fully free browser-based tool.</p>
    </a>
    <a class="post-card" href="/blog/mysql-foreign-key">
        <p class="card-meta">March 2026 · 6 min read</p>
        <h2>MySQL Foreign Key — Syntax, Examples, and Best Practices</h2>
        <p>A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples for e-commerce schemas, and common mistakes to avoid.</p>
    </a>
    <a class="post-card" href="/blog/mysql-data-types">
        <p class="card-meta">March 2026 · 7 min read</p>
        <h2>MySQL Data Types Explained — Which to Use and When</h2>
        <p>A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case and what to avoid.</p>
    </a>
    <a class="post-card" href="/blog/database-normalization">
        <p class="card-meta">March 2026 · 8 min read</p>
        <h2>Database Normalization Explained — 1NF, 2NF, and 3NF with Examples</h2>
        <p>Learn database normalization with concrete before-and-after examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.</p>
    </a>
    <a class="post-card" href="/blog/how-to-draw-er-diagram">
        <p class="card-meta">March 2026 · 6 min read</p>
        <h2>How to Draw an ER Diagram Step by Step</h2>
        <p>A step-by-step guide to drawing entity-relationship diagrams from a blank page to a complete design — with cardinality notation, common mistakes, and a practical blog platform example.</p>
    </a>
    <a class="post-card" href="/blog/mysql-vs-postgresql">
        <p class="card-meta">March 2026 · 7 min read</p>
        <h2>MySQL vs PostgreSQL — Key Differences for Schema Design</h2>
        <p>Comparing MySQL and PostgreSQL for database schema design: data types, constraints, auto-increment, JSON support, and which to choose for your next project.</p>
    </a>
</div>

@endsection
