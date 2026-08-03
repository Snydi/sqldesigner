@extends('layouts.main')

@section('title', 'Site Map — SQL Designer')

@section('head')
    <meta name="description" content="Full site map for SQL Designer — links to all pages including the schema designer, blog articles, and account pages.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/sitemap">
    <style>
        .sitemap { max-width: 760px; margin: 0 auto; padding: 4rem 1.5rem; }
        .sitemap h1 { font-size: 1.3rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-primary); margin: 0 0 2.5rem; }
        .sitemap h2 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-primary); margin: 2rem 0 0.8rem; }
        .sitemap ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.5rem; }
        .sitemap li a { font-size: 1rem; color: var(--text-primary); text-decoration: none; text-transform: none; border-bottom: 1px solid transparent; }
        .sitemap li a:hover { border-bottom-color: var(--color-primary-text); color: var(--color-primary-text); }
        .sitemap li .desc { font-size: 1rem; color: var(--text-subtle); display: block; margin-top: 0.15rem; text-transform: none; }
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
                <a href="/demo">ER Diagram Maker</a>
                <span class="desc">Working browser-based ER diagram editor with relationship modeling and SQL export for six database dialects.</span>
            </li>
            <li>
                <a href="/about">About</a>
                <span class="desc">About SQL Designer and its creator, Dmitriy Snyatkov.</span>
            </li>
            <li>
                <a href="/features">Features</a>
                <span class="desc">Full list of everything SQL Designer can do: canvas editing, SQL export, foreign keys, constraints, and more.</span>
            </li>
            <li>
                <a href="/pricing">Pricing</a>
                <span class="desc">Compare the free and Pro plans and see what's included with each.</span>
            </li>
            <li>
                <a href="/library">Schema Library</a>
                <span class="desc">Real database schemas shared by the community — browse for inspiration or share your own.</span>
            </li>
            <li>
                <a href="/sitemap">Site Map</a>
                <span class="desc">Full index of all pages on SQL Designer.</span>
            </li>
            <li>
                <a href="/privacy">Privacy Policy</a>
                <span class="desc">How we collect, use, and protect your data.</span>
            </li>
            <li>
                <a href="/personal-data-consent">Personal Data Processing Consent</a>
                <span class="desc">Consent terms for processing account, subscription, payment, and support data.</span>
            </li>
            <li>
                <a href="/oferta">Public Offer</a>
                <span class="desc">Subscription price, recurring charge, cancellation, refund, and price-change terms.</span>
            </li>
            <li>
                <a href="/terms">Terms of Service</a>
                <span class="desc">Rules and conditions for using SQL Designer.</span>
            </li>
            <li>
                <a href="/refund-policy">Refund Policy</a>
                <span class="desc">Refund and cancellation terms for SQL Designer Pro subscriptions.</span>
            </li>
        </ul>

        <h2>Blog</h2>
        <ul>
            <li>
                <a href="/blog">Blog Index</a>
                <span class="desc">All articles on database design, SQL, and schema best practices.</span>
            </li>
            <li>
                <a href="/blog/erd-tools-guides">ERD Tools and Database Diagram Guides</a>
                <span class="desc">Comparisons, workflows, and practical guidance for choosing and using ERD tools.</span>
            </li>
            <li>
                <a href="/blog/relational-database-design-guides">Relational Database Design Guides</a>
                <span class="desc">Normalization, cardinality, joins, relationships, and practical schema patterns.</span>
            </li>
            <li>
                <a href="/blog/mysql-postgresql-guides">MySQL and PostgreSQL Schema Guides</a>
                <span class="desc">Data types, foreign keys, indexes, DDL differences, and engine selection.</span>
            </li>
            <li>
                <a href="/blog/database-ddl-comparison">DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</a>
                <span class="desc">Side-by-side comparison of CREATE TABLE syntax, primary keys, data types, constraints, and ALTER TABLE across five major databases.</span>
            </li>
            <li>
                <a href="/blog/crowfoot-notation">Crow's Foot Notation — ER Diagram Cardinality Explained</a>
                <span class="desc">Crow's foot symbols for one-to-one, one-to-many, and many-to-many relationships, and how they map to real foreign key constraints.</span>
            </li>
            <li>
                <a href="/blog/best-free-erd-tools">10 Best Free Online ERD Tools in 2026 — Tested and Compared</a>
                <span class="desc">We tested 10 free ERD tools — SQL Designer, DrawSQL, dbdiagram.io, draw.io, ChartDB, ERDPlus, QuickDBD, Lucidchart, DB Designer, and DBeaver — with honest strengths, real limits, and use-case guidance.</span>
            </li>
            <li>
                <a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step Guide</a>
                <span class="desc">Create a relational database schema online in 5 steps using a free browser-based SQL designer. Design tables, draw relationships, and export SQL — no install required.</span>
            </li>
            <li>
                <a href="/blog/er-diagram-maker-online">ER Diagram Tools: SQL-Aware vs. Generic Editors</a>
                <span class="desc">How SQL-aware schema tools differ from generic drawing editors, which capabilities matter, and when to use each approach.</span>
            </li>
            <li>
                <a href="/blog/database-designer">How to Choose Database Design Software for SQL Teams</a>
                <span class="desc">A practical framework for comparing dialect support, schema import, relationships, collaboration, and export features.</span>
            </li>
            <li>
                <a href="/blog/database-schema-examples">Database Schema Examples — MySQL &amp; PostgreSQL Templates</a>
                <span class="desc">Five real-world schema templates — e-commerce, blog, SaaS, task tracker, and messaging — with complete CREATE TABLE scripts.</span>
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
                <a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences for Schema Design</a>
                <span class="desc">Comparing data types, constraints, JSON support, and which database to choose for your project.</span>
            </li>
            <li>
                <a href="/blog/postgresql-data-types">PostgreSQL Data Types Explained — Which to Use and When</a>
                <span class="desc">Complete guide to PostgreSQL's 42+ built-in types: numeric, text, boolean, date/time, JSONB, arrays, UUID, and identity columns — with CREATE TABLE examples.</span>
            </li>
            <li>
                <a href="/blog/sql-joins">SQL JOIN Types Explained — INNER, LEFT, RIGHT, and FULL</a>
                <span class="desc">Complete guide to SQL joins: INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax, NULL handling, join algorithms, and common mistakes.</span>
            </li>
            <li>
                <a href="/blog/mysql-indexes">MySQL Indexes Explained — B-Tree, Composite, and EXPLAIN</a>
                <span class="desc">How B-tree indexes work, composite indexes and the leftmost prefix rule, reading EXPLAIN output, and common indexing mistakes.</span>
            </li>
            <li>
                <a href="/blog/postgresql-indexes">PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, and GiST</a>
                <span class="desc">PostgreSQL's six index types, composite/partial/covering indexes, when the planner picks a sequential scan, and reading EXPLAIN ANALYZE.</span>
            </li>
        </ul>
    </div>
@endsection
