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
        .sitemap li a { font-size: 0.9rem; color: var(--text-primary); text-decoration: none; text-transform: none; border-bottom: 1px solid transparent; }
        .sitemap li a:hover { border-bottom-color: var(--color-primary-text); color: var(--color-primary-text); }
        .sitemap li .desc { font-size: 0.875rem; color: var(--text-subtle); display: block; margin-top: 0.15rem; text-transform: none; }
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
                <a href="/features">Features</a>
                <span class="desc">Full list of everything SQL Designer can do: canvas editing, SQL export, foreign keys, constraints, and more.</span>
            </li>
            <li>
                <a href="/library">Schema Library</a>
                <span class="desc">Real database schemas shared by the community — browse for inspiration or share your own.</span>
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
            <li>
                <a href="/docs">API Docs</a>
                <span class="desc">REST API reference for SQL Designer — endpoints, request parameters, and response schemas.</span>
            </li>
            <li>
                <a href="/privacy">Privacy Policy</a>
                <span class="desc">How we collect, use, and protect your data.</span>
            </li>
            <li>
                <a href="/terms">Terms of Service</a>
                <span class="desc">Rules and conditions for using SQL Designer.</span>
            </li>
        </ul>

        <h2>Blog</h2>
        <ul>
            <li>
                <a href="/blog">Blog Index</a>
                <span class="desc">All articles on database design, SQL, and schema best practices.</span>
            </li>
            <li>
                <a href="/blog/database-ddl-comparison">DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</a>
                <span class="desc">Side-by-side comparison of CREATE TABLE syntax, primary keys, data types, constraints, and ALTER TABLE across five major databases.</span>
            </li>
            <li>
                <a href="/blog/erd-maker">Free ERD Maker Online</a>
                <span class="desc">What an ERD maker is, how it differs from a generic diagram tool, and how to create ER diagrams and export SQL for free.</span>
            </li>
            <li>
                <a href="/blog/sql-to-erd">SQL to ERD — Generate an ER Diagram from SQL</a>
                <span class="desc">Import a CREATE TABLE script and generate a visual ER diagram automatically — tables, columns, and foreign key relationships rendered instantly.</span>
            </li>
            <li>
                <a href="/blog/crowfoot-notation">Crow's Foot Notation — ER Diagram Cardinality Explained</a>
                <span class="desc">Crow's foot symbols for one-to-one, one-to-many, and many-to-many relationships, and how they map to real foreign key constraints.</span>
            </li>
            <li>
                <a href="/blog/best-erd-tools">Best Free ERD Tools in 2026 — Honest Comparison</a>
                <span class="desc">Honest comparison of 7 ERD tools with real strengths, real weaknesses, and clear use-case guidance.</span>
            </li>
            <li>
                <a href="/blog/share-database-diagram">How to Share a Database Diagram Online</a>
                <span class="desc">Share your schema with a live link or embed it as an interactive iframe in your docs, with access control.</span>
            </li>
            <li>
                <a href="/blog/database-designer">Free Online Database Designer</a>
                <span class="desc">Visual schema builder for MySQL and PostgreSQL — design databases without writing DDL.</span>
            </li>
            <li>
                <a href="/blog/free-erd-tool">Free ERD Tool Online</a>
                <span class="desc">Visual entity relationship diagram editor — draw ER diagrams and export SQL for free.</span>
            </li>
            <li>
                <a href="/blog/postgres-db-designer">Free PostgreSQL DB Designer Online</a>
                <span class="desc">Visual ERD tool for Postgres — design PostgreSQL schemas and export DDL in the browser.</span>
            </li>
            <li>
                <a href="/blog/mysql-db-designer">Free MySQL DB Designer Online</a>
                <span class="desc">Design MySQL databases visually with drag-and-drop tables, foreign keys, and SQL export.</span>
            </li>
            <li>
                <a href="/blog/database-schema-examples">Database Schema Examples — MySQL &amp; PostgreSQL Templates</a>
                <span class="desc">Five real-world schema templates — e-commerce, blog, SaaS, task tracker, and messaging — with complete CREATE TABLE scripts.</span>
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
