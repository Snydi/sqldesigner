@extends('layouts.main')

@section('title', 'MySQL and PostgreSQL Database Guides | SQL Designer')

@section('head')
    <meta name="description" content="Use practical MySQL and PostgreSQL guides for data types, foreign keys, indexes, DDL differences, query plans, and database selection.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-postgresql-guides">
    <meta property="og:title" content="MySQL and PostgreSQL Schema Guides">
    <meta property="og:description" content="Database-specific guidance for types, keys, indexes, DDL, and choosing MySQL or PostgreSQL.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-postgresql-guides">
    <link rel="stylesheet" href="/css/blog.css">
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "@id": "https://sql-designer.com/blog/mysql-postgresql-guides#collection",
        "name": "MySQL and PostgreSQL Schema Guides",
        "url": "https://sql-designer.com/blog/mysql-postgresql-guides",
        "dateModified": "2026-08-03",
        "description": "Guides to MySQL and PostgreSQL data types, foreign keys, indexes, and DDL.",
        "isPartOf": { "@id": "https://sql-designer.com/#website" },
        "publisher": { "@id": "https://sql-designer.com/#organization" }
    }
    @endverbatim
    </script>
@endsection

@section('content')
    @include('blog.partials.topic-hub', [
        'breadcrumb' => 'MySQL & PostgreSQL',
        'heading' => 'MySQL and PostgreSQL Schema Guides',
        'intro' => 'Choose data types, define foreign keys, design useful indexes, compare DDL, and understand the schema-level differences between MySQL and PostgreSQL.',
        'articles' => [
            ['url' => '/blog/mysql-data-types', 'title' => 'MySQL Data Types Explained', 'description' => 'Choose numeric, string, date, time, JSON, and identifier types.'],
            ['url' => '/blog/postgresql-data-types', 'title' => 'PostgreSQL Data Types Explained', 'description' => 'Use PostgreSQL numeric, temporal, UUID, JSONB, array, and identity types.'],
            ['url' => '/blog/mysql-foreign-key', 'title' => 'MySQL Foreign Keys', 'description' => 'Apply foreign-key syntax, index requirements, and referential actions.'],
            ['url' => '/blog/mysql-indexes', 'title' => 'MySQL Indexes', 'description' => 'Design B-tree and composite indexes around the leftmost-prefix rule.'],
            ['url' => '/blog/postgresql-indexes', 'title' => 'PostgreSQL Indexes', 'description' => 'Choose B-tree, GIN, GiST, BRIN, partial, and covering indexes.'],
            ['url' => '/blog/mysql-vs-postgresql', 'title' => 'MySQL vs PostgreSQL', 'description' => 'Compare types, constraints, JSON support, and schema-design tradeoffs.'],
            ['url' => '/blog/database-ddl-comparison', 'title' => 'SQL DDL Comparison', 'description' => 'Compare CREATE TABLE and ALTER TABLE syntax across five databases.'],
        ],
    ])
@endsection
