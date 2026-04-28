@extends('layouts.main')

@section('title', 'SQL Designer — Free MySQL & PostgreSQL Database Designer')

@section('head')
    <meta name="description" content="Free visual MySQL designer and PostgreSQL database designer. Drag-and-drop tables, define relationships, and export SQL scripts in seconds.">
    <meta name="keywords" content="mysql designer, mysql database designer, mysql db designer, visual sql designer, visual database designer, database designer for postgresql, ERD tool, online ERD tool, MySQL schema designer, PostgreSQL schema designer, postgres schema designer, postgres db designer, postgres database designer, database diagram tool, SQL schema visualizer, ER diagram, entity relationship diagram, database design tool, free database designer, MySQL workbench alternative, pgAdmin alternative, free postgres designer">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SQL Designer">
    <meta name="theme-color" content="#c05252">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="SQL Designer — Free MySQL & PostgreSQL Database Designer">
    <meta property="og:description" content="Free visual MySQL designer and database designer for PostgreSQL. Drag-and-drop schema editor with SQL export. No install, no subscription.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2555">
    <meta property="og:image:height" content="1267">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer — Free MySQL Designer & PostgreSQL Database Designer | ERD Tool">
    <meta name="twitter:description" content="Free visual MySQL designer and database designer for PostgreSQL. Design schemas online and export SQL. No install, no subscription.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.png">
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
                "@type": "WebApplication",
                "name": "SQL Designer",
                "url": "https://sql-designer.com",
                "description": "Free visual MySQL and PostgreSQL database designer. Drag-and-drop tables, define relationships, and export SQL scripts. No install, no subscription.",
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
                    "Foreign key relationships",
                    "Crow's foot notation",
                    "PRIMARY and UNIQUE constraints",
                    "UNSIGNED and NOT NULL properties",
                    "Unlimited diagrams",
                    "Auto-save",
                    "SQL export",
                    "JSON export",
                    "Laravel migration export"
                ]
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
                            "text": "MySQL and PostgreSQL, with more coming soon."
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
                    }
                ]
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
        <span class="eyebrow"><span class="dot"></span> Free ERD tool · MySQL &amp; PostgreSQL</span>
        <h1 class="hero-h1" id="hero-h1">
            Design database schemas <em>visually</em>.
            Export clean SQL.
        </h1>
        <p class="hero-sub">
            A browser-based database design tool for MySQL and PostgreSQL. Drag tables, draw relations, ship a CREATE TABLE script.
        </p>
        <div class="hero-actions">
            <a id="hero-btn-authed" class="btn btn-solid btn-lg" href="/diagrams" style="display:none">Open My Diagrams</a>
            <a id="hero-btn-register" class="btn btn-solid btn-lg" href="/register">Start designing</a>
            <a class="btn btn-outline btn-lg" href="/demo">Open demo →</a>
        </div>
        <div class="hero-meta">
            <span><span class="tick">✓</span> No install</span>
            <span><span class="tick">✓</span> Auto-save</span>
            <span><span class="tick">✓</span> Free</span>
        </div>
    </div>
    <div class="hero-screenshot">
        <picture>
            <source
                type="image/webp"
                srcset="/images/designer_screenshot-400w.webp 400w, /images/designer_screenshot-800w.webp 800w, /images/designer_screenshot-1120w.webp 1120w, /images/designer_screenshot.webp 2240w"
                sizes="(max-width: 760px) 100vw, 1120px">
            <img class="diagram-canvas"
                 src="/images/designer_screenshot.png"
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
    </script>
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
                <h3>Creates tables, draw relations</h3>
                <p>Press a few buttons to create tables and columns. Draw relations with your mouse.</p>
            </div>
            <div class="how-step">
                <span class="num">03 / EXPORT</span>
                <h3>Generate CREATE TABLE script</h3>
                <p>Export valid SQL, JSON or even Laravel migration file. More formats coming soon!</p>
            </div>
        </div>
    </div>
</section>

<section class="block features" aria-labelledby="features-h2">
    <div class="block-inner">
        <div class="features-grid">
            <div>
                <h2 class="section-h2" id="features-h2">Features</h2>
                <ul class="features-list" aria-label="Feature list">
                    <li>Drag-and-drop canvas</li>
                    <li>MySQL &amp; PostgreSQL support</li>
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
                <p>MySQL and PostgreSQL, with more coming soon</p>
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
        </ul>
    </div>
</section>

<section class="block blog" aria-labelledby="blog-h2">
    <div class="block-inner">
        <h2 class="section-h2" id="blog-h2">From the blog</h2>
        <div class="blog-grid">
            <a class="blog-card" href="/blog/how-to-design-mysql-database-schema">
                <h3>How to design a MySQL schema</h3>
                <p>Entities, types, primary keys, foreign keys, normalization — a working guide.</p>
                <span class="read">Read →</span>
            </a>
            <a class="blog-card" href="/blog/er-diagram-tool-online">
                <h3>ER diagrams, explained</h3>
                <p>What ER diagrams are, why they matter, and how to build one in the browser.</p>
                <span class="read">Read →</span>
            </a>
            <a class="blog-card" href="/blog/sql-to-erd">
                <h3>From SQL to ERD in 30 seconds</h3>
                <p>Paste a <code class="mono">CREATE TABLE</code> script and get a visual diagram you can edit.</p>
                <span class="read">Read →</span>
            </a>
        </div>
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
