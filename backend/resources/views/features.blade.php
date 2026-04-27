@extends('layouts.main')

@section('title', 'Features — Visual MySQL & PostgreSQL ERD Tool | SQL Designer')

@section('head')
    <meta name="description" content="Every feature in SQL Designer: drag-and-drop canvas, MySQL and PostgreSQL export, foreign keys, constraints, SQL import, auto-save, sharing.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/features">
    <meta property="og:title" content="Features — Visual MySQL &amp; PostgreSQL ERD Tool | SQL Designer">
    <meta property="og:description" content="Every feature in SQL Designer: drag-and-drop canvas, MySQL and PostgreSQL export, foreign keys, constraints, SQL import, auto-save, sharing.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="https://sql-designer.com/features">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2557">
    <meta property="og:image:height" content="1269">
    <meta property="og:image:alt" content="SQL Designer — full feature list for the free database designer and ERD tool">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Features — Visual MySQL &amp; PostgreSQL ERD Tool | SQL Designer">
    <meta name="twitter:description" content="Every feature in SQL Designer: drag-and-drop canvas, MySQL and PostgreSQL export, foreign keys, constraints, SQL import, auto-save, sharing.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <meta name="keywords" content="ERD tool, database designer, MySQL schema designer, PostgreSQL schema designer, entity relationship diagram, visual SQL tool, foreign key diagram, CREATE TABLE generator, SQL import, free ERD tool, crow's foot notation, database diagram online">
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
        "name": "SQL Designer",
        "url": "https://sql-designer.com",
        "applicationCategory": "DeveloperApplication",
        "operatingSystem": "Any",
        "browserRequirements": "Requires JavaScript. Works in Chrome, Firefox, Safari, Edge.",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
        "screenshot": "https://sql-designer.com/images/screenshot.png",
        "featureList": [
            "Drag-and-drop ERD canvas",
            "MySQL SQL export",
            "PostgreSQL SQL export",
            "SQL import — reverse-engineer existing schemas",
            "Foreign key relationships with crow's foot notation",
            "PRIMARY KEY, UNIQUE, NOT NULL constraints",
            "Share links with read-only, editable, or approval-based access",
            "Embeddable iframe diagrams",
            "Auto-save to account",
            "Unlimited diagrams"
        ],
        "sameAs": [
            "https://gitlab.com/Snydi/sql-designer",
            "https://discord.gg/vFwgX7qKqA"
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Features — SQL Designer",
        "url": "https://sql-designer.com/features",
        "dateModified": "2026-04-27",
        "description": "Every feature in SQL Designer: drag-and-drop canvas, MySQL and PostgreSQL export, foreign keys, constraints, SQL import, auto-save, sharing.",
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
                { "@type": "ListItem", "position": 10, "name": "SQL Import" },
                { "@type": "ListItem", "position": 11, "name": "One-Click Copy" },
                { "@type": "ListItem", "position": 12, "name": "Share Links" },
                { "@type": "ListItem", "position": 13, "name": "Embeds" },
                { "@type": "ListItem", "position": 14, "name": "Multiple Diagrams" },
                { "@type": "ListItem", "position": 15, "name": "Browser-Based, Nothing to Install" }
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
                    "text": "Yes. SQL Designer is completely free — no credit card required, no subscription, no document limits. You can create unlimited diagrams, export SQL for MySQL and PostgreSQL, and share diagrams with others at no cost."
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
                "name": "Can SQL Designer export SQL for both MySQL and PostgreSQL?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. SQL Designer can generate CREATE TABLE scripts for both MySQL and PostgreSQL. Switch between dialects and copy the generated DDL to your clipboard with one click."
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
                    "text": "Unlimited. There is no cap on the number of diagrams you can create with a free account."
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
            mask-image: radial-gradient(ellipse 60% 70% at 30% 0%, black 30%, transparent 75%);
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
        <h1 class="page-h1">Everything an <em>ERD tool</em> should do.</h1>
        <p class="page-sub">A visual database designer for MySQL and PostgreSQL. Drag-and-drop canvas, foreign keys with crow's foot notation, SQL import and export — all in the browser.</p>
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
                        <p>An infinite, pan-and-zoom canvas. Drop tables anywhere, rearrange freely, work with schemas of any size without losing the shape.</p>
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
                        <p>Common MySQL and PostgreSQL types: <code>INT</code>, <code>BIGINT</code>, <code>VARCHAR</code>, <code>TEXT</code>, <code>BOOLEAN</code>, <code>DATE</code>, <code>TIMESTAMP</code>, <code>UUID</code>, <code>DECIMAL</code>, <code>JSON</code>.</p>
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
                        <p>Drag from one column to another to define a foreign key. Cardinality is rendered in <a href="/blog/crowfoot-notation">crow's foot notation</a> and the constraint is included on export.</p>
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
                    <div class="feat-glyph">My</div>
                    <div class="feat-body">
                        <h3>MySQL export</h3>
                        <p>A complete MySQL <code>CREATE TABLE</code> script — types, constraints, foreign keys. Paste into MySQL Workbench, DBeaver, or a terminal and run.</p>
                    </div>
                </div>

                <div class="feat" id="postgres-export">
                    <div class="feat-glyph">Pg</div>
                    <div class="feat-body">
                        <h3>PostgreSQL export</h3>
                        <p>Switch dialects and the same diagram comes out as Postgres-compatible DDL — works with <code>psql</code>, pgAdmin, Supabase, anything that speaks Postgres.</p>
                    </div>
                </div>

                <div class="feat" id="sql-import">
                    <div class="feat-glyph">↧</div>
                    <div class="feat-body">
                        <h3>SQL import</h3>
                        <p>Paste a <code>CREATE TABLE</code> script and SQL Designer parses it into a <a href="/blog/sql-to-erd">visual ER diagram</a> automatically. Reverse-engineer an existing schema in seconds.</p>
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
                        <p>Generate a link with three access modes: read-only, editable, or approval-based — you approve each visitor individually. <a href="/blog/share-database-diagram">How sharing works →</a></p>
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
