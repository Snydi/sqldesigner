@extends('layouts.main')

@section('title', 'About SQL Designer — Built by Dmitriy Snyatkov')

@section('head')
    <meta name="description"
          content="SQL Designer is a free visual database designer built by Dmitriy Snyatkov. Learn about the tool, why it was built, and the person behind it.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/about">
    <meta property="og:title" content="About SQL Designer — Built by Dmitriy Snyatkov">
    <meta property="og:description"
          content="SQL Designer is a free visual ERD and database schema designer for MySQL, PostgreSQL, and more. Built and maintained by Dmitriy Snyatkov.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/about">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <script type="application/ld+json">
        @verbatim
        [
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sql-designer.com/" },
                { "@type": "ListItem", "position": 2, "name": "About", "item": "https://sql-designer.com/about" }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "AboutPage",
            "name": "About SQL Designer",
            "url": "https://sql-designer.com/about",
            "description": "SQL Designer is a free visual ERD and database schema designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. Built and maintained by Dmitriy Snyatkov.",
            "author": {
                "@type": "Person",
                "name": "Dmitriy Snyatkov",
                "url": "https://sql-designer.com/about",
                "sameAs": "https://github.com/Snydi"
            },
            "publisher": {
                "@type": "Organization",
                "name": "SQL Designer",
                "url": "https://sql-designer.com",
                "sameAs": "https://github.com/Snydi/sqldesigner"
            }
        },
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "name": "Dmitriy Snyatkov",
            "url": "https://sql-designer.com/about",
            "sameAs": "https://github.com/Snydi",
            "jobTitle": "Software Developer",
            "worksFor": {
                "@type": "Organization",
                "name": "SQL Designer",
                "url": "https://sql-designer.com"
            }
        }
        ]
        @endverbatim
    </script>
    <style>
        body { overflow-y: auto; }
        .about-page {
            max-width: 760px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
        }
        .about-page .breadcrumb {
            font-size: 1rem;
            color: #767676;
            margin-bottom: 1.5rem;
        }
        .about-page .breadcrumb a { color: var(--color-primary-text); }
        .about-page h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-primary);
            margin: 0 0 1.5rem;
            line-height: 1.3;
        }
        .about-page .intro {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            border-left: 3px solid var(--color-primary-text);
            padding-left: 1.2rem;
        }
        .about-page h2 {
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-primary-text);
            margin: 2.5rem 0 0.8rem;
        }
        .about-page p {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin: 0 0 1rem;
        }
        .about-page a { color: var(--color-primary-text); text-decoration: none; }
        .about-page a:hover { text-decoration: underline; }
        .author-card {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .author-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid var(--border-color);
        }
        .author-info { flex: 1; }
        .author-info h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.3rem;
        }
        .author-info p {
            font-size: 1rem;
            color: var(--text-secondary);
            margin: 0 0 0.6rem;
        }
        .author-links { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .author-links a {
            font-size: 1rem;
            color: var(--text-subtle);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border: 1px solid var(--border-color);
            padding: 0.3rem 0.7rem;
            border-radius: 4px;
            transition: border-color 120ms, color 120ms;
        }
        .author-links a:hover {
            color: var(--color-primary-text);
            border-color: var(--color-primary-text);
            text-decoration: none;
        }
        .tool-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .tool-item {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1rem;
        }
        .tool-item h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-primary-text);
            margin: 0 0 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-family: 'JetBrains Mono', monospace;
        }
        .tool-item p {
            font-size: 1rem;
            color: var(--text-subtle);
            margin: 0;
        }
        @media (max-width: 540px) {
            .author-card { flex-direction: column; }
        }
    </style>
@endsection

@section('content')
    <div class="about-page">
        <p class="breadcrumb"><a href="/">Home</a> &rsaquo; About</p>

        <h1>About SQL Designer</h1>

        <p class="intro">
            SQL Designer is a free, browser-based visual database designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. It was built to make database schema design faster and more accessible — without installing software, creating an account on a paid platform, or hitting a paywall for SQL export.
        </p>

        <h2>Who Built This</h2>

        <div class="author-card">
            <img src="/images/author_photo.jpeg" alt="Dmitriy Snyatkov" class="author-avatar" width="64" height="64" loading="lazy">
            <div class="author-info">
                <h3>Dmitriy Snyatkov</h3>
                <p>Software developer and the creator of SQL Designer. I've been building web applications for over 3 years, with a focus on developer tooling and data-heavy systems. I built SQL Designer because I kept running into ERD tools that looked great in screenshots but hit paywalls the moment you tried to export SQL or create a second diagram. SQL Designer began development in 2024 and launched publicly in 2026, and has since been used by developers across the stack — from junior devs sketching their first normalized schema to teams documenting production databases.</p>
                <div class="author-links">
                    <a href="https://github.com/Snydi" target="_blank" rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        GitHub
                    </a>
                    <a href="mailto:dmitriy@sql-designer.com">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        Email
                    </a>
                </div>
            </div>
        </div>

        <h2>Why SQL Designer Exists</h2>
        <p>
            Most ERD tools fall into one of two categories: generic drawing tools (like draw.io or Miro) that produce a picture of a schema but can't validate SQL types or export DDL, and dedicated database design tools that are either paid, require a desktop install, or cap free users at a small number of diagrams.
        </p>
        <p>
            The frustration that led to building SQL Designer was specific: trying to sketch a normalized schema for a multi-tenant SaaS application and hitting a paywall every time I wanted to export the DDL. The free tier of every dedicated tool I tried — DrawSQL, dbdiagram.io, QuickDBD — had table limits, export limits, or both. draw.io worked but knew nothing about MySQL vs PostgreSQL type differences. I ended up writing the CREATE TABLE statements by hand, which is exactly what a visual tool is supposed to prevent.
        </p>
        <p>
            SQL Designer sits in a different spot: it understands real database types and constraints, generates valid <code>CREATE TABLE</code> scripts for six dialects, and offers a Free plan with 1 diagram and 3 daily combined exports. Pro removes the diagram and export limits. The source code is open on GitHub.
        </p>

        <h2>What It Does</h2>
        <div class="tool-grid">
            <div class="tool-item">
                <h4>Visual Schema Design</h4>
                <p>Drag-and-drop tables, columns with real data types, and foreign key relationships with crow's foot notation.</p>
            </div>
            <div class="tool-item">
                <h4>Six Database Dialects</h4>
                <p>MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access — each with type-specific column pickers.</p>
            </div>
            <div class="tool-item">
                <h4>SQL Export &amp; Import</h4>
                <p>Export valid CREATE TABLE scripts or import an existing SQL script to generate a visual ER diagram instantly.</p>
            </div>
            <div class="tool-item">
                <h4>Collaboration &amp; Sharing</h4>
                <p>Real-time multiplayer editing, shareable links with read-only or edit access, and embeddable iframes.</p>
            </div>
        </div>

        <h2>How It Was Built</h2>
        <p>
            Development started in 2024. The first working version was a single-page app with a canvas rendered in SVG and SQL output hardcoded for MySQL. Over the following months the rendering layer was rewritten around Vue Flow for proper node-based graph interactions, PostgreSQL was added as a second dialect (which required rethinking the column type system entirely — the two databases share very few type names), and the backend moved to Laravel with a PostgreSQL store for diagrams and user accounts.
        </p>
        <p>
            The six-dialect SQL export — MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access — was the most time-consuming part to get right. Each dialect has different quoting characters, different identity/auto-increment syntax, different constraint placement rules, and different sets of native types. The type picker component adapts to the selected dialect, so a column you define as <code>SERIAL</code> in PostgreSQL mode becomes <code>INT AUTO_INCREMENT</code> in MySQL without you having to remember the difference. The tool launched publicly in early 2026.
        </p>

        <h2>Open Source</h2>
        <p>
            SQL Designer is open source. The full source code — Laravel backend, Vue 3 frontend, and PostgreSQL schema — is available on <a href="https://github.com/Snydi/sqldesigner" target="_blank" rel="noopener noreferrer">GitHub</a>. Issues, pull requests, and feature suggestions are welcome.
        </p>

        <h2>Content on This Site</h2>
        <p>
            The blog covers database design, ER diagram concepts, SQL reference topics, and comparisons of ERD tools. All articles are written by Dmitriy Snyatkov based on direct experience building and using the tool. Where SQL Designer is compared to alternatives, it is identified explicitly to avoid any conflict-of-interest ambiguity.
        </p>
        <p>
            Questions or corrections? <a href="mailto:dmitriy@sql-designer.com">Email directly</a> or open an issue on GitHub.
        </p>
    </div>
@endsection
