@extends('layouts.main')

@section('title', 'Blog — SQL Designer')

@section('head')
    <meta name="description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices. Written by the SQL Designer team.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog">
    <meta property="og:title" content="Blog — SQL Designer">
    <meta property="og:description" content="Tutorials and guides on MySQL database design, ER diagrams, and schema best practices.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/blog">
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
            color: var(--color-primary);
            margin: 0 0 0.5rem;
        }
        .blog-index .subtitle {
            font-size: 0.9rem;
            color: #666;
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
            background: #fff;
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
            color: var(--color-primary);
            margin: 0 0 0.5rem;
        }
        .post-card p {
            font-size: 0.85rem;
            color: #555;
            text-transform: none;
            line-height: 1.7;
            margin: 0;
        }
        .post-meta {
            font-size: 0.875rem;
            color: #aaa;
            text-transform: none;
            margin-bottom: 0.4rem;
        }
    </style>
@endsection

@section('content')
    <div class="blog-index">
        <h1>Blog</h1>
        <p class="subtitle">Guides and tutorials on MySQL schema design and database modelling.</p>

        <ul class="post-list">
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
