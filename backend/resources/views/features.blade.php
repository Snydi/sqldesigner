@extends('layouts.main')

@section('title', 'Features — Free Database Designer & ERD Tool | SQL Designer')

@section('head')
    <meta name="description" content="Everything SQL Designer can do: visual drag-and-drop schema editing, MySQL and PostgreSQL SQL export, foreign keys, constraints, auto-save, and more.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/features">
    <meta property="og:title" content="Features — Free Database Designer &amp; ERD Tool | SQL Designer">
    <meta property="og:description" content="Everything SQL Designer can do: visual drag-and-drop schema editing, MySQL and PostgreSQL SQL export, foreign keys, constraints, auto-save, and more.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/features">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2557">
    <meta property="og:image:height" content="1269">
    <meta property="og:image:alt" content="SQL Designer — full feature list for the free database designer and ERD tool">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Features — Free Database Designer &amp; ERD Tool | SQL Designer">
    <meta name="twitter:description" content="Everything SQL Designer can do: visual drag-and-drop schema editing, MySQL and PostgreSQL SQL export, foreign keys, constraints, auto-save, and more.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Features — SQL Designer",
        "url": "https://sql-designer.com/features",
        "description": "Everything SQL Designer can do: visual drag-and-drop schema editing, MySQL and PostgreSQL SQL export, foreign keys, constraints, auto-save, and more.",
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
                { "@type": "ListItem", "position": 1, "name": "Drag-and-Drop Canvas" },
                { "@type": "ListItem", "position": 2, "name": "Tables & Columns" },
                { "@type": "ListItem", "position": 3, "name": "Column Data Types" },
                { "@type": "ListItem", "position": 4, "name": "Auto-Save" },
                { "@type": "ListItem", "position": 5, "name": "Foreign Key Relationships" },
                { "@type": "ListItem", "position": 6, "name": "PRIMARY KEY constraint" },
                { "@type": "ListItem", "position": 7, "name": "UNIQUE & NOT NULL constraints" },
                { "@type": "ListItem", "position": 8, "name": "MySQL SQL Export" },
                { "@type": "ListItem", "position": 9, "name": "PostgreSQL SQL Export" },
                { "@type": "ListItem", "position": 10, "name": "One-Click Copy" },
                { "@type": "ListItem", "position": 11, "name": "Free Account" },
                { "@type": "ListItem", "position": 12, "name": "Multiple Diagrams" },
                { "@type": "ListItem", "position": 13, "name": "Browser-Based, Nothing to Install" }
            ]
        }
    }
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
            }
        ]
    }
    @endverbatim
    </script>
    <style>
        body { overflow-y: auto; margin: 0; }

        /* ── Layout ── */
        .docs-layout {
            display: flex;
            align-items: flex-start;
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
            gap: 3rem;
        }

        /* ── Sidebar ── */
        .docs-sidebar {
            flex-shrink: 0;
            width: 200px;
            position: sticky;
            top: 2rem;
        }

        .sidebar-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
            margin: 0 0 0.6rem;
            padding: 0 0 0.5rem;
        }

        .sidebar-nav {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .sidebar-nav li {
            display: flex;
            flex-direction: column;
        }

        .sidebar-nav a.sidebar-section {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-subtle);
            text-decoration: none;
            padding: 0.45rem 0.75rem;
            border-left: 2px solid var(--border-color);
            transition: color 0.15s, border-color 0.15s;
            display: block;
        }

        .sidebar-nav a.sidebar-section:hover,
        .sidebar-nav a.sidebar-section.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }

        .sidebar-sub {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-sub a {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.3rem 0.75rem 0.3rem 1.4rem;
            border-left: 2px solid var(--border-color);
            display: block;
            transition: color 0.15s, border-color 0.15s;
        }

        .sidebar-sub a:hover,
        .sidebar-sub a.active {
            color: var(--color-primary-text);
            border-left-color: var(--color-primary-text);
        }

        /* ── Content ── */
        .docs-content {
            flex: 1;
            min-width: 0;
        }

        .docs-content h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-primary-text);
            margin: 0 0 0.5rem;
        }

        .docs-intro {
            font-size: 0.95rem;
            color: var(--text-subtle);
            line-height: 1.8;
            margin: 0 0 3.5rem;
            text-transform: none;
        }

        .docs-section {
            margin-bottom: 4rem;
            scroll-margin-top: 2rem;
        }

        .docs-section h2 {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--text-muted);
            margin: 0 0 1.4rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border-light);
        }

        .feature-entry {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--border-light);
            scroll-margin-top: 2rem;
        }

        .feature-entry:last-child {
            border-bottom: none;
        }

        .feat-icon {
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            background: var(--color-primary);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feat-icon svg {
            width: 1rem;
            height: 1rem;
            fill: #fff;
        }

        .feat-body h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-primary);
            margin: 0.1rem 0 0.35rem;
        }

        .feat-body p {
            font-size: 0.875rem;
            color: var(--text-subtle);
            line-height: 1.75;
            margin: 0;
            text-transform: none;
        }

        .feat-body code {
            font-size: 0.82rem;
            background: var(--bg-elevated);
            border-radius: 3px;
            padding: 0.1em 0.35em;
        }

        /* ── CTA ── */
        .docs-cta {
            margin-top: 1rem;
            background: var(--color-primary);
            color: #fff;
            border-radius: 8px;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .docs-cta h2 {
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 0 0 1.2rem;
        }

        .btn-cta {
            display: inline-block;
            background: #fff;
            color: var(--color-primary);
            padding: 0.7rem 2rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-cta:hover { opacity: 0.85; }

        /* ── Mobile ── */
        @media (max-width: 720px) {
            .docs-layout { flex-direction: column; gap: 2rem; padding-top: 2rem; }
            .docs-sidebar { width: 100%; position: static; }
            .sidebar-nav { flex-direction: row; flex-wrap: wrap; gap: 0.4rem; }
            .sidebar-nav li { flex-direction: row; flex-wrap: wrap; }
            .sidebar-nav a.sidebar-section { border-left: none; border-bottom: 2px solid var(--border-color); padding: 0.3rem 0.5rem; }
            .sidebar-nav a.sidebar-section:hover,
            .sidebar-nav a.sidebar-section.active { border-bottom-color: var(--color-primary-text); }
            .sidebar-sub { display: none; }
        }
    </style>
@endsection

@section('content')
<div class="docs-layout">

    {{-- Sidebar --}}
    <aside class="docs-sidebar" aria-label="Features navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li>
                <a class="sidebar-section" href="#canvas">Canvas &amp; Editing</a>
                <ul class="sidebar-sub">
                    <li><a href="#drag-drop">Drag-and-Drop</a></li>
                    <li><a href="#tables-columns">Tables &amp; Columns</a></li>
                    <li><a href="#data-types">Data Types</a></li>
                    <li><a href="#auto-save">Auto-Save</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#relationships">Relationships &amp; Constraints</a>
                <ul class="sidebar-sub">
                    <li><a href="#foreign-keys">Foreign Keys</a></li>
                    <li><a href="#primary-key">PRIMARY KEY</a></li>
                    <li><a href="#unique-not-null">UNIQUE &amp; NOT NULL</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#sql-export">SQL Export</a>
                <ul class="sidebar-sub">
                    <li><a href="#mysql-export">MySQL</a></li>
                    <li><a href="#postgres-export">PostgreSQL</a></li>
                    <li><a href="#one-click-copy">One-Click Copy</a></li>
                </ul>
            </li>
            <li>
                <a class="sidebar-section" href="#account">Account &amp; Organisation</a>
                <ul class="sidebar-sub">
                    <li><a href="#free-account">Free Account</a></li>
                    <li><a href="#multiple-diagrams">Multiple Diagrams</a></li>
                    <li><a href="#browser-based">Browser-Based</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    {{-- Main content --}}
    <div class="docs-content">
        <h1>Features</h1>
        <p class="docs-intro">SQL Designer is a free, browser-based ERD tool for MySQL and PostgreSQL. Here is everything it can do.</p>

        {{-- Canvas & Editing --}}
        <section class="docs-section" id="canvas" aria-labelledby="section-canvas">
            <h2 id="section-canvas">Canvas &amp; Editing</h2>

            <div class="feature-entry" id="drag-drop">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M2 2h4v4H2zm8 0h4v4h-4zM2 10h4v4H2zm8 0h4v4h-4z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Drag-and-Drop Canvas</h3>
                    <p>Place and reposition tables freely on an infinite canvas. Pan and zoom to work with schemas of any size without losing track of structure.</p>
                </div>
            </div>

            <div class="feature-entry" id="tables-columns">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M1 3h14v2H1zm0 4h14v2H1zm0 4h14v2H1z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Tables &amp; Columns</h3>
                    <p>Add as many tables as you need. Each table holds any number of columns with a name, data type, and optional constraints. Rename and reorder on the fly.</p>
                </div>
            </div>

            <div class="feature-entry" id="data-types">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M2 2h12v3H2zm0 5h12v3H2zm0 5h12v2H2z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Column Data Types</h3>
                    <p>Choose from all common MySQL and PostgreSQL data types: <code>INT</code>, <code>BIGINT</code>, <code>VARCHAR</code>, <code>TEXT</code>, <code>BOOLEAN</code>, <code>DATE</code>, <code>TIMESTAMP</code>, <code>UUID</code>, <code>DECIMAL</code>, <code>JSON</code>, and more.</p>
                </div>
            </div>

            <div class="feature-entry" id="auto-save">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M13 1H3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V3a2 2 0 00-2-2zm-2 12H5v-4h6v4zm2 0h-1v-5H4v5H3V3h10v10z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Auto-Save</h3>
                    <p>Every change is saved automatically to your account. Close the browser, switch devices, come back later — your work is exactly where you left it.</p>
                </div>
            </div>

        </section>

        {{-- Relationships --}}
        <section class="docs-section" id="relationships" aria-labelledby="section-relationships">
            <h2 id="section-relationships">Relationships &amp; Constraints</h2>

            <div class="feature-entry" id="foreign-keys">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M1 2h6v5H1zm8 0h6v5H9zM1 10h6v4H1zm8 0h6v4H9z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Foreign Key Relationships</h3>
                    <p>Draw lines between columns to define foreign key constraints. Relationships are visualised with crow's foot (chicken-foot) notation so cardinality is immediately clear.</p>
                </div>
            </div>

            <div class="feature-entry" id="primary-key">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M7 0a5 5 0 100 10 5 5 0 000-10zm0 8a3 3 0 110-6 3 3 0 010 6zm1 2h4v2h-2v2h-2v-4z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>PRIMARY KEY</h3>
                    <p>Mark any column as the primary key directly in the diagram. The generated SQL includes the <code>PRIMARY KEY</code> constraint automatically.</p>
                </div>
            </div>

            <div class="feature-entry" id="unique-not-null">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M8 1a7 7 0 100 14A7 7 0 008 1zm-.75 3.5h1.5v5h-1.5v-5zm0 6h1.5v1.5h-1.5V10.5z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>UNIQUE &amp; NOT NULL</h3>
                    <p>Toggle <code>UNIQUE</code> and <code>NOT NULL</code> constraints per column with a checkbox. No DDL to write — the SQL is generated correctly for you.</p>
                </div>
            </div>

        </section>

        {{-- SQL Export --}}
        <section class="docs-section" id="sql-export" aria-labelledby="section-export">
            <h2 id="section-export">SQL Export</h2>

            <div class="feature-entry" id="mysql-export">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M2 1h12a1 1 0 011 1v3H1V2a1 1 0 011-1zm-1 5h14v8a1 1 0 01-1 1H2a1 1 0 01-1-1V6z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>MySQL Export</h3>
                    <p>Generate a complete MySQL <code>CREATE TABLE</code> script for your entire schema — column definitions, data types, constraints, and foreign keys included. Ready to paste into MySQL Workbench, DBeaver, or a terminal.</p>
                </div>
            </div>

            <div class="feature-entry" id="postgres-export">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M2 1h12a1 1 0 011 1v3H1V2a1 1 0 011-1zm-1 5h14v8a1 1 0 01-1 1H2a1 1 0 01-1-1V6z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>PostgreSQL Export</h3>
                    <p>Switch to PostgreSQL dialect and export a schema script compatible with <code>psql</code>, pgAdmin, Supabase, or any Postgres-compatible tool. The same diagram, the right syntax.</p>
                </div>
            </div>

            <div class="feature-entry" id="one-click-copy">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M10 1H2a1 1 0 00-1 1v10h1V2h8V1zm2 2H5a1 1 0 00-1 1v11a1 1 0 001 1h7a1 1 0 001-1V4a1 1 0 00-1-1zm0 12H5V4h7v11z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>One-Click Copy</h3>
                    <p>Copy the full generated SQL to your clipboard with a single click. No file to download, no extra steps.</p>
                </div>
            </div>

        </section>

        {{-- Account --}}
        <section class="docs-section" id="account" aria-labelledby="section-account">
            <h2 id="section-account">Account &amp; Organisation</h2>

            <div class="feature-entry" id="free-account">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M8 8a4 4 0 100-8 4 4 0 000 8zm0 1c-5.33 0-8 2.67-8 4v1h16v-1c0-1.33-2.67-4-8-4z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Free Account</h3>
                    <p>Sign up with your email address. No credit card, no subscription, no trial — all features are available immediately after registration with no expiry.</p>
                </div>
            </div>

            <div class="feature-entry" id="multiple-diagrams">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M1 1h8v8H1zm6 6H3V3h4v4zM7 9h8v6H7zm6 4H9v-2h4v2zM0 9h6v6H0zm5 4H1v-2h4v2z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Multiple Diagrams</h3>
                    <p>Create unlimited diagrams — one per project, microservice, or database. All are saved to your account and accessible from any device.</p>
                </div>
            </div>

            <div class="feature-entry" id="browser-based">
                <div class="feat-icon">
                    <svg viewBox="0 0 16 16"><path d="M0 3a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H2a2 2 0 01-2-2V3zm14 0H2v1h12V3zm0 3H2v7h12V6z"/></svg>
                </div>
                <div class="feat-body">
                    <h3>Browser-Based, Nothing to Install</h3>
                    <p>SQL Designer runs entirely in your browser. Open <a href="https://sql-designer.com" style="color:var(--color-primary-text)">sql-designer.com</a> in Chrome, Firefox, Safari, or Edge and start designing — no download, no extension, no setup.</p>
                </div>
            </div>
        </section>
    </div>

</div>

<script>
    // Highlight active sidebar link on scroll
    (function () {
        const anchors = document.querySelectorAll('.docs-section, .feature-entry[id]');
        const sidebarLinks = document.querySelectorAll('.docs-sidebar a');

        function onScroll() {
            let current = '';
            anchors.forEach(el => {
                if (window.scrollY >= el.offsetTop - 80) current = el.id;
            });
            sidebarLinks.forEach(a => {
                const href = a.getAttribute('href');
                a.classList.toggle('active', href === '#' + current);
            });
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    })();
</script>
@endsection
