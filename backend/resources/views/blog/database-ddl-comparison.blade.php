@extends('layouts.main')

@section('title', 'DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite')

@section('head')
    <meta name="description" content="How CREATE TABLE syntax, primary keys, data types, constraints, and ALTER TABLE differ across MySQL, PostgreSQL, Oracle, Microsoft SQL Server, and SQLite — with side-by-side DDL examples.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/database-ddl-comparison">
    <meta property="og:title" content="DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
    <meta property="og:description" content="Side-by-side DDL comparison across five major databases: CREATE TABLE, primary keys, data types, constraints, and ALTER TABLE syntax.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/database-ddl-comparison">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual database schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
    <meta name="twitter:description" content="Side-by-side DDL comparison across five major databases: CREATE TABLE, primary keys, data types, constraints, and ALTER TABLE syntax.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <script type="application/ld+json">
        @verbatim
    [
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sql-designer.com/" },
            { "@type": "ListItem", "position": 2, "name": "Blog", "item": "https://sql-designer.com/blog" },
            { "@type": "ListItem", "position": 3, "name": "DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite", "item": "https://sql-designer.com/blog/database-ddl-comparison" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "TechArticle",
        "headline": "DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite",
        "description": "How CREATE TABLE syntax, primary keys, data types, constraints, and ALTER TABLE differ across MySQL, PostgreSQL, Oracle, Microsoft SQL Server, and SQLite — with side-by-side DDL examples.",
        "image": "https://sql-designer.com/images/designer_screenshot.png",
        "url": "https://sql-designer.com/blog/database-ddl-comparison",
        "datePublished": "2026-05-05",
        "dateModified": "2026-05-05",
        "author": { "@type": "Organization", "name": "SQL Designer" },
        "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
        "about": [
            { "@type": "SoftwareApplication", "name": "MySQL" },
            { "@type": "SoftwareApplication", "name": "PostgreSQL" },
            { "@type": "SoftwareApplication", "name": "Oracle Database" },
            { "@type": "SoftwareApplication", "name": "Microsoft SQL Server" },
            { "@type": "SoftwareApplication", "name": "SQLite" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "How do you create an auto-increment primary key in each database?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "MySQL uses AUTO_INCREMENT, PostgreSQL uses SERIAL or GENERATED ALWAYS AS IDENTITY, Oracle uses GENERATED ALWAYS AS IDENTITY (12c+) or a separate sequence, SQL Server uses IDENTITY(1,1), and SQLite uses INTEGER PRIMARY KEY which auto-increments implicitly."
                }
            },
            {
                "@type": "Question",
                "name": "Which databases enforce CHECK constraints?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "PostgreSQL, Oracle, SQL Server, and SQLite all enforce CHECK constraints fully. MySQL enforces CHECK constraints from version 8.0.16 onwards — older MySQL versions parse them but silently ignore them."
                }
            },
            {
                "@type": "Question",
                "name": "What is the equivalent of VARCHAR across different databases?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "MySQL and PostgreSQL both use VARCHAR(n) for variable-length strings. Oracle uses VARCHAR2(n). SQL Server uses VARCHAR(n) for ASCII and NVARCHAR(n) for Unicode. SQLite uses TEXT regardless of the declared type."
                }
            }
        ]
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
        .post-eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 0.8rem;
        }
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

        .article-layout {
            display: grid;
            grid-template-columns: 200px minmax(0, 1fr);
            gap: clamp(2rem, 5vw, 4rem);
            max-width: 1060px;
            margin: 0 auto;
            padding: clamp(2rem, 4vw, 3rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
            align-items: start;
        }
        @media (max-width: 760px) {
            .article-layout { grid-template-columns: 1fr; gap: 1.5rem; padding-top: 1.5rem; }
        }
        .article-sidebar { position: sticky; top: 5rem; }
        @media (max-width: 760px) { .article-sidebar { position: static; } }
        .sidebar-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0 0 0.8rem;
        }
        .sidebar-nav { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.05rem; }
        .sidebar-nav a {
            display: block;
            font-size: 0.82rem;
            color: var(--text-muted);
            padding: 0.35rem 0.7rem;
            border-left: 2px solid var(--border-color);
            transition: color 120ms, border-color 120ms;
            text-decoration: none;
            line-height: 1.4;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }
        @media (max-width: 760px) {
            .sidebar-nav { flex-direction: row; flex-wrap: wrap; gap: 0.4rem; }
            .sidebar-nav a { border-left: none; border-bottom: 2px solid var(--border-color); padding: 0.3rem 0.5rem; font-size: 0.78rem; }
        }

        .article-body { min-width: 0; }
        .article-body h2 {
            font-size: clamp(1.1rem, 2vw, 1.35rem);
            letter-spacing: -0.01em;
            font-weight: 600;
            margin: 2.5rem 0 0.8rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            scroll-margin-top: 5rem;
        }
        .article-body h2:first-child { margin-top: 0; }
        .article-body h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 1.5rem 0 0.5rem;
            letter-spacing: -0.005em;
        }
        .article-body p { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin: 0 0 1rem; text-wrap: pretty; }
        .article-body ul, .article-body ol { margin: 0 0 1rem 1.5rem; padding: 0; }
        .article-body li { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin-bottom: 0.35rem; }
        .article-body code { background: var(--bg-elevated); padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.83em; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }
        .article-body pre { background: #181f2e; color: #e2e8f0; border-radius: 8px; padding: 1.2rem 1.5rem; overflow-x: auto; margin: 1rem 0 1.5rem; font-size: 0.875rem; line-height: 1.65; border: 1px solid var(--border-color); }
        .article-body pre code { background: none; padding: 0; color: inherit; font-size: inherit; font-family: 'JetBrains Mono', monospace; }
        .article-body table { width: 100%; border-collapse: collapse; margin: 0 0 1.5rem; font-size: 0.85rem; }
        .article-body th { background: var(--bg-elevated); color: var(--text-primary); padding: 0.6rem 1rem; text-align: left; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid var(--border-strong); font-family: 'JetBrains Mono', monospace; }
        .article-body td { padding: 0.55rem 1rem; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); vertical-align: top; }
        .article-body tr:nth-child(even) td { background: var(--bg-elevated); }
        .article-body .verdict { background: rgba(93,181,131,0.08); border-left: 3px solid var(--color-primary-text); padding: 0.9rem 1.2rem; border-radius: 0 6px 6px 0; margin: 1.2rem 0; }
        .article-body .verdict p { margin: 0; font-size: 0.88rem; }
        .article-body .warn { background: rgba(234,179,8,0.07); border-left: 3px solid #ca8a04; padding: 0.9rem 1.2rem; border-radius: 0 6px 6px 0; margin: 1.2rem 0; }
        .article-body .warn p { margin: 0; font-size: 0.88rem; }
        .article-body a { color: var(--color-primary-text); }
        .article-body strong { color: var(--text-primary); }

        .db-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin: 0 0 1.5rem; }
        .db-block { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem 1.2rem; }
        .db-block .db-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.5rem; }
        .db-block pre { margin: 0; padding: 0; background: none; border: none; font-size: 0.8rem; line-height: 1.6; color: #e2e8f0; }

        .related-nav { margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); }
        .related-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.8rem; }
        .related-nav ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.4rem; }
        .related-nav a { color: var(--color-primary-text); font-size: 0.88rem; text-decoration: none; }
        .related-nav a:hover { text-decoration: underline; }

        .docs-cta { margin: 0 auto; max-width: 1060px; padding: clamp(2rem, 4vw, 2.8rem) var(--gutter, 2rem); border-top: 1px solid var(--border-color); text-align: center; }
        .docs-cta h2 { font-size: clamp(1.2rem, 2.2vw, 1.6rem); letter-spacing: -0.02em; margin: 0 0 0.6rem; font-weight: 600; color: var(--text-primary); text-transform: none; }
        .docs-cta p { color: var(--text-secondary); margin: 0 auto 1.2rem; max-width: 52ch; font-size: 0.93rem; }
        .docs-cta .actions { display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; }
    </style>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>Schema Design</span></p>
        <p class="post-eyebrow">May 2026 · 12 min read</p>
        <h1 class="page-h1">DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</h1>
        <p class="page-sub">The five most-used relational databases all speak SQL, but their DDL dialects diverge in ways that matter when you're designing a schema. This guide compares <code>CREATE TABLE</code> syntax, primary keys, data types, constraints, and <code>ALTER TABLE</code> across MySQL, PostgreSQL, Oracle Database, Microsoft SQL Server, and SQLite — with concrete examples for each.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#at-a-glance">At a Glance</a></li>
            <li><a href="#primary-keys">Primary Keys</a></li>
            <li><a href="#string-types">String Types</a></li>
            <li><a href="#numeric-types">Numeric Types</a></li>
            <li><a href="#boolean-type">Boolean Type</a></li>
            <li><a href="#date-and-time">Date and Time</a></li>
            <li><a href="#check-constraints">CHECK Constraints</a></li>
            <li><a href="#default-values">DEFAULT Values</a></li>
            <li><a href="#generated-columns">Generated Columns</a></li>
            <li><a href="#alter-table">ALTER TABLE</a></li>
            <li><a href="#summary">Summary</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <h2 id="at-a-glance">At a Glance</h2>
        <p>The table below summarises the most important DDL differences. Each section below drills into one topic with side-by-side code examples.</p>
        <table>
            <tr>
                <th>Feature</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Auto-increment PK</td>
                <td><code>AUTO_INCREMENT</code></td>
                <td><code>SERIAL</code> / <code>IDENTITY</code></td>
                <td><code>GENERATED AS IDENTITY</code></td>
                <td><code>IDENTITY(1,1)</code></td>
                <td><code>INTEGER PRIMARY KEY</code></td>
            </tr>
            <tr>
                <td>Variable string</td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR2(n)</code></td>
                <td><code>NVARCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Large text</td>
                <td><code>LONGTEXT</code></td>
                <td><code>TEXT</code></td>
                <td><code>CLOB</code></td>
                <td><code>NVARCHAR(MAX)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Boolean</td>
                <td><code>TINYINT(1)</code></td>
                <td><code>BOOLEAN</code></td>
                <td><code>NUMBER(1)</code></td>
                <td><code>BIT</code></td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td>Auto timestamp</td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
                <td><code>DEFAULT NOW()</code></td>
                <td><code>DEFAULT SYSTIMESTAMP</code></td>
                <td><code>DEFAULT GETDATE()</code></td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
            </tr>
            <tr>
                <td>CHECK constraints</td>
                <td>Enforced (MySQL 8.0.16+)</td>
                <td>Always enforced</td>
                <td>Always enforced</td>
                <td>Always enforced</td>
                <td>Parsed; enforced since 3.25.0</td>
            </tr>
            <tr>
                <td>Generated columns</td>
                <td><code>GENERATED ALWAYS AS (...) STORED/VIRTUAL</code></td>
                <td><code>GENERATED ALWAYS AS (...) STORED</code></td>
                <td><code>GENERATED ALWAYS AS (...) VIRTUAL</code></td>
                <td><code>AS (...) PERSISTED/computed</code></td>
                <td><code>GENERATED ALWAYS AS (...) STORED/VIRTUAL</code></td>
            </tr>
            <tr>
                <td>Rename column (ALTER)</td>
                <td><code>RENAME COLUMN</code> (MySQL 8+)</td>
                <td><code>RENAME COLUMN</code></td>
                <td><code>RENAME COLUMN</code></td>
                <td><code>sp_rename</code> (stored procedure)</td>
                <td>Not supported directly</td>
            </tr>
            <tr>
                <td>IF NOT EXISTS</td>
                <td>Supported</td>
                <td>Supported</td>
                <td>Not supported (use exception handling)</td>
                <td><code>IF NOT EXISTS</code> (SQL Server 2022+) or <code>OBJECT_ID</code> check</td>
                <td>Supported</td>
            </tr>
        </table>

        <h2 id="primary-keys">Primary Keys and Auto-Increment</h2>
        <p>
            Auto-incrementing integer primary keys are the most visible DDL difference between databases. Every database has its own keyword or mechanism, and the underlying behaviour varies in subtle ways.
        </p>

        <div class="db-grid">
            <div class="db-block">
                <p class="db-label">MySQL</p>
                <pre><code>CREATE TABLE users (
  id INT UNSIGNED NOT NULL
     AUTO_INCREMENT,
  PRIMARY KEY (id)
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">PostgreSQL</p>
                <pre><code>-- modern standard (PG 10+)
CREATE TABLE users (
  id INT GENERATED ALWAYS
     AS IDENTITY PRIMARY KEY
);

-- legacy shorthand
-- id SERIAL PRIMARY KEY</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">Oracle</p>
                <pre><code>-- Oracle 12c+
CREATE TABLE users (
  id NUMBER GENERATED ALWAYS
     AS IDENTITY PRIMARY KEY
);

-- Oracle 11g and older:
-- use a SEQUENCE + trigger</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQL Server</p>
                <pre><code>CREATE TABLE users (
  id INT NOT NULL
     IDENTITY(1, 1),
  CONSTRAINT PK_users
     PRIMARY KEY (id)
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQLite</p>
                <pre><code>CREATE TABLE users (
  id INTEGER PRIMARY KEY
  -- AUTOINCREMENT keyword
  -- is optional and changes
  -- the reuse behaviour
);</code></pre>
            </div>
        </div>

        <p>Key behavioural notes:</p>
        <ul>
            <li><strong>MySQL</strong> — <code>AUTO_INCREMENT</code> reuses gaps in SQLite style unless you use <code>NO_AUTO_VALUE_ON_ZERO</code>. The counter resets on server restart if the table is empty (before MySQL 8.0).</li>
            <li><strong>PostgreSQL</strong> — <code>SERIAL</code> creates a backing sequence object. <code>GENERATED ALWAYS AS IDENTITY</code> is the SQL:2003 standard equivalent and is preferred for new schemas.</li>
            <li><strong>Oracle</strong> — Before 12c, sequences were always separate objects and had to be fed into the column via a trigger or called explicitly in the <code>INSERT</code>.</li>
            <li><strong>SQL Server</strong> — <code>IDENTITY(seed, increment)</code>. Once a row with an identity value is inserted, you cannot insert an explicit value without <code>SET IDENTITY_INSERT table ON</code>.</li>
            <li><strong>SQLite</strong> — <code>INTEGER PRIMARY KEY</code> (without the <code>AUTOINCREMENT</code> keyword) is an alias for the internal <code>rowid</code> and reuses deleted values. Adding <code>AUTOINCREMENT</code> prevents reuse but has a small performance cost.</li>
        </ul>

        <h2 id="string-types">String Types</h2>
        <p>String storage is one of the most divergent areas across databases, particularly around Unicode support and maximum lengths.</p>
        <table>
            <tr>
                <th>Use case</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Short variable string</td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR2(n)</code></td>
                <td><code>NVARCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Fixed-length string</td>
                <td><code>CHAR(n)</code></td>
                <td><code>CHAR(n)</code></td>
                <td><code>CHAR(n)</code></td>
                <td><code>NCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Large text / CLOB</td>
                <td><code>LONGTEXT</code></td>
                <td><code>TEXT</code> (unlimited)</td>
                <td><code>CLOB</code></td>
                <td><code>NVARCHAR(MAX)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Max <code>VARCHAR</code> length</td>
                <td>65,535 bytes</td>
                <td>1 GB</td>
                <td>32,767 bytes</td>
                <td>4,000 chars (<code>NVARCHAR</code>)</td>
                <td>Unlimited (stored as TEXT)</td>
            </tr>
        </table>

        <p>Important differences to be aware of:</p>
        <ul>
            <li><strong>Oracle uses <code>VARCHAR2</code>, not <code>VARCHAR</code></strong> — Oracle's <code>VARCHAR</code> is reserved and may behave differently in future versions. Always use <code>VARCHAR2</code>.</li>
            <li><strong>SQL Server stores Unicode by default with <code>N</code> prefix types</strong> — <code>NVARCHAR</code> and <code>NCHAR</code> store Unicode (UTF-16). Plain <code>VARCHAR</code> is ASCII only. For any modern application use <code>NVARCHAR</code>.</li>
            <li><strong>PostgreSQL's <code>TEXT</code> is unlimited</strong> — <code>TEXT</code> and <code>VARCHAR(n)</code> have the same performance; the length limit is just a constraint, not a storage optimisation.</li>
            <li><strong>SQLite ignores type affinity at storage level</strong> — SQLite stores any string as <code>TEXT</code> regardless of whether you declare the column as <code>VARCHAR(255)</code> or <code>CHAR(10)</code>. Type names are only advisory.</li>
        </ul>

        <div class="verdict"><p>If you're writing portable DDL, <code>VARCHAR(n)</code> works on MySQL, PostgreSQL, Oracle (as <code>VARCHAR2</code>), and SQL Server. Always use <code>NVARCHAR</code> on SQL Server for Unicode safety.</p></div>

        <h2 id="numeric-types">Numeric Types</h2>
        <p>Integer and decimal types are broadly consistent in naming, but there are gaps:</p>
        <table>
            <tr>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td><code>TINYINT</code> (1 byte)</td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>NUMBER(3)</code></td>
                <td><code>TINYINT</code> (1 byte)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>NUMBER(5)</code></td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>INT</code> (4 bytes)</td>
                <td><code>INTEGER</code> (4 bytes)</td>
                <td><code>NUMBER(10)</code></td>
                <td><code>INT</code> (4 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>NUMBER(19)</code></td>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>DECIMAL(p,s)</code></td>
                <td><code>NUMERIC(p,s)</code></td>
                <td><code>NUMBER(p,s)</code></td>
                <td><code>DECIMAL(p,s)</code></td>
                <td><code>NUMERIC(p,s)</code></td>
            </tr>
        </table>

        <p>Notable differences:</p>
        <ul>
            <li><strong>Oracle has no separate integer types</strong> — everything is <code>NUMBER(p,s)</code>. <code>INTEGER</code> is an alias for <code>NUMBER(38)</code> and lacks the compact storage of a true 4-byte integer.</li>
            <li><strong>SQLite uses dynamic typing</strong> — any integer you declare gets stored in 1–8 bytes depending on value, regardless of the declared type name.</li>
            <li><strong>MySQL supports <code>UNSIGNED</code></strong> — <code>INT UNSIGNED</code> doubles the positive range (0 to ~4.3B). No other major database supports this modifier.</li>
            <li>Use <code>DECIMAL</code> or <code>NUMERIC</code> for monetary values in all databases. Never use <code>FLOAT</code> or <code>DOUBLE</code> for money.</li>
        </ul>

        <h2 id="boolean-type">Boolean Type</h2>
        <p>Boolean is one of the most inconsistent types across SQL databases:</p>
        <table>
            <tr>
                <th>Database</th>
                <th>Boolean DDL</th>
                <th>True / False values</th>
            </tr>
            <tr>
                <td>MySQL</td>
                <td><code>TINYINT(1)</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
            <tr>
                <td>PostgreSQL</td>
                <td><code>BOOLEAN</code></td>
                <td><code>TRUE</code>/<code>FALSE</code>, <code>'t'</code>/<code>'f'</code>, <code>1</code>/<code>0</code></td>
            </tr>
            <tr>
                <td>Oracle</td>
                <td><code>NUMBER(1)</code> or <code>CHAR(1)</code></td>
                <td><code>1</code>/<code>0</code> or <code>'Y'</code>/<code>'N'</code> by convention</td>
            </tr>
            <tr>
                <td>SQL Server</td>
                <td><code>BIT</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
            <tr>
                <td>SQLite</td>
                <td><code>INTEGER</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
        </table>

        <p>
            PostgreSQL is the only database here with a true native boolean type. Oracle has no boolean type at all in SQL (PL/SQL has one, but it cannot be used as a column type). SQL Server's <code>BIT</code> accepts <code>1</code>/<code>0</code> and <code>'true'</code>/<code>'false'</code> strings but is not a proper boolean.
        </p>
        <div class="warn"><p>Oracle 23c introduced a native <code>BOOLEAN</code> column type — the first Oracle version to support it. If you're on an older Oracle version, use <code>NUMBER(1) CHECK (col IN (0, 1))</code>.</p></div>

        <h2 id="date-and-time">Date and Time Types</h2>
        <table>
            <tr>
                <th>Use case</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Date only</td>
                <td><code>DATE</code></td>
                <td><code>DATE</code></td>
                <td><code>DATE</code> (includes time!)</td>
                <td><code>DATE</code></td>
                <td><code>TEXT</code> / <code>NUMERIC</code></td>
            </tr>
            <tr>
                <td>Date + time</td>
                <td><code>DATETIME</code></td>
                <td><code>TIMESTAMP</code></td>
                <td><code>TIMESTAMP</code></td>
                <td><code>DATETIME2</code></td>
                <td><code>TEXT</code> (ISO 8601)</td>
            </tr>
            <tr>
                <td>Date + time + timezone</td>
                <td>Not natively supported</td>
                <td><code>TIMESTAMPTZ</code></td>
                <td><code>TIMESTAMP WITH TIME ZONE</code></td>
                <td><code>DATETIMEOFFSET</code></td>
                <td>Not supported</td>
            </tr>
            <tr>
                <td>Auto-set on insert</td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
                <td><code>DEFAULT NOW()</code></td>
                <td><code>DEFAULT SYSTIMESTAMP</code></td>
                <td><code>DEFAULT GETDATE()</code></td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
            </tr>
        </table>

        <p>Critical Oracle gotcha: Oracle's <code>DATE</code> type stores both date <em>and time</em> (to the nearest second). This is unlike every other database where <code>DATE</code> is date-only. If you query <code>WHERE event_date = DATE '2026-05-01'</code> in Oracle and the column has a time component, you'll get no results. Use <code>TRUNC(event_date)</code> to strip the time, or use <code>TIMESTAMP</code> columns from the start.</p>

        <p>SQL Server has deprecated the older <code>DATETIME</code> type in favour of <code>DATETIME2</code>, which has higher precision (100ns vs 3ms) and a wider range. Use <code>DATETIME2</code> in new SQL Server schemas.</p>

        <h2 id="check-constraints">CHECK Constraints</h2>
        <p><code>CHECK</code> constraints let you enforce rules at the database level — for example, that a status column can only hold certain values, or that a price must be positive.</p>
        <pre><code>-- Works the same way in MySQL 8.0.16+, PostgreSQL, Oracle, and SQL Server
CREATE TABLE orders (
    id          INT PRIMARY KEY,
    status      VARCHAR(20) NOT NULL,
    total_cents INT NOT NULL,
    CONSTRAINT chk_status  CHECK (status IN ('pending', 'paid', 'cancelled')),
    CONSTRAINT chk_total   CHECK (total_cents >= 0)
);</code></pre>

        <p>The SQL above is portable across all five databases with minor syntax adjustments. The key differences are enforcement history:</p>
        <ul>
            <li><strong>MySQL</strong> — <code>CHECK</code> constraints were parsed but silently ignored before MySQL 8.0.16. Any existing schema built on MySQL 5.7 or earlier that uses <code>CHECK</code> for data integrity may have dirty data in it.</li>
            <li><strong>PostgreSQL, Oracle, SQL Server</strong> — always enforced <code>CHECK</code> constraints fully.</li>
            <li><strong>SQLite</strong> — enforced <code>CHECK</code> constraints from version 3.25.0 (2018). Very old SQLite builds may ignore them.</li>
        </ul>

        <div class="verdict"><p>For maximum portability and correctness, always use <code>CHECK</code> constraints with a named <code>CONSTRAINT chk_name</code> so they can be dropped by name later.</p></div>

        <h2 id="default-values">DEFAULT Values</h2>
        <p>Specifying default values for columns is mostly consistent, but the function names for current timestamp differ:</p>
        <pre><code>-- MySQL
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- PostgreSQL
created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()  -- triggers or rules needed for auto-update

-- Oracle
created_at TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
updated_at TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL

-- SQL Server
created_at DATETIME2 NOT NULL DEFAULT GETDATE(),
updated_at DATETIME2 NOT NULL DEFAULT GETDATE()

-- SQLite
created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP</code></pre>

        <p>
            MySQL's <code>ON UPDATE CURRENT_TIMESTAMP</code> is a MySQL-only extension that automatically refreshes the column on any <code>UPDATE</code>. PostgreSQL, Oracle, and SQL Server require a trigger to replicate this behaviour. SQLite has no trigger-free equivalent.
        </p>

        <h2 id="generated-columns">Generated (Computed) Columns</h2>
        <p>Generated columns derive their value from an expression and are recalculated automatically. They are useful for storing pre-computed values like full names or totals without duplicating logic in application code.</p>

        <div class="db-grid">
            <div class="db-block">
                <p class="db-label">MySQL</p>
                <pre><code>ALTER TABLE products ADD COLUMN
  total_price DECIMAL(10,2)
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">PostgreSQL</p>
                <pre><code>ALTER TABLE products ADD COLUMN
  total_price DECIMAL(10,2)
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;
-- VIRTUAL not yet supported</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">Oracle</p>
                <pre><code>ALTER TABLE products ADD (
  total_price NUMBER
  GENERATED ALWAYS AS
    (quantity * unit_price)
  VIRTUAL
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQL Server</p>
                <pre><code>ALTER TABLE products ADD
  total_price AS
    (quantity * unit_price)
  PERSISTED;</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQLite</p>
                <pre><code>-- Must be in CREATE TABLE
total_price REAL
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;</code></pre>
            </div>
        </div>

        <p>
            <strong>STORED vs VIRTUAL</strong> — a stored (persisted) generated column writes the computed value to disk, making reads fast but writes slightly slower. A virtual column recomputes on every read. PostgreSQL only supports <code>STORED</code>. Oracle defaults to <code>VIRTUAL</code>. MySQL supports both.
        </p>

        <h2 id="alter-table">ALTER TABLE Differences</h2>
        <p>Modifying an existing table is where dialects diverge most sharply. Here are the most common operations:</p>

        <h3>Add a column</h3>
        <pre><code>-- MySQL, PostgreSQL, SQLite
ALTER TABLE users ADD COLUMN phone VARCHAR(20);

-- Oracle
ALTER TABLE users ADD (phone VARCHAR2(20));

-- SQL Server
ALTER TABLE users ADD phone NVARCHAR(20);</code></pre>

        <h3>Rename a column</h3>
        <pre><code>-- MySQL 8+, PostgreSQL
ALTER TABLE users RENAME COLUMN phone TO phone_number;

-- Oracle 12c R2+
ALTER TABLE users RENAME COLUMN phone TO phone_number;

-- SQL Server (uses a system stored procedure, not standard SQL)
EXEC sp_rename 'users.phone', 'phone_number', 'COLUMN';

-- SQLite — not supported directly.
-- You must recreate the table.</code></pre>

        <h3>Change a column's data type</h3>
        <pre><code>-- MySQL
ALTER TABLE users MODIFY COLUMN age SMALLINT;

-- PostgreSQL
ALTER TABLE users ALTER COLUMN age TYPE SMALLINT;

-- Oracle
ALTER TABLE users MODIFY (age NUMBER(5));

-- SQL Server
ALTER TABLE users ALTER COLUMN age SMALLINT;

-- SQLite — not supported. Recreate the table.</code></pre>

        <h3>Drop a column</h3>
        <pre><code>-- MySQL, PostgreSQL, Oracle, SQL Server
ALTER TABLE users DROP COLUMN phone;

-- SQLite — not supported before version 3.35.0 (2021).
-- Upgrade or recreate the table.</code></pre>

        <div class="warn"><p>SQLite has historically had very limited <code>ALTER TABLE</code> support. Adding columns and renaming tables always worked, but renaming columns requires SQLite 3.25.0+ and dropping columns requires SQLite 3.35.0+. Check your SQLite version before relying on these operations.</p></div>

        <h2 id="summary">Summary: Which Differences Matter Most in Practice</h2>
        <p>If you're designing a schema from scratch, here's where to focus your attention:</p>
        <ul>
            <li><strong>Primary keys</strong> — the biggest immediate syntax difference. Know your database's keyword before writing your first <code>CREATE TABLE</code>.</li>
            <li><strong>String types</strong> — use <code>VARCHAR2</code> on Oracle, <code>NVARCHAR</code> on SQL Server, and <code>VARCHAR</code> everywhere else. SQLite accepts anything and stores it as <code>TEXT</code>.</li>
            <li><strong>Booleans</strong> — only PostgreSQL has a true native <code>BOOLEAN</code>. Use <code>TINYINT(1)</code>, <code>BIT</code>, or <code>NUMBER(1)</code> with a <code>CHECK</code> constraint elsewhere.</li>
            <li><strong>Oracle's <code>DATE</code> includes time</strong> — this surprises almost everyone coming from MySQL or PostgreSQL. Use <code>TIMESTAMP</code> in Oracle if you only want a date.</li>
            <li><strong>CHECK enforcement history</strong> — if you're working with a MySQL 5.7 or earlier schema, <code>CHECK</code> constraints were ignored. Verify data integrity before migrating.</li>
            <li><strong>SQLite ALTER TABLE limitations</strong> — SQLite is not suitable for schemas that need frequent structural changes in production. Column drops and renames require a recent SQLite version.</li>
            <li><strong>Timestamp defaults</strong> — <code>CURRENT_TIMESTAMP</code> works in MySQL and SQLite; PostgreSQL prefers <code>NOW()</code>; Oracle uses <code>SYSTIMESTAMP</code>; SQL Server uses <code>GETDATE()</code> or <code>SYSDATETIME()</code>.</li>
        </ul>
        <p>
            Whichever database you're targeting, modelling your schema visually before writing DDL makes it easier to catch type mismatches and missing constraints early. SQL Designer supports both MySQL and PostgreSQL dialects and exports ready-to-run <code>CREATE TABLE</code> scripts — <a href="/demo">try the demo</a> with your own schema.
        </p>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences for Schema Design &rarr;</a></li>
                <li><a href="/blog/mysql-data-types">MySQL Data Types Explained &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization Explained &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design your database schema visually</h2>
    <p>SQL Designer lets you model tables, relationships, and constraints visually and export a CREATE TABLE script for MySQL or PostgreSQL. Free, browser-based, no installation required.</p>
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
