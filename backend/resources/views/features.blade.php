@extends('layouts.main')

@section('title', 'SQL Designer Features — Free & Pro Database Schema Tool')

@section('head')
    <meta name="description" content="Visual SQL schema builder with Free and Pro plans: 1 free diagram, 3 daily exports, or unlimited diagrams and exports with Pro.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/features">
    <meta property="og:title" content="SQL Designer Features — Visual SQL Schema Builder">
    <meta property="og:description" content="Visual SQL schema builder with Free and Pro plans: 1 free diagram, 3 daily exports, or unlimited diagrams and exports with Pro.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="https://sql-designer.com/features">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — full feature list for the free database designer and ERD tool">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer Features — Visual SQL Schema Builder">
    <meta name="twitter:description" content="Visual SQL schema builder with Free and Pro plans: 1 free diagram, 3 daily exports, or unlimited diagrams and exports with Pro.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <script type="application/ld+json">
    @verbatim
    [
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home",     "item": "https://sql-designer.com/" },
            { "@type": "ListItem", "position": 2, "name": "Features", "item": "https://sql-designer.com/features" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "@id": "https://sql-designer.com/#app",
        "name": "SQL Designer",
        "url": "https://sql-designer.com",
        "applicationCategory": "DeveloperApplication",
        "operatingSystem": "Any",
        "browserRequirements": "Requires JavaScript. Works in Chrome, Firefox, Safari, Edge.",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
        "screenshot": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111, "caption": "SQL Designer — drag-and-drop ERD canvas with multi-dialect SQL export" },
        "featureList": [
            "Drag-and-drop ERD canvas",
            "MySQL SQL export",
            "PostgreSQL SQL export",
            "SQLite SQL export",
            "Oracle SQL export",
            "SQL Server SQL export",
            "MS Access SQL export",
            "SQL import — reverse-engineer existing schemas",
            "Foreign key relationships with crow's foot notation",
            "PRIMARY KEY, UNIQUE, NOT NULL constraints",
            "Share links with read-only, editable, or approval-based access",
            "Embeddable iframe diagrams",
            "Auto-save to account",
            "Free plan: 1 diagram and 3 daily combined exports",
            "Pro plan: unlimited diagrams and exports"
        ],
        "sameAs": [
            "https://github.com/Snydi/sqldesigner",
            "https://discord.gg/vFwgX7qKqA"
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Features — SQL Designer",
        "url": "https://sql-designer.com/features",
        "datePublished": "2026-04-05",
        "dateModified": "2026-04-27",
        "description": "SQL Designer features: a drag-and-drop canvas, SQL export, SQL import, sharing, plus Free and Pro plan limits.",
        "isPartOf": { "@type": "WebSite", "url": "https://sql-designer.com" },
        "about": {
            "@type": "SoftwareApplication",
            "name": "SQL Designer",
            "url": "https://sql-designer.com"
        },
        "mainEntity": {
            "@type": "ItemList",
            "name": "SQL Designer Features",
            "itemListElement": [
                { "@type": "ListItem", "position": 1,  "name": "Drag-and-Drop Canvas" },
                { "@type": "ListItem", "position": 2,  "name": "Tables & Columns" },
                { "@type": "ListItem", "position": 3,  "name": "Column Data Types" },
                { "@type": "ListItem", "position": 4,  "name": "Auto-Save" },
                { "@type": "ListItem", "position": 5,  "name": "Foreign Key Relationships" },
                { "@type": "ListItem", "position": 6,  "name": "PRIMARY KEY constraint" },
                { "@type": "ListItem", "position": 7,  "name": "UNIQUE & NOT NULL constraints" },
                { "@type": "ListItem", "position": 8,  "name": "MySQL SQL Export" },
                { "@type": "ListItem", "position": 9,  "name": "PostgreSQL SQL Export" },
                { "@type": "ListItem", "position": 10, "name": "SQLite SQL Export" },
                { "@type": "ListItem", "position": 11, "name": "Oracle SQL Export" },
                { "@type": "ListItem", "position": 12, "name": "SQL Server SQL Export" },
                { "@type": "ListItem", "position": 13, "name": "MS Access SQL Export" },
                { "@type": "ListItem", "position": 14, "name": "SQL Import" },
                { "@type": "ListItem", "position": 15, "name": "One-Click Copy" },
                { "@type": "ListItem", "position": 16, "name": "Share Links" },
                { "@type": "ListItem", "position": 17, "name": "Embeds" },
                { "@type": "ListItem", "position": 18, "name": "Multiple Diagrams" },
                { "@type": "ListItem", "position": 19, "name": "Browser-Based, Nothing to Install" }
            ]
        }
    }
    ]
    @endverbatim
    </script>
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "Is SQL Designer free to use?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. The Free plan includes 1 diagram and 3 daily combined SQL, JSON, migration, or PNG exports, with no credit card required. Pro provides unlimited diagrams and exports."
                }
            },
            {
                "@type": "Question",
                "name": "Does SQL Designer work without installation?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. SQL Designer runs entirely in the browser — nothing to download or install. It works on any modern browser on Windows, Mac, or Linux."
                }
            },
            {
                "@type": "Question",
                "name": "Which SQL dialects does SQL Designer support for export?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "SQL Designer can generate CREATE TABLE scripts for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access. Switch between dialects per diagram and copy the generated DDL to your clipboard with one click."
                }
            },
            {
                "@type": "Question",
                "name": "Can I share my database diagram with someone else?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. SQL Designer generates shareable links with three access modes: read-only (anyone with the link can view), editable (anyone can edit), or approval-based (you approve each visitor individually). Diagrams can also be embedded as interactive iframes in any webpage or documentation site."
                }
            },
            {
                "@type": "Question",
                "name": "Can I import an existing SQL schema into SQL Designer?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. SQL Designer can parse and import existing CREATE TABLE SQL scripts and render them as a visual diagram automatically."
                }
            },
            {
                "@type": "Question",
                "name": "How many diagrams can I create?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "The Free plan includes 1 diagram. Pro includes unlimited diagrams."
                }
            },
            {
                "@type": "Question",
                "name": "Does SQL Designer support foreign key relationships?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. You can draw foreign key relationships between columns by connecting them visually on the canvas. Relationships are rendered using crow's foot notation and are included in the SQL export."
                }
            },
            {
                "@type": "Question",
                "name": "What is crow's foot notation in SQL Designer?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Crow's foot notation is a visual convention for showing the cardinality of database relationships. In SQL Designer, when you draw a foreign key between two columns, the connection line uses crow's foot symbols to indicate whether the relationship is one-to-one or one-to-many, making cardinality immediately clear on the diagram."
                }
            },
            {
                "@type": "Question",
                "name": "Can I embed a SQL Designer diagram in my website?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. SQL Designer generates an iframe embed code for any shared diagram. Paste it into a blog post, documentation page, or internal wiki and the diagram renders as an interactive preview."
                }
            },
            {
                "@type": "Question",
                "name": "What browsers does SQL Designer support?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "SQL Designer runs in all modern browsers: Chrome, Firefox, Safari, and Edge on Windows, Mac, and Linux. No installation or browser extension is required."
                }
            }
        ]
    }
    @endverbatim
    </script>
    <style>
        body { overflow-y: auto; margin: 0; }

        /* ── Page intro ── */
        .page-intro {
            padding: clamp(2.5rem, 5vw, 4rem) var(--gutter, 2rem) clamp(1.5rem, 3vw, 2.5rem);
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
        .intro-inner {
            max-width: 1120px;
            margin: 0 auto;
            position: relative;
        }
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
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            line-height: 1.1;
            letter-spacing: -0.025em;
            font-weight: 600;
            margin: 0 0 0.8rem;
            text-wrap: balance;
            max-width: 22ch;
        }
        h1.page-h1 em { font-style: normal; color: var(--color-primary-text); }
        .page-sub {
            font-size: 1.02rem;
            color: var(--text-secondary);
            margin: 0;
            max-width: 56ch;
            text-wrap: pretty;
        }

        /* ── Docs layout ── */
        .docs-layout {
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr);
            gap: clamp(2rem, 5vw, 4rem);
            max-width: 1120px;
            margin: 0 auto;
            padding: clamp(2rem, 5vw, 3.5rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
            align-items: start;
        }
        @media (max-width: 820px) {
            .docs-layout { grid-template-columns: 1fr; gap: 1.5rem; padding-top: 1.5rem; }
        }

        /* ── Sidebar ── */
        .docs-sidebar {
            position: sticky;
            top: 5rem;
        }
        @media (max-width: 820px) { .docs-sidebar { position: static; } }

        .sidebar-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0 0 0.8rem;
        }
        .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }
        .sidebar-nav > li {
            display: flex;
            flex-direction: column;
        }
        .sidebar-nav a.sidebar-section {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            padding: 0.45rem 0.8rem;
            border-left: 2px solid var(--border-color);
            transition: color 120ms, border-color 120ms;
            display: block;
            text-decoration: none;
        }
        .sidebar-nav a.sidebar-section:hover,
        .sidebar-nav a.sidebar-section.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }
        .sidebar-sub {
            list-style: none;
            margin: 0 0 0.5rem;
            padding: 0;
        }
        .sidebar-sub a {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.76rem;
            color: var(--text-muted);
            padding: 0.3rem 0.8rem 0.3rem 1.6rem;
            border-left: 2px solid var(--border-color);
            transition: color 120ms, border-color 120ms;
            text-decoration: none;
        }
        .sidebar-sub a:hover,
        .sidebar-sub a.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }
        @media (max-width: 820px) {
            .sidebar-nav { flex-direction: row; flex-wrap: wrap; gap: 0.4rem; }
            .sidebar-nav > li { flex-direction: row; }
            .sidebar-nav a.sidebar-section { border-left: none; border-bottom: 2px solid var(--border-color); padding: 0.35rem 0.6rem; }
            .sidebar-sub { display: none; }
        }

        /* ── Content ── */
        .docs-content { min-width: 0; }
        .docs-section {
            margin-bottom: clamp(2.5rem, 5vw, 3.5rem);
            scroll-margin-top: 5rem;
        }
        .docs-section:last-child { margin-bottom: 0; }

        .section-eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin: 0 0 0.5rem;
        }
        h2.section-h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            font-weight: 600;
            margin: 0 0 1.4rem;
            padding-bottom: 0.7rem;
            border-bottom: 1px solid var(--border-color);
        }

        /* ── Feature list ── */
        .feat-list { display: flex; flex-direction: column; }
        .feat {
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr);
            gap: 1.1rem;
            padding: 1.3rem 0;
            border-bottom: 1px solid var(--border-light);
            scroll-margin-top: 5rem;
        }
        .feat:last-child { border-bottom: none; }
        .feat-glyph {
            width: 44px;
            height: 44px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-surface);
            display: grid;
            place-items: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--color-primary-text);
            flex-shrink: 0;
        }
        .feat-glyph.fk { color: var(--accent-fk, #c9a86a); border-color: rgba(201,168,106,0.35); }
        .feat-body h3 {
            font-size: 1rem;
            letter-spacing: -0.005em;
            font-weight: 600;
            margin: 0.15rem 0 0.35rem;
            color: var(--text-primary);
            text-transform: none;
        }
        .feat-body p {
            font-size: 0.93rem;
            color: var(--text-secondary);
            line-height: 1.65;
            margin: 0;
            text-wrap: pretty;
            max-width: 62ch;
        }
        .feat-body a { color: var(--color-primary-text); }
        .feat-body code {
            font-size: 0.82rem;
            background: var(--bg-elevated);
            border-radius: 3px;
            padding: 0.1em 0.35em;
        }

        /* ── CTA ── */
        .docs-cta {
            margin: clamp(2rem, 5vw, 3rem) auto 0;
            max-width: 1120px;
            padding: clamp(2rem, 4vw, 2.8rem) var(--gutter, 2rem);
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        .docs-cta h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            margin: 0 0 0.6rem;
            text-transform: none;
            color: var(--text-primary);
        }
        .docs-cta p { color: var(--text-secondary); margin: 0 auto 1.2rem; max-width: 50ch; }
        .docs-cta .actions { display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; }
    </style>
@endsection

@section('content')

{{-- Page intro --}}
<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><span>Features</span></p>
        <h1 class="page-h1">Everything a <em>visual SQL schema builder</em> should do.</h1>
        <p class="page-sub">The free visual SQL schema builder and online database modeler for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. Drag-and-drop ERD canvas, foreign keys with crow's foot notation, SQL import and export — all in the browser, nothing to install.</p>
    </div>
</section>

{{-- Docs layout --}}
<div class="docs-layout">

    {{-- Sidebar --}}
    <aside class="docs-sidebar" aria-label="Features navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li>
                <a class="sidebar-section" href="#canvas">Canvas &amp; editing</a>
                <ul class="sidebar-sub">
                    <li><a href="#drag-drop">Drag-and-drop</a></li>
                    <li><a href="#tables-columns">Tables &amp; columns</a></li>
                    <li><a href="#data-types">Data types</a></li>
                    <li><a href="#auto-save">Auto-save</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#relationships">Relationships</a>
                <ul class="sidebar-sub">
                    <li><a href="#foreign-keys">Foreign keys</a></li>
                    <li><a href="#primary-key">PRIMARY KEY</a></li>
                    <li><a href="#unique-not-null">UNIQUE / NOT NULL</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#sql">SQL in &amp; out</a>
                <ul class="sidebar-sub">
                    <li><a href="#mysql-export">MySQL export</a></li>
                    <li><a href="#postgres-export">PostgreSQL export</a></li>
                    <li><a href="#sqlite-export">SQLite export</a></li>
                    <li><a href="#oracle-export">Oracle export</a></li>
                    <li><a href="#sqlserver-export">SQL Server export</a></li>
                    <li><a href="#msaccess-export">MS Access export</a></li>
                    <li><a href="#sql-import">SQL import</a></li>
                    <li><a href="#one-click-copy">One-click copy</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#sharing">Sharing</a>
                <ul class="sidebar-sub">
                    <li><a href="#share-links">Share links</a></li>
                    <li><a href="#embeds">Embeds</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#workspace">Workspace</a>
                <ul class="sidebar-sub">
                    <li><a href="#multiple-diagrams">Multiple diagrams</a></li>
                    <li><a href="#browser-based">Browser-based</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#vs">vs. alternatives</a>
            </li>
        </ul>
    </aside>

    {{-- Main content --}}
    <div class="docs-content">

        <section class="docs-section" id="canvas" aria-labelledby="canvas-h2">
            <p class="section-eyebrow">01 / Canvas</p>
            <h2 class="section-h2" id="canvas-h2">Canvas &amp; editing</h2>
            <div class="feat-list">

                <div class="feat" id="drag-drop">
                    <div class="feat-glyph">⌘</div>
                    <div class="feat-body">
                        <h3>Drag-and-drop canvas</h3>
                        <p>An infinite, pan-and-zoom canvas. Drop tables anywhere, rearrange freely, and work with schemas of any size without losing the shape of your design. Pan by scrolling, zoom with pinch or mouse wheel, and auto-fit the whole diagram to your screen in one click. The layout is always yours to control — no auto-arrange forced on you when you add a new table.</p>
                    </div>
                </div>

                <div class="feat" id="tables-columns">
                    <div class="feat-glyph">⊞</div>
                    <div class="feat-body">
                        <h3>Tables &amp; columns</h3>
                        <p>Add tables, add columns, rename inline, reorder by drag. Each column gets a name, a type, and optional constraints.</p>
                    </div>
                </div>

                <div class="feat" id="data-types">
                    <div class="feat-glyph">T</div>
                    <div class="feat-body">
                        <h3>Data types</h3>
                        <p>Each dialect gets its own type list — MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access all show only the types they actually support. Common examples: <code>INT</code>, <code>BIGINT</code>, <code>VARCHAR</code>, <code>TEXT</code>, <code>BOOLEAN</code>, <code>DATE</code>, <code>TIMESTAMP</code>, <code>UUID</code>, <code>DECIMAL</code>, <code>JSON</code>.</p>
                    </div>
                </div>

                <div class="feat" id="auto-save">
                    <div class="feat-glyph">↻</div>
                    <div class="feat-body">
                        <h3>Auto-save</h3>
                        <p>Every edit is saved to your account. Close the tab, switch machines, come back tomorrow — it's still there.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="docs-section" id="relationships" aria-labelledby="rel-h2">
            <p class="section-eyebrow">02 / Relationships</p>
            <h2 class="section-h2" id="rel-h2">Relationships &amp; constraints</h2>
            <div class="feat-list">

                <div class="feat" id="foreign-keys">
                    <div class="feat-glyph fk">⤳</div>
                    <div class="feat-body">
                        <h3>Foreign keys</h3>
                        <p>Drag from one column to another to define a foreign key — no dialog boxes, no typing constraint names by hand. Cardinality is rendered in <a href="/blog/crowfoot-notation">crow's foot notation</a> so one-to-many and one-to-one relationships are immediately readable on the canvas. Every foreign key you draw is reflected in the SQL export as a proper <code>FOREIGN KEY ... REFERENCES</code> constraint, in the correct syntax for whichever dialect you've selected.</p>
                    </div>
                </div>

                <div class="feat" id="primary-key">
                    <div class="feat-glyph">PK</div>
                    <div class="feat-body">
                        <h3>PRIMARY KEY</h3>
                        <p>Mark a column as the primary key with one click. The generated DDL adds the <code>PRIMARY KEY</code> constraint for you.</p>
                    </div>
                </div>

                <div class="feat" id="unique-not-null">
                    <div class="feat-glyph">!=</div>
                    <div class="feat-body">
                        <h3>UNIQUE &amp; NOT NULL</h3>
                        <p>Toggle <code>UNIQUE</code> and <code>NOT NULL</code> per column. The SQL output reflects exactly what you set.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="docs-section" id="sql" aria-labelledby="sql-h2">
            <p class="section-eyebrow">03 / SQL</p>
            <h2 class="section-h2" id="sql-h2">SQL in &amp; out</h2>
            <div class="feat-list">

                <div class="feat" id="mysql-export">
                    <div class="feat-glyph"><img src="/images/db-mysql.svg" alt="MySQL" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>MySQL export</h3>
                        <p>A complete MySQL <code>CREATE TABLE</code> script — types, constraints, foreign keys. Paste into MySQL Workbench, DBeaver, or a terminal and run.</p>
                    </div>
                </div>

                <div class="feat" id="postgres-export">
                    <div class="feat-glyph"><img src="/images/db-postgresql.svg" alt="PostgreSQL" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>PostgreSQL export</h3>
                        <p>Switch dialects and the same diagram comes out as Postgres-compatible DDL — works with <code>psql</code>, pgAdmin, Supabase, anything that speaks Postgres.</p>
                    </div>
                </div>

                <div class="feat" id="sqlite-export">
                    <div class="feat-glyph"><img src="/images/db-sqlite.svg" alt="SQLite" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>SQLite export</h3>
                        <p>Generates SQLite-compatible DDL with foreign key constraints declared inline inside <code>CREATE TABLE</code> — the only syntax SQLite accepts.</p>
                    </div>
                </div>

                <div class="feat" id="oracle-export">
                    <div class="feat-glyph"><img src="/images/db-oracle.svg" alt="Oracle" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>Oracle export</h3>
                        <p>Outputs Oracle DDL using double-quoted identifiers and Oracle-native types like <code>NUMBER</code>, <code>VARCHAR2</code>, and <code>CLOB</code>. Compatible with SQL*Plus and Oracle SQL Developer.</p>
                    </div>
                </div>

                <div class="feat" id="sqlserver-export">
                    <div class="feat-glyph"><img src="/images/db-sqlserver.svg" alt="SQL Server" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>SQL Server export</h3>
                        <p>Generates T-SQL with bracket-quoted identifiers and SQL Server types like <code>NVARCHAR</code>, <code>DATETIME2</code>, and <code>UNIQUEIDENTIFIER</code>. Ready to run in SSMS or Azure Data Studio.</p>
                    </div>
                </div>

                <div class="feat" id="msaccess-export">
                    <div class="feat-glyph"><img src="/images/db-msaccess.svg" alt="MS Access" width="28" height="28"></div>
                    <div class="feat-body">
                        <h3>MS Access export</h3>
                        <p>Exports bracket-quoted DDL with Access-specific types like <code>AUTOINCREMENT</code>, <code>MEMO</code>, and <code>YESNO</code>. Compatible with the Access SQL view and DAO.</p>
                    </div>
                </div>

                <div class="feat" id="sql-import">
                    <div class="feat-glyph">↧</div>
                    <div class="feat-body">
                        <h3>SQL import</h3>
                        <p>Paste a <code>CREATE TABLE</code> script and SQL Designer parses it into a visual ER diagram automatically — tables, columns, types, constraints, and foreign keys all placed on the canvas. Useful for reverse-engineering a production schema before a refactor, visualizing a schema from documentation, or onboarding onto an unfamiliar database. Supports multi-table scripts with <code>FOREIGN KEY</code> references across tables.</p>
                    </div>
                </div>

                <div class="feat" id="one-click-copy">
                    <div class="feat-glyph">⎘</div>
                    <div class="feat-body">
                        <h3>One-click copy</h3>
                        <p>Copy the full generated SQL to your clipboard with one click. No download, no extra step.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="docs-section" id="sharing" aria-labelledby="share-h2">
            <p class="section-eyebrow">04 / Sharing</p>
            <h2 class="section-h2" id="share-h2">Sharing &amp; embedding</h2>
            <div class="feat-list">

                <div class="feat" id="share-links">
                    <div class="feat-glyph">⌬</div>
                    <div class="feat-body">
                        <h3>Share links</h3>
                        <p>Generate a link with three access modes: read-only, editable, or approval-based — you approve each visitor individually.</p>
                    </div>
                </div>

                <div class="feat" id="embeds">
                    <div class="feat-glyph">&lt;/&gt;</div>
                    <div class="feat-body">
                        <h3>Embeds</h3>
                        <p>Embed a diagram as an interactive iframe in any docs site, README, or internal wiki. Embedded diagrams with a backlink can be <a href="/library">featured in the schema library</a>.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="docs-section" id="workspace" aria-labelledby="ws-h2">
            <p class="section-eyebrow">05 / Workspace</p>
            <h2 class="section-h2" id="ws-h2">Workspace</h2>
            <div class="feat-list">

                <div class="feat" id="multiple-diagrams">
                    <div class="feat-glyph">▦</div>
                    <div class="feat-body">
                        <h3>Multiple diagrams</h3>
                        <p>One diagram per project, microservice, or database. All saved to your account, accessible from any device.</p>
                    </div>
                </div>

                <div class="feat" id="browser-based">
                    <div class="feat-glyph">⌘W</div>
                    <div class="feat-body">
                        <h3>Browser-based</h3>
                        <p>Runs in Chrome, Firefox, Safari, Edge. No download, no extension, no setup — just <a href="https://sql-designer.com">sql-designer.com</a>.</p>
                    </div>
                </div>

            </div>
        </section>

        <section class="docs-section" id="vs" aria-labelledby="vs-h2">
            <p class="section-eyebrow">06 / Compare</p>
            <h2 class="section-h2" id="vs-h2">How SQL Designer compares</h2>
            <p style="font-size:0.95rem; color:var(--text-secondary); margin:0 0 1.5rem; max-width:65ch; text-wrap:pretty;">
                Most database designers fall into one of two buckets: desktop software that requires installation and ties you to one engine, or SaaS tools that put SQL export behind a paywall. SQL Designer is browser-based and offers Free and Pro plans.
            </p>
            <div style="display:flex; flex-direction:column; gap:1px; background:var(--border-light); border:1px solid var(--border-light); border-radius:8px; overflow:hidden; margin-bottom:1.5rem;">
                <div style="background:var(--bg-surface); padding:1.3rem 1.4rem;">
                    <h3 style="font-size:0.95rem; font-weight:600; margin:0 0 0.5rem; letter-spacing:-0.005em;">vs. MySQL Workbench</h3>
                    <p style="font-size:0.9rem; color:var(--text-secondary); margin:0; max-width:62ch; line-height:1.65; text-wrap:pretty;">MySQL Workbench is powerful but desktop-only, MySQL-exclusive, and requires a ~200 MB install. It's the right choice for deep MySQL administration (query tuning, server monitoring, migrations). SQL Designer is the right choice when you want to sketch or document a schema fast, collaborate with someone who isn't on the same machine, or need output for a dialect other than MySQL — without installing anything.</p>
                </div>
                <div style="background:var(--bg-surface); padding:1.3rem 1.4rem;">
                    <h3 style="font-size:0.95rem; font-weight:600; margin:0 0 0.5rem; letter-spacing:-0.005em;">vs. dbdiagram.io</h3>
                    <p style="font-size:0.9rem; color:var(--text-secondary); margin:0; max-width:62ch; line-height:1.65; text-wrap:pretty;">dbdiagram.io uses a custom DSL — you write schema text and it renders a diagram. That's fast for people who prefer code-first workflows, but it means a learning curve and no drag-and-drop. Its free tier also caps the number of diagrams and restricts SQL export to paid plans. SQL Designer is visual from the start, dialect-aware, and has a Free plan plus unlimited Pro access.</p>
                </div>
                <div style="background:var(--bg-surface); padding:1.3rem 1.4rem;">
                    <h3 style="font-size:0.95rem; font-weight:600; margin:0 0 0.5rem; letter-spacing:-0.005em;">The SQL Designer position</h3>
                    <p style="font-size:0.9rem; color:var(--text-secondary); margin:0; max-width:62ch; line-height:1.65; text-wrap:pretty;">Browser-based so nothing to install. Visual so there's no DSL to learn. The Free plan includes 1 diagram and 3 daily combined exports; Pro removes those limits. Open source so you can inspect exactly what the tool does with your schema.</p>
                </div>
            </div>
            <p style="font-size:0.88rem; color:var(--text-muted);">Full comparison including DrawSQL, ERDPlus, ChartDB, and Lucidchart: <a href="/blog/best-free-erd-tools" style="color:var(--color-primary-text);">10 Best Free ERD Tools in 2026 →</a></p>
        </section>

    </div>
</div>

{{-- CTA --}}
<section class="docs-cta">
    <h2>Ready to draw a schema?</h2>
    <p>Open the demo and try it on a real schema — no account required.</p>
    <div class="actions">
        <a class="btn btn-solid btn-lg" href="/demo">Open the demo</a>
        <a class="btn btn-outline btn-lg" href="/register">Sign up to save</a>
    </div>
</section>

<script>
    (function () {
        const targets = document.querySelectorAll('.docs-section[id], .feat[id]');
        const links = document.querySelectorAll('.docs-sidebar a[href^="#"]');
        function update() {
            let current = '';
            const y = window.scrollY + 100;
            targets.forEach(el => { if (el.offsetTop <= y) current = el.id; });
            links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + current));
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    })();
</script>
@endsection
