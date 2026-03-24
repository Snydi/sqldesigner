@extends('layouts.main')

@section('title', 'Site Map — SQL Designer')

@section('head')
    <meta name="description" content="Full site map for SQL Designer — links to all pages including the schema designer, blog articles, and account pages.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/sitemap">
    <style>
        .sitemap { max-width: 760px; margin: 0 auto; padding: 4rem 1.5rem; }
        .sitemap h1 { font-size: 1.3rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-primary); margin: 0 0 2.5rem; }
        .sitemap h2 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-primary); margin: 2rem 0 0.8rem; }
        .sitemap ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
        .sitemap li a { font-size: 0.9rem; color: #2c3e50; text-decoration: none; text-transform: none; border-bottom: 1px solid transparent; }
        .sitemap li a:hover { border-bottom-color: var(--color-primary); color: var(--color-primary); }
        .sitemap li .desc { font-size: 0.8rem; color: #888; display: block; margin-top: 0.15rem; text-transform: none; }
    </style>
@endsection

@section('content')
    <div class="sitemap">
        <h1>Site Map</h1>

        <h2>Main Pages</h2>
        <ul>
            <li>
                <a href="/">Home</a>
                <span class="desc">Free online MySQL and PostgreSQL schema designer — visual drag-and-drop interface with SQL export.</span>
            </li>
            <li>
                <a href="/demo">Try Demo</a>
                <span class="desc">Try the schema designer without creating an account.</span>
            </li>
            <li>
                <a href="/register">Register</a>
                <span class="desc">Create a free account to save and manage your diagrams.</span>
            </li>
            <li>
                <a href="/login">Log In</a>
                <span class="desc">Sign in to your SQL Designer account.</span>
            </li>
        </ul>

        <h2>Blog</h2>
        <ul>
            <li>
                <a href="/blog">Blog Index</a>
                <span class="desc">All articles on database design, SQL, and schema best practices.</span>
            </li>
            <li>
                <a href="/blog/how-to-design-mysql-database-schema">How to Design a MySQL Database Schema</a>
                <span class="desc">Step-by-step guide covering entities, data types, primary keys, foreign keys, and normalization.</span>
            </li>
            <li>
                <a href="/blog/er-diagram-tool-online">Free ER Diagram Tool Online for MySQL</a>
                <span class="desc">What ER diagrams are, why they matter, and how to create one in the browser for free.</span>
            </li>
            <li>
                <a href="/blog/mysql-workbench-alternative">MySQL Workbench Alternative Online</a>
                <span class="desc">The best free browser-based alternatives to MySQL Workbench — no download required.</span>
            </li>
            <li>
                <a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax, Examples, and Best Practices</a>
                <span class="desc">Complete guide to MySQL foreign keys: syntax, ON DELETE/UPDATE options, and common mistakes.</span>
            </li>
            <li>
                <a href="/blog/mysql-data-types">MySQL Data Types Explained</a>
                <span class="desc">Practical guide to numeric, string, date/time, and JSON types — and which to choose.</span>
            </li>
            <li>
                <a href="/blog/database-normalization">Database Normalization Explained — 1NF, 2NF, and 3NF</a>
                <span class="desc">Learn normalization with clear examples and understand when it's acceptable to denormalize.</span>
            </li>
            <li>
                <a href="/blog/how-to-draw-er-diagram">How to Draw an ER Diagram Step by Step</a>
                <span class="desc">Entities, attributes, relationships, cardinality notation, and practical tips.</span>
            </li>
            <li>
                <a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences for Schema Design</a>
                <span class="desc">Comparing data types, constraints, JSON support, and which database to choose for your project.</span>
            </li>
        </ul>
    </div>
@endsection
