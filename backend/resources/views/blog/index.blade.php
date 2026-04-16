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
        .blog-index {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
        }
        .blog-index h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-primary-text);
            background-color: transparent;
            margin: 0 0 0.5rem;
        }
        .blog-index .subtitle {
            font-size: 0.9rem;
            color: var(--text-subtle);
            background-color: transparent;
            text-transform: none;
            margin: 0 0 3rem;
        }
        .post-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .post-card {
            background: var(--bg-surface);
            border-radius: 6px;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-decoration: none;
            display: block;
            transition: box-shadow 0.2s;
        }
        .post-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
        .post-card h2 {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-primary-text);
            margin: 0 0 0.5rem;
        }
        .post-card p {
            font-size: 0.85rem;
            color: var(--text-subtle);
            text-transform: none;
            line-height: 1.7;
            margin: 0;
        }
        .post-meta {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: none;
            margin-bottom: 0.4rem;
        }
    </style>
@endsection

@section('content')
    <div class="blog-index">
        <h1>Blog</h1>
        <p class="subtitle">Guides and tutorials on MySQL and PostgreSQL schema design, ER diagrams, and database modelling.</p>

        <ul class="post-list">
            <li>
                <a class="post-card" href="/blog/best-erd-tools">
                    <p class="post-meta">April 2026 &mdash; 8 min read</p>
                    <h2>Best Free ERD Tools Online in 2026 — Compared</h2>
                    <p>Comparing the best free online entity-relationship diagram tools: SQL Designer, dbdiagram.io, draw.io, Lucidchart, ERDPlus, and QuickDBD — rated on SQL support, usability, and what's genuinely free.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/lucidchart-alternative">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>Lucidchart Alternative for Database Design — Free Online ERD Tool</h2>
                    <p>Lucidchart is a polished diagramming tool but it can't export SQL or validate constraints. Here are the best free alternatives purpose-built for MySQL and PostgreSQL database design.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/drawio-alternative">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>draw.io Alternative for Database Design — SQL-Aware ERD Tool</h2>
                    <p>draw.io is excellent for general diagrams but has no SQL awareness — no data types, no constraints, no DDL export. Here are the best free alternatives that actually understand your database.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/share-database-diagram">
                    <p class="post-meta">April 2026 &mdash; 5 min read</p>
                    <h2>How to Share a Database Diagram Online — Shareable Links &amp; Embeds</h2>
                    <p>Share your database schema with a live link that always shows the current version, or embed it as an interactive iframe in your docs. Control access with read-only, editable, or approval-based permissions.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/database-designer">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>Free Online Database Designer — Visual Schema Builder for MySQL &amp; PostgreSQL</h2>
                    <p>What to look for in a free online database designer, how visual schema building compares to writing DDL by hand, and how to go from blank canvas to exported SQL.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/free-erd-tool">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>Free ERD Tool Online — Visual Entity Relationship Diagram Editor</h2>
                    <p>What makes a genuinely free ERD tool, the difference between SQL-aware and generic diagram editors, and how to use SQL Designer to draw ER diagrams and export SQL for free.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/postgres-db-designer">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>Free PostgreSQL DB Designer Online — Visual ERD Tool for Postgres</h2>
                    <p>Design PostgreSQL schemas visually in your browser — with PostgreSQL-specific types, foreign key relationships, and one-click DDL export. Free, no install required.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/mysql-db-designer">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>Free MySQL DB Designer Online — Visual Schema Builder</h2>
                    <p>Design MySQL databases visually — drag-and-drop tables, define columns with MySQL types, draw foreign key relationships, and export a CREATE TABLE script in one click.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/database-schema-examples">
                    <p class="post-meta">April 2026 &mdash; 9 min read</p>
                    <h2>Database Schema Examples — MySQL &amp; PostgreSQL Templates</h2>
                    <p>Five real-world database schema templates — e-commerce, blog, SaaS, task tracker, and messaging — with complete MySQL and PostgreSQL CREATE TABLE scripts you can copy or build visually.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/dbdiagram-alternative">
                    <p class="post-meta">April 2026 &mdash; 6 min read</p>
                    <h2>dbdiagram.io Alternative — Free Visual Schema Designer</h2>
                    <p>dbdiagram.io requires a paid plan for SQL export and private diagrams. Here are the best free alternatives — including a fully visual drag-and-drop option that exports MySQL and PostgreSQL DDL at no cost.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/how-to-design-mysql-database-schema">
                    <p class="post-meta">March 2026 &mdash; 7 min read</p>
                    <h2>How to Design a MySQL Database Schema — A Step-by-Step Guide</h2>
                    <p>A practical walkthrough covering entity identification, column types, primary keys, foreign key relationships, and normalization — with tips on visualising your schema before writing any SQL.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/er-diagram-tool-online">
                    <p class="post-meta">March 2026 &mdash; 5 min read</p>
                    <h2>Free ER Diagram Tool Online for MySQL — No Download Required</h2>
                    <p>What entity-relationship diagrams are, why they matter, and how to create one for your MySQL database entirely in the browser — for free.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/mysql-workbench-alternative">
                    <p class="post-meta">March 2026 &mdash; 5 min read</p>
                    <h2>MySQL Workbench Alternative Online — Free &amp; No Install Required</h2>
                    <p>MySQL Workbench is powerful but heavy. If you need to design a schema quickly without installing anything, here are your options — including a fully free browser-based tool.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/mysql-foreign-key">
                    <p class="post-meta">March 2026 &mdash; 6 min read</p>
                    <h2>MySQL Foreign Key — Syntax, Examples, and Best Practices</h2>
                    <p>A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples for e-commerce schemas, and common mistakes to avoid.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/mysql-data-types">
                    <p class="post-meta">March 2026 &mdash; 7 min read</p>
                    <h2>MySQL Data Types Explained — Which to Use and When</h2>
                    <p>A practical guide to MySQL data types: numeric, string, date/time, and JSON types — with advice on which to choose for each use case and what to avoid.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/database-normalization">
                    <p class="post-meta">March 2026 &mdash; 8 min read</p>
                    <h2>Database Normalization Explained — 1NF, 2NF, and 3NF with Examples</h2>
                    <p>Learn database normalization with concrete before-and-after examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/how-to-draw-er-diagram">
                    <p class="post-meta">March 2026 &mdash; 6 min read</p>
                    <h2>How to Draw an ER Diagram Step by Step</h2>
                    <p>A step-by-step guide to drawing entity-relationship diagrams from a blank page to a complete design — with cardinality notation, common mistakes, and a practical blog platform example.</p>
                </a>
            </li>
            <li>
                <a class="post-card" href="/blog/mysql-vs-postgresql">
                    <p class="post-meta">March 2026 &mdash; 7 min read</p>
                    <h2>MySQL vs PostgreSQL — Key Differences for Schema Design</h2>
                    <p>Comparing MySQL and PostgreSQL for database schema design: data types, constraints, auto-increment, JSON support, and which to choose for your next project.</p>
                </a>
            </li>
        </ul>
    </div>
@endsection
