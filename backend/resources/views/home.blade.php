@extends('layouts.main')

@section('title', 'SQL Designer — Free Online Database Schema Designer')

@section('head')
    <meta name="description" content="Free online database schema designer and ER diagram tool for MySQL, PostgreSQL, SQLite and more. Build tables visually, draw relationships, export SQL.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SQL Designer">
    <meta name="theme-color" content="#5db583">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="SQL Designer — Free Online Database Schema Designer">
    <meta property="og:description" content="Free online database schema designer and ER diagram tool for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. No install, no subscription.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual database schema editor for MySQL, PostgreSQL, SQLite, Oracle and more">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer — Free Online Database Schema Designer">
    <meta name="twitter:description" content="Free online database schema designer and ER diagram tool for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. No install, no subscription.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <link rel="canonical" href="https://sql-designer.com/">
    <link rel="preload" as="image" type="image/webp" fetchpriority="high"
          imagesrcset="/images/designer_screenshot-400w.webp 400w, /images/designer_screenshot-800w.webp 800w, /images/designer_screenshot-1120w.webp 1120w, /images/designer_screenshot.webp 2240w"
          imagesizes="(max-width: 760px) 100vw, 1120px">
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "WebSite",
                "@id": "https://sql-designer.com/#website",
                "name": "SQL Designer",
                "url": "https://sql-designer.com",
                "description": "Free visual database designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access."
            },
            {
                "@type": "SoftwareApplication",
                "@id": "https://sql-designer.com/#app",
                "name": "SQL Designer",
                "url": "https://sql-designer.com",
                "description": "Free visual database designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. Drag-and-drop tables, define relationships, and export SQL scripts. No install, no subscription.",
                "applicationCategory": "DeveloperApplication",
                "operatingSystem": "Any",
                "browserRequirements": "Requires a modern browser with JavaScript enabled",
                "offers": {
                    "@type": "Offer",
                    "price": "0",
                    "priceCurrency": "USD"
                },
                "featureList": [
                    "Drag-and-drop canvas",
                    "MySQL support",
                    "PostgreSQL support",
                    "SQLite support",
                    "Oracle support",
                    "SQL Server support",
                    "MS Access support",
                    "Foreign key relationships",
                    "Crow's foot notation",
                    "PRIMARY and UNIQUE constraints",
                    "UNSIGNED and NOT NULL properties",
                    "Unlimited diagrams",
                    "Auto-save",
                    "SQL export",
                    "JSON export",
                    "Laravel migration export"
                ],
                "provider": {"@id": "https://sql-designer.com/#organization"},
                "screenshot": {
                    "@type": "ImageObject",
                    "url": "https://sql-designer.com/images/designer_screenshot.webp",
                    "width": 2240,
                    "height": 1111,
                    "caption": "SQL Designer canvas showing ER diagram with drag-and-drop tables and foreign key relationships"
                }
            },
            {
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "Is SQL Designer free?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Yes, completely free. No subscription or any paywall."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Is there anything to install?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "No — SQL Designer runs entirely in your browser."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "What SQL dialects are supported?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Do I need to know SQL?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "No. You design visually — drag tables, click columns, draw relationships. The SQL is generated for you."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Can I import an existing schema?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Yes. Paste a CREATE TABLE script or import it from file and SQL Designer transforms it into a visual diagram you can edit."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Is my work saved?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Yes — changes auto-save to your account."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "How many diagrams can I create?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "There is no limit. Create as many diagrams as you need."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Is SQL Designer open source?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Yes. The source code is publicly available on GitHub at https://github.com/Snydi/sqldesigner."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "How do I create a database schema online?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Open SQL Designer, click New Table to add your first table, define columns and data types, then draw foreign key relationships by connecting columns on the canvas. When ready, click Export to generate a complete CREATE TABLE script. No account needed to start — sign up only when you want to save your work."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "Can I use SQL Designer as a MySQL database designer?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "Yes. SQL Designer has a dedicated MySQL mode with MySQL-specific data types (TINYINT, MEDIUMINT, ENUM, SET, and more) and exports valid MySQL CREATE TABLE syntax. The same applies to PostgreSQL, SQLite, Oracle, SQL Server, and MS Access."
                        }
                    },
                    {
                        "@type": "Question",
                        "name": "What is an ER diagram maker?",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "An ER diagram maker (entity-relationship diagram maker) is a tool for visually designing database schemas. You create entities (tables), define attributes (columns), and draw relationships between them. SQL Designer is a free, browser-based ER diagram maker that also generates the SQL CREATE TABLE script from your diagram."
                        }
                    }
                ]
            },
            {
                "@type": "VideoObject",
                "@id": "https://sql-designer.com/#demo-video",
                "name": "SQL Designer: Free Online Database Schema Tool (Demo)",
                "description": "SQL Designer is a free, online visual tool for designing, editing, and exporting database schemas. This 2-minute demo shows the /demo page, signing up, creating and editing tables, exporting a diagram, and importing an existing SQL database into a visual ERD.",
                "thumbnailUrl": "https://i.ytimg.com/vi/10gHB66qR_o/maxresdefault.jpg",
                "uploadDate": "2026-07-03T00:00:00+00:00",
                "duration": "PT2M",
                "contentUrl": "https://youtu.be/10gHB66qR_o",
                "embedUrl": "https://www.youtube.com/embed/10gHB66qR_o",
                "publisher": {"@id": "https://sql-designer.com/#organization"}
            },
            {
                "@type": "Organization",
                "@id": "https://sql-designer.com/#organization",
                "name": "SQL Designer",
                "url": "https://sql-designer.com",
                "logo": {
                    "@type": "ImageObject",
                    "url": "https://sql-designer.com/favicon-192x192.png",
                    "width": 192,
                    "height": 192
                },
                "description": "Free online visual database designer for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access.",
                "sameAs": [
                    "https://github.com/Snydi/sqldesigner",
                    "https://alternativeto.net/software/sql-designer/",
                    "https://www.youtube.com/@sql-designer"
                ],
                "founder": {
                    "@type": "Person",
                    "name": "Dmitriy Snyatkov",
                    "url": "https://sql-designer.com/about"
                },
                "foundingDate": "2024"
            }
        ]
    }
    @endverbatim
    </script>
    <style>
        /* ── Section primitive ──────────────────────────── */
        section.block { padding: clamp(3rem, 6vw, 5rem) var(--gutter); }
        .block-inner { max-width: var(--maxw); margin: 0 auto; }

        h2.section-h2 {
            font-size: clamp(1.5rem, 2.6vw, 2rem);
            letter-spacing: -0.02em; font-weight: 600;
            margin: 0 0 1.5rem; text-wrap: balance;
        }

        /* ── Hero ───────────────────────────────────────── */
        .hero {
            padding: clamp(3rem, 7vw, 5.5rem) var(--gutter) 0;
            border-bottom: 1px solid var(--border-light);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background-image:
                linear-gradient(var(--border-light) 1px, transparent 1px),
                linear-gradient(90deg, var(--border-light) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: linear-gradient(to bottom, black 0%, transparent 75%);
            opacity: 0.35; pointer-events: none;
        }
        .hero-inner {
            max-width: 680px; margin: 0 auto;
            text-align: center;
            display: flex; flex-direction: column; align-items: center;
            position: relative;
        }
        .hero-screenshot {
            max-width: var(--maxw); margin: clamp(2rem, 4vw, 3rem) auto 0;
            position: relative; border-radius: 10px 10px 0 0;
            border: 1px solid var(--border-color); border-bottom: none;
            box-shadow: 0 -8px 40px -8px rgba(93,181,131,0.08), 0 1px 0 rgba(255,255,255,0.02) inset;
            overflow: hidden; aspect-ratio: 2240 / 1111;
        }

        .eyebrow {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase;
            color: var(--color-primary-text);
            padding: 0.3rem 0.6rem; border: 1px solid var(--border-color);
            border-radius: 999px; background: var(--bg-surface);
        }
        .eyebrow .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--color-primary-text); box-shadow: 0 0 10px var(--color-primary-text); }

        h1.hero-h1 {
            font-size: clamp(2rem, 4.5vw, 3.4rem);
            line-height: 1.05; letter-spacing: -0.025em; font-weight: 600;
            margin: 1rem 0 1.1rem; text-wrap: balance;
        }
        h1.hero-h1 em { font-style: normal; color: var(--color-primary-text); }

        .hero-sub {
            font-size: 1.05rem; color: var(--text-secondary);
            max-width: 48ch; text-wrap: pretty; margin: 0 0 1.6rem;
        }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 0.6rem; justify-content: center; }
        .hero-meta {
            display: flex; flex-wrap: wrap; gap: 1.25rem; margin-top: 1.4rem;
            font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: var(--text-muted);
            justify-content: center;
        }
        .hero-meta span { display: inline-flex; align-items: center; gap: 0.45rem; }
        .hero-meta .tick { color: var(--color-primary-text); }

        /* ── Hero diagram frame ─────────────────────────── */
        .diagram-canvas {
            position: absolute; inset: 34px 0 0 0;
            width: 100%; height: calc(100% - 34px);
            object-fit: cover; object-position: top left; display: block;
        }

        /* ── How it works ───────────────────────────────── */
        .how { background: var(--bg-page); border-bottom: 1px solid var(--border-light); }
        .how-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px;
            background: var(--border-light); border: 1px solid var(--border-light);
            border-radius: 10px; overflow: hidden; margin-top: 1rem;
        }
        @media (max-width: 760px) { .how-grid { grid-template-columns: 1fr; } }
        .how-step {
            background: var(--bg-surface); padding: 1.6rem 1.5rem 1.8rem;
            display: flex; flex-direction: column; gap: 0.5rem;
        }
        .how-step .num { font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: var(--color-primary-text); letter-spacing: 0.14em; }
        .how-step h3 { font-size: 1.05rem; font-weight: 600; margin: 0; letter-spacing: -0.01em; }
        .how-step p { font-size: 0.92rem; color: var(--text-secondary); margin: 0; text-wrap: pretty; }
        .how-step .glyph {
            margin-top: 0.6rem; padding-top: 0.9rem; border-top: 1px dashed var(--border-color);
            font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: var(--text-muted);
        }
        .how-video-facade {
            display: block; position: relative; width: 100%; max-width: 720px;
            margin: 2rem auto 0; border-radius: 8px; overflow: hidden;
            border: 1px solid var(--border-color); background: none; padding: 0;
            cursor: pointer; aspect-ratio: 16 / 9;
        }
        .how-video-facade img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .how-video-facade .play-glyph {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 64px; height: 64px; border-radius: 50%;
            background: rgba(0,0,0,0.65); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; transition: background 120ms ease;
        }
        .how-video-facade:hover .play-glyph { background: var(--color-primary-text); }
        iframe.how-video-facade { aspect-ratio: 16 / 9; }

        /* ── Features ───────────────────────────────────── */
        .features { background: var(--bg-elevated); border-bottom: 1px solid var(--border-light); }
        .features-grid {
            display: grid; grid-template-columns: 1.1fr 1fr;
            gap: clamp(2rem, 5vw, 4rem); align-items: start;
        }
        @media (max-width: 820px) { .features-grid { grid-template-columns: 1fr; } }
        .features-list {
            list-style: none; margin: 0; padding: 0;
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.1rem 1.5rem;
        }
        @media (max-width: 540px) { .features-list { grid-template-columns: 1fr; } }
        .features-list li {
            display: flex; gap: 0.6rem; align-items: baseline;
            padding: 0.7rem 0; border-bottom: 1px solid var(--border-light);
            font-size: 0.92rem; color: var(--text-secondary);
        }
        .features-list li::before {
            content: '+'; color: var(--color-primary-text);
            font-family: 'JetBrains Mono', monospace; font-weight: 600;
        }
        .features-side { align-self: center; }
        .features-side h3 { font-size: 1.1rem; margin: 0 0 0.6rem; letter-spacing: -0.01em; }
        .features-side p { color: var(--text-secondary); font-size: 0.95rem; margin: 0 0 1rem; max-width: 36ch; }


        /* ── FAQ ────────────────────────────────────────── */
        .faq { border-bottom: 1px solid var(--border-light); }
        .faq-grid {
            display: grid; grid-template-columns: 1fr 1.7fr;
            gap: clamp(1.5rem, 5vw, 4rem); align-items: start;
        }
        @media (max-width: 820px) { .faq-grid { grid-template-columns: 1fr; } }
        .faq-list {
            list-style: none; margin: 0; padding: 0;
            border-top: 1px solid var(--border-color);
        }
        .faq-item { border-bottom: 1px solid var(--border-color); }
        .faq-item summary {
            cursor: pointer; list-style: none;
            padding: 1.05rem 0;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 0.98rem; font-weight: 500; letter-spacing: -0.005em;
        }
        .faq-item summary::-webkit-details-marker { display: none; }
        .faq-item summary::after {
            content: '+'; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);
            font-size: 1.05rem; transition: transform 200ms ease; flex-shrink: 0; margin-left: 1rem;
        }
        .faq-item[open] summary::after { content: '−'; color: var(--color-primary-text); }
        .faq-item p { margin: 0 0 1.1rem; color: var(--text-secondary); font-size: 0.92rem; max-width: 65ch; text-wrap: pretty; }
        .faq-item code {
            font-family: 'JetBrains Mono', monospace; font-size: 0.85em;
            background: var(--bg-surface); padding: 1px 6px; border-radius: 3px; color: var(--text-primary);
        }

        /* ── Blog ───────────────────────────────────────── */
        .blog { background: var(--bg-elevated); border-bottom: 1px solid var(--border-light); }
        .blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        @media (max-width: 820px) { .blog-grid { grid-template-columns: 1fr; } }
        .blog-card {
            display: flex; flex-direction: column; gap: 0.4rem;
            padding: 1.4rem; border: 1px solid var(--border-color); border-radius: 8px;
            background: var(--bg-surface);
            transition: border-color 120ms ease, transform 120ms ease;
        }
        .blog-card:hover { border-color: var(--color-primary-text); transform: translateY(-2px); }
        .blog-card .cat { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 0.14em; color: var(--text-muted); text-transform: uppercase; }
        .blog-card h3 { margin: 0; font-size: 1rem; letter-spacing: -0.01em; font-weight: 600; }
        .blog-card p { margin: 0; color: var(--text-secondary); font-size: 0.88rem; line-height: 1.55; }
        .blog-card .read { margin-top: 0.4rem; color: var(--color-primary-text); font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; }

        /* ── Stats ───────────────────────────────────────── */
        .stats-bar-inner {
            display: flex; justify-content: center; gap: clamp(2rem, 6vw, 4rem);
            flex-wrap: wrap;
        }
        .stat-item {
            display: flex; flex-direction: column; align-items: center; gap: 0.15rem;
            font-family: 'JetBrains Mono', monospace;
        }
        .stat-value {
            font-size: clamp(1.4rem, 3vw, 2rem);
            font-weight: 700; letter-spacing: -0.03em;
            color: var(--color-primary-text);
        }
        .stat-label {
            font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--text-muted);
        }
        .stat-online .stat-value::before {
            content: ''; display: inline-block;
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--color-primary-text);
            box-shadow: 0 0 6px var(--color-primary-text);
            margin-right: 0.4rem; vertical-align: middle;
        }

        /* ── Final CTA ──────────────────────────────────── */
        .final-cta {
            text-align: center;
            padding: clamp(3rem, 6vw, 5rem) var(--gutter);
            background: radial-gradient(ellipse at center, rgba(93,181,131,0.07), transparent 60%), var(--bg-page);
        }
        .final-cta h2 {
            font-size: clamp(1.6rem, 3vw, 2.3rem); letter-spacing: -0.02em;
            margin: 0 0 0.6rem; text-wrap: balance;
        }
        .final-cta p { color: var(--text-secondary); max-width: 48ch; margin: 0 auto 1.4rem; }
    </style>
@endsection

@section('content')

<!-- ── Hero ─────────────────────────────────────────────── -->
<section class="hero" aria-labelledby="hero-h1">
    <div class="hero-inner">
        <span class="eyebrow"><span class="dot"></span> Free ERD tool · MySQL · PostgreSQL · SQLite · Oracle · SQL Server · MS Access</span>
        <h1 class="hero-h1" id="hero-h1">
            Free online <em>database schema designer</em>.
            Export clean SQL.
        </h1>
        <p class="hero-sub">
            The free online database schema designer and ER diagram maker — build tables visually, draw relationships, and export clean SQL for MySQL, PostgreSQL, SQLite, Oracle, SQL Server, or MS Access. No install required.
        </p>
        <div class="hero-actions">
            <a id="hero-btn-authed" class="btn btn-solid btn-lg" href="/diagrams" style="display:none">Open My Diagrams</a>
            <a class="btn btn-solid btn-lg" href="/demo">Try demo — no signup</a>
            <a id="hero-btn-register" class="btn btn-outline btn-lg" href="/register">Sign up</a>
        </div>
        <div class="hero-meta">
            <span><span class="tick">✓</span> Unlimited diagrams</span>
            <span><span class="tick">✓</span> Free</span>
            <span><span class="tick">✓</span> Open source</span>
        </div>
        <div class="stats-bar-inner" style="margin-top:1.6rem;">
            <div class="stat-item">
                <span class="stat-value" id="stat-users">—</span>
                <span class="stat-label">Registered users</span>
            </div>
            <div class="stat-item">
                <span class="stat-value" id="stat-diagrams">—</span>
                <span class="stat-label">Diagrams created</span>
            </div>
            <div class="stat-item stat-online">
                <span class="stat-value" id="stat-online">—</span>
                <span class="stat-label">Online now</span>
            </div>
        </div>
    </div>
    <div class="hero-screenshot">
        <picture>
            <source
                type="image/webp"
                srcset="/images/designer_screenshot-400w.webp 400w, /images/designer_screenshot-800w.webp 800w, /images/designer_screenshot-1120w.webp 1120w, /images/designer_screenshot.webp 2240w"
                sizes="(max-width: 760px) 100vw, 1120px">
            <img class="diagram-canvas"
                 src="/images/designer_screenshot.webp"
                 alt="SQL Designer canvas showing an ER diagram with orders, users, products, and categories tables"
                 width="2240" height="1111"
                 fetchpriority="high">
        </picture>
    </div>
    <script>
        if (localStorage.getItem('auth_token')) {
            document.getElementById('hero-btn-authed').style.display = 'inline-flex';
            document.getElementById('hero-btn-register').style.display = 'none';
        }
        fetch('/api/stats').then(function(r){ return r.json(); }).then(function(d){
            document.getElementById('stat-users').textContent    = d.users.toLocaleString();
            document.getElementById('stat-diagrams').textContent = d.diagrams.toLocaleString();
            document.getElementById('stat-online').textContent   = d.online.toLocaleString();
        }).catch(function(){});
    </script>
</section>

<section class="block" aria-labelledby="what-is-h2" style="background:var(--bg-page); border-bottom:1px solid var(--border-light);">
    <div class="block-inner">
        <h2 class="section-h2" id="what-is-h2">What is SQL Designer?</h2>
        <p style="color:var(--text-secondary); font-size:0.97rem; line-height:1.75;">
            SQL Designer is a free entity relationship diagram (ERD) tool — a browser-based database schema designer built for developers who need to model relational databases visually. It supports six SQL dialects — MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access — with dedicated column type pickers and DDL export for each engine. The design workflow is visual: create tables, add columns using real database types such as <code style="font-family:'JetBrains Mono',monospace; font-size:0.9em; background:var(--bg-surface); padding:1px 5px; border-radius:3px;">INT</code>, <code style="font-family:'JetBrains Mono',monospace; font-size:0.9em; background:var(--bg-surface); padding:1px 5px; border-radius:3px;">VARCHAR</code>, and <code style="font-family:'JetBrains Mono',monospace; font-size:0.9em; background:var(--bg-surface); padding:1px 5px; border-radius:3px;">DECIMAL</code>, set PRIMARY KEY, UNIQUE, and NOT NULL constraints with toggles, and draw foreign key relationships by connecting columns on the canvas. The diagram uses crow's foot notation for cardinality. When the schema is ready, export a complete CREATE TABLE script in one click — or paste an existing SQL script to visualize it instantly as an editable diagram. SQL Designer runs entirely in the browser with no download or installation required. Unlimited diagrams, SQL export, real-time collaboration, shareable links, and embeddable iframes are all free, with no credit card required. The full source code is available on <a href="https://github.com/Snydi/sqldesigner" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary-text);">GitHub</a> under an open-source license.
        </p>
    </div>
</section>

<section class="block how" aria-labelledby="how-h2">
    <div class="block-inner">
        <h2 class="section-h2" id="how-h2">How it works</h2>
        <div class="how-grid">
            <div class="how-step">
                <span class="num">01 / IMPORT</span>
                <h3>Import your DB or start from scratch</h3>
                <p>Use SQL or JSON schema you have. Optionally, you can just start designing from scratch</p>
            </div>
            <div class="how-step">
                <span class="num">02 / DESIGN</span>
                <h3>Create tables, draw relations</h3>
                <p>Press a few buttons to create tables and columns. Draw relations with your mouse.</p>
            </div>
            <div class="how-step">
                <span class="num">03 / EXPORT</span>
                <h3>Generate CREATE TABLE script</h3>
                <p>Export valid SQL, JSON or even Laravel migration file. More formats coming soon!</p>
            </div>
        </div>
        <button type="button" class="how-video-facade" id="demo-video-facade" aria-label="Play SQL Designer demo video">
            <img src="https://i.ytimg.com/vi/10gHB66qR_o/maxresdefault.jpg" alt="SQL Designer demo video thumbnail" loading="lazy" width="1280" height="720">
            <span class="play-glyph" aria-hidden="true">▶</span>
        </button>
        <script>
            document.getElementById('demo-video-facade').addEventListener('click', function onClick() {
                var iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube.com/embed/10gHB66qR_o?autoplay=1';
                iframe.title = 'SQL Designer demo: create tables, export a diagram, and import a SQL database';
                iframe.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
                iframe.allowFullscreen = true;
                iframe.className = 'how-video-facade';
                iframe.style.border = '0';
                this.replaceWith(iframe);
            });
        </script>
    </div>
</section>

<section class="block features" aria-labelledby="features-h2">
    <div class="block-inner">
        <div class="features-grid">
            <div>
                <h2 class="section-h2" id="features-h2">Features</h2>
                <ul class="features-list" aria-label="Feature list">
                    <li>Drag-and-drop canvas</li>
                    <li>MySQL, PostgreSQL, SQLite, Oracle, SQL Server &amp; MS Access</li>
                    <li>Foreign key relationships</li>
                    <li>Crow's foot notation</li>
                    <li>PRIMARY and UNIQUE constraints</li>
                    <li>UNSIGNED &amp; NOT NULL properties</li>
                    <li>Unlimited diagrams per user</li>
                    <li>Auto-save</li>
                </ul>
            </div>
            <aside class="features-side">
                <p>No SQL code in designer. No download. No login wall in front of the canvas — try the demo, then come back when you need to keep your work.</p>
                <p><a class="btn btn-outline" href="/features">See all features →</a></p>
            </aside>
        </div>
    </div>
</section>

<section class="block faq" aria-labelledby="faq-h2">
    <div class="block-inner faq-grid">
        <div>
            <h2 class="section-h2" id="faq-h2">Common questions</h2>
            <p style="color:var(--text-secondary); font-size:0.92rem; max-width:32ch;">More on the <a href="/blog" style="color:var(--color-primary-text)">blog</a> — including comparisons with MySQL Workbench, dbdiagram.io, and Lucidchart.</p>
        </div>
        <ul class="faq-list" aria-label="Frequently asked questions">
            <li class="faq-item"><details open>
                <summary>Is SQL Designer free?</summary>
                <p>Yes, completely free. No subscription or any paywall.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Is there anything to install?</summary>
                <p>No — SQL Designer runs entirely in your browser.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>What SQL dialects are supported?</summary>
                <p>MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Do I need to know SQL?</summary>
                <p>No. You design visually — drag tables, click columns, draw relationships. The SQL is generated for you.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Can I import an existing schema?</summary>
                <p>Yes. Paste a CREATE TABLE script or import it from file and SQL Designer transforms it into a visual diagram you can edit.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Is my work saved?</summary>
                <p>Yes — changes auto-save to your account.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>How many diagrams can I create?</summary>
                <p>There is no limit. Create as many diagrams as you need.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Is SQL Designer open source?</summary>
                <p>Yes. The full source code is available on <a href="https://github.com/Snydi/sqldesigner" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary-text)">GitHub</a>. You can inspect it, report issues, or contribute.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>How do I create a database schema online?</summary>
                <p>Open SQL Designer, click <code>New Table</code> to add your first table, define columns and data types, then draw foreign key relationships by connecting columns on the canvas. Click <code>Export</code> to generate a complete CREATE TABLE script. No account needed to start — sign up only when you want to save your work.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>Can I use this as a MySQL database designer?</summary>
                <p>Yes. SQL Designer has a dedicated MySQL mode with MySQL-specific data types (TINYINT, MEDIUMINT, ENUM, SET, and more) and exports valid MySQL CREATE TABLE syntax. The same applies to PostgreSQL, SQLite, Oracle, SQL Server, and MS Access.</p>
            </details></li>
            <li class="faq-item"><details>
                <summary>What is an ER diagram maker?</summary>
                <p>An ER diagram maker is a tool for visually designing database schemas. You create entities (tables), define attributes (columns), and draw relationships between them. SQL Designer is a free, browser-based ER diagram maker that also generates the SQL CREATE TABLE script directly from your diagram.</p>
            </details></li>
        </ul>
    </div>
</section>

<section class="block blog" aria-labelledby="blog-h2">
    <div class="block-inner">
        <h2 class="section-h2" id="blog-h2">From the blog</h2>
        <div class="blog-grid">
            <a class="blog-card" href="/blog/best-free-erd-tools">
                <span class="cat">Tools</span>
                <h3>10 Best Free ERD Tools in 2026</h3>
                <p>We tested SQL Designer, DrawSQL, dbdiagram.io, ChartDB, ERDPlus and more — honest strengths, real limits.</p>
                <span class="read">Read →</span>
            </a>
            <a class="blog-card" href="/blog/mysql-vs-postgresql">
                <span class="cat">Reference</span>
                <h3>MySQL vs PostgreSQL — Key Differences</h3>
                <p>Data types, constraints, JSON support, and which database engine to choose for your next project.</p>
                <span class="read">Read →</span>
            </a>
            <a class="blog-card" href="/blog/database-normalization">
                <span class="cat">Schema Design</span>
                <h3>Database Normalization — 1NF, 2NF, 3NF</h3>
                <p>Concrete before-and-after table examples. Understand normal forms and when to denormalize.</p>
                <span class="read">Read →</span>
            </a>
            <a class="blog-card" href="/blog/crowfoot-notation">
                <span class="cat">Reference</span>
                <h3>Crow's Foot Notation Explained</h3>
                <p>One-to-one, one-to-many, many-to-many symbols — and how they map to real foreign key constraints.</p>
                <span class="read">Read →</span>
            </a>
        </div>
        <p style="margin-top:1.25rem; text-align:center;"><a href="/blog" style="color:var(--color-primary-text); font-size:0.88rem; font-family:'JetBrains Mono',monospace;">View all articles →</a></p>
    </div>
</section>

<section class="final-cta" aria-labelledby="cta-h2">
    <h2 id="cta-h2">Sketch a schema. Export the SQL.</h2>
    <p>Try the demo — no account needed. Sign up when you want to save.</p>
    <div class="hero-actions" style="justify-content:center">
        <a class="btn btn-solid btn-lg" href="/demo">Open the demo</a>
        <a class="btn btn-outline btn-lg" href="/register">Sign up to save</a>
    </div>
</section>

@endsection
