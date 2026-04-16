@extends('layouts.main')

@section('title', 'SQL to ERD — Generate an ER Diagram from a SQL Script')

@section('head')
    <meta name="description"
          content="Import a SQL script and generate an ER diagram automatically — SQL Designer parses your CREATE TABLE statements and renders tables, columns, and relationships on a visual canvas.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/sql-to-erd">
    <meta property="og:title" content="SQL to ERD — Generate an ER Diagram from a SQL Script">
    <meta property="og:description"
          content="Import a SQL script and generate an ER diagram automatically. SQL Designer parses CREATE TABLE statements and renders your schema visually — free, no install.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/sql-to-erd">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2557">
    <meta property="og:image:height" content="1269">
    <meta property="og:image:alt" content="SQL Designer — SQL to ERD diagram import">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL to ERD — Generate an ER Diagram from a SQL Script">
    <meta name="twitter:description" content="Import a SQL script and generate an ER diagram automatically. SQL Designer parses CREATE TABLE statements and renders your schema visually.">
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
                { "@type": "ListItem", "position": 3, "name": "SQL to ERD", "item": "https://sql-designer.com/blog/sql-to-erd" }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "TechArticle",
            "headline": "SQL to ERD — Generate an ER Diagram from a SQL Script",
            "description": "Import a SQL script and generate an ER diagram automatically. SQL Designer parses CREATE TABLE statements and renders tables, columns, and foreign key relationships on a visual canvas.",
            "image": "https://sql-designer.com/images/screenshot.png",
            "url": "https://sql-designer.com/blog/sql-to-erd",
            "datePublished": "2026-04-16",
            "dateModified": "2026-04-16",
            "author": { "@type": "Organization", "name": "SQL Designer" },
            "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
        }
        ]
        @endverbatim
    </script>
    <style>
        body { overflow-y: auto; }
        .blog-post { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 5rem; }
        .blog-post .breadcrumb { font-size: 0.875rem; color: #767676; background-color: transparent; text-transform: none; margin-bottom: 1.5rem; }
        .blog-post .breadcrumb a { color: var(--color-primary); }
        .blog-post .post-meta { font-size: 0.875rem; color: #767676; background-color: transparent; text-transform: none; margin-bottom: 1rem; }
        .blog-post h1 { font-size: 1.6rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-primary); background-color: transparent; margin: 0 0 1rem; line-height: 1.3; }
        .blog-post .intro { font-size: 1rem; color: var(--text-secondary); background-color: transparent; text-transform: none; line-height: 1.8; margin-bottom: 2.5rem; border-left: 3px solid var(--color-primary); padding-left: 1.2rem; }
        .blog-post h2 { font-size: 1.05rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary); background-color: transparent; margin: 2.5rem 0 0.8rem; }
        .blog-post p { font-size: 0.9rem; color: var(--text-secondary); background-color: transparent; text-transform: none; line-height: 1.8; margin: 0 0 1rem; }
        .blog-post ul { margin: 0 0 1rem 1.5rem; padding: 0; }
        .blog-post li { font-size: 0.9rem; color: var(--text-secondary); background-color: transparent; text-transform: none; line-height: 1.8; margin-bottom: 0.3rem; }
        .blog-post code { background: var(--bg-elevated); padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.85em; color: var(--text-primary); }
        .blog-post .cta-box { background: var(--color-primary-hover); color: #fff; border-radius: 6px; padding: 2rem; text-align: center; margin-top: 3rem; }
        .blog-post .cta-box h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.8rem; }
        .blog-post .cta-box p { color: #fff; background-color: transparent; margin: 0 0 1.2rem; font-size: 0.85rem; }
        .blog-post .btn-cta { background: var(--bg-surface); color: var(--color-primary); padding: 0.6rem 1.8rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; }
        .blog-post .btn-cta:hover { opacity: 0.9; }
    </style>
@endsection

@section('content')
    <article class="blog-post">
        <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; ER Diagrams</p>
        <p class="post-meta"><time datetime="2026-04-16">April 2026</time> &mdash; 5 min read</p>
        <h1>SQL to ERD — Generate an ER Diagram from a SQL Script</h1>

        <p class="intro">
            If you have an existing database and want to see its structure visually, you don't need to rebuild
            the diagram by hand. SQL Designer can parse a <code>CREATE TABLE</code> SQL script and generate an
            ER diagram from it automatically — rendering tables, columns, data types, constraints, and foreign
            key relationships on a visual canvas in seconds.
        </p>

        <h2>What Is SQL to ERD Conversion?</h2>
        <p>
            SQL to ERD conversion (also called SQL to ER diagram or SQL schema import) means taking a DDL
            script — a set of <code>CREATE TABLE</code> statements — and rendering it as a visual
            entity-relationship diagram. The tool reads your SQL, identifies each table and its columns,
            detects <code>FOREIGN KEY</code> constraints, and draws the corresponding relationships on a canvas.
        </p>
        <p>
            The result is the same ERD you would have drawn from scratch, but generated automatically from
            SQL you already have. It's the reverse of the more common workflow (design visually, then export
            SQL) — which is why it's often called reverse engineering a database schema.
        </p>

        <h2>Why Generate an ERD from SQL?</h2>
        <p>
            There are several common reasons to convert an existing SQL schema to a diagram:
        </p>
        <ul>
            <li><strong>Documentation</strong> — the schema exists only as migration files or a raw DDL dump, and you need a diagram to share with the team or include in technical docs</li>
            <li><strong>Onboarding</strong> — a new developer joins and needs to understand the database structure quickly; a diagram is far faster to read than <code>SHOW CREATE TABLE</code> output</li>
            <li><strong>Audit and review</strong> — before adding a major feature, you want to see how the current schema is structured to plan new tables and relationships</li>
            <li><strong>Legacy databases</strong> — the database was built without any visual design tool and no diagram has ever existed</li>
            <li><strong>Refactoring</strong> — you want to visualise the current state before deciding what to change</li>
        </ul>

        <h2>How to Import SQL and Generate an ERD in SQL Designer</h2>
        <ul>
            <li><strong>1. Copy your SQL script</strong> — export a <code>CREATE TABLE</code> DDL from your database client (MySQL Workbench, DBeaver, pgAdmin, <code>mysqldump</code>, <code>pg_dump</code>, etc.).</li>
            <li><strong>2. Open SQL Designer</strong> — create a free account and start a new diagram.</li>
            <li><strong>3. Use the Import function</strong> — paste your SQL script into the import dialog. SQL Designer parses the statements and renders the schema on the canvas.</li>
            <li><strong>4. Review the diagram</strong> — tables, columns, types, constraints, and foreign key relationships are drawn automatically. Reposition tables as needed to improve readability.</li>
            <li><strong>5. Export or share</strong> — export updated SQL, share a read-only link, or embed the diagram in your documentation.</li>
        </ul>

        <h2>What SQL Is Supported?</h2>
        <p>
            SQL Designer supports standard MySQL and PostgreSQL <code>CREATE TABLE</code> syntax, including:
        </p>
        <ul>
            <li>Column definitions with data types and lengths</li>
            <li><code>PRIMARY KEY</code> declarations (inline and table-level)</li>
            <li><code>UNIQUE</code> and <code>NOT NULL</code> constraints</li>
            <li><code>FOREIGN KEY ... REFERENCES</code> clauses — rendered as relationship lines</li>
            <li><code>AUTO_INCREMENT</code> (MySQL) and <code>SERIAL</code> / <code>GENERATED ALWAYS AS IDENTITY</code> (PostgreSQL)</li>
        </ul>

        <h2>SQL to ERD vs. ERD to SQL</h2>
        <p>
            SQL Designer supports both directions of the workflow:
        </p>
        <ul>
            <li><strong>ERD to SQL</strong> — design your schema visually on the canvas, then export a <code>CREATE TABLE</code> script. The standard forward-engineering workflow.</li>
            <li><strong>SQL to ERD</strong> — paste an existing SQL script, get a visual diagram automatically. Reverse-engineering an existing schema.</li>
        </ul>
        <p>
            Both workflows use the same free account with no diagram limits. Once a schema is imported, you
            can continue editing it visually and re-export updated SQL at any point.
        </p>

        <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border-color);">
            <p style="font-size:0.875rem; text-transform:uppercase; letter-spacing:0.06em; color:#767676; margin:0 0 0.8rem;">Related Articles</p>
            <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
                <li><a href="/blog/erd-maker" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Free ERD Maker Online — Create ER Diagrams in Your Browser &rarr;</a></li>
                <li><a href="/blog/free-erd-tool" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Free ERD Tool Online — Visual Entity Relationship Diagram Editor &rarr;</a></li>
                <li><a href="/blog/database-schema-examples" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Database Schema Examples — MySQL &amp; PostgreSQL Templates &rarr;</a></li>
            </ul>
        </nav>

        <div class="cta-box">
            <h3>Generate an ERD from your SQL script</h3>
            <p>Import a CREATE TABLE script into SQL Designer and get a visual ER diagram instantly — free, no install, no subscription required.</p>
            <a class="btn-cta" href="/register">Create a Free Account</a>
        </div>
    </article>
@endsection
