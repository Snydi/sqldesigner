@extends('layouts.main')

@section('title', 'SQL Designer — MySQL & PostgreSQL Schema Designer')

@section('head')
    <meta name="description" content="Online ERD tool and schema designer for MySQL and PostgreSQL. Drag-and-drop tables, define relationships, and export SQL scripts in seconds. No install needed.">
    <meta name="keywords" content="ERD tool, online ERD tool, MySQL schema designer, PostgreSQL schema designer, postgres schema designer, postgres db designer, postgres database designer, database diagram tool, SQL schema visualizer, ER diagram, entity relationship diagram, database design tool, free database designer, MySQL workbench alternative, pgAdmin alternative, PostgreSQL database designer, free postgres designer">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SQL Designer">
    <meta name="theme-color" content="#c05252">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="SQL Designer — MySQL & PostgreSQL Schema Designer">
    <meta property="og:description" content="Visually design and export MySQL and PostgreSQL (postgres) database schemas with a drag-and-drop interface. Free, fast, and browser-based.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2557">
    <meta property="og:image:height" content="1269">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer — MySQL & PostgreSQL Schema Designer & ERD Tool">
    <meta name="twitter:description" content="Design, visualize, and export MySQL and PostgreSQL database schemas online. Browser-based ERD tool with SQL export.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <link rel="canonical" href="https://sql-designer.com/">
    <link rel="preload" as="image" href="/images/screenshot.webp" imagesrcset="/images/screenshot-600.webp 600w, /images/screenshot-750.webp 750w, /images/screenshot-1000.webp 1000w, /images/screenshot.webp 2557w" imagesizes="(max-width: 660px) calc(100vw - 3rem), 2000px" fetchpriority="high">
    <script type="application/ld+json">
    @verbatim
    [
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "SQL Designer",
            "url": "https://sql-designer.com",
            "description": "Online ERD tool and schema designer for MySQL and PostgreSQL with a visual drag-and-drop interface and SQL export.",
            "applicationCategory": "DeveloperApplication",
            "operatingSystem": "Any",
            "screenshot": "https://sql-designer.com/images/screenshot.png",
            "featureList": [
                "ERD tool — visual entity relationship diagram editor",
                "Visual drag-and-drop schema editor",
                "MySQL and PostgreSQL CREATE TABLE SQL export",
                "Foreign key relationship modelling",
                "PRIMARY KEY, UNIQUE, NOT NULL constraints",
                "Multiple diagrams per account",
                "Auto-save"
            ],
            "keywords": "ERD tool, online ERD tool, MySQL schema designer, PostgreSQL schema designer, database diagram tool, entity relationship diagram",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "USD"
            }
        },
        {
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "How to design a database schema with SQL Designer",
            "description": "Step-by-step guide to creating a visual MySQL or PostgreSQL database schema and exporting a SQL script.",
            "step": [
                {
                    "@type": "HowToStep",
                    "position": 1,
                    "name": "Create a Diagram",
                    "text": "Sign up and create a new diagram for your project or database."
                },
                {
                    "@type": "HowToStep",
                    "position": 2,
                    "name": "Add Tables and Columns",
                    "text": "Drag tables onto the canvas, add columns, choose data types, and set constraints."
                },
                {
                    "@type": "HowToStep",
                    "position": 3,
                    "name": "Draw Relationships",
                    "text": "Connect tables with foreign key lines to define your relational structure."
                },
                {
                    "@type": "HowToStep",
                    "position": 4,
                    "name": "Export SQL",
                    "text": "Click export to generate a clean MySQL or PostgreSQL CREATE TABLE script ready to run in your database."
                }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Is SQL Designer free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, completely free. There is no subscription, no credit card required, and no hidden fees. Create an account with your email and start designing immediately."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need to install anything?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. SQL Designer runs entirely in your browser. There is nothing to download or install — just open the site and start designing."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What SQL does it generate?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "SQL Designer generates MySQL and PostgreSQL CREATE TABLE scripts, including column definitions, data types, constraints (PRIMARY KEY, UNIQUE, NOT NULL), and foreign key relationships."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need to know SQL to use it?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. The visual interface lets you build your schema by clicking and dragging — no SQL knowledge required. The SQL is generated for you automatically."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is my work saved automatically?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Changes to your diagram are saved automatically to your account. You can close the browser and pick up where you left off at any time."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How many diagrams can I create?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "There is no limit. Create as many diagrams as you need — one per project, service, or database."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is SQL Designer a free MySQL Workbench alternative?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. SQL Designer is a free, browser-based alternative to MySQL Workbench for schema design. Unlike MySQL Workbench, it requires no download or installation — you design your schema visually in the browser and export a CREATE TABLE script in seconds."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does SQL Designer compare to dbdiagram.io?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Both tools let you design database schemas visually and export SQL. SQL Designer uses a drag-and-drop canvas with a visual ERD layout, supports both MySQL and PostgreSQL, and is completely free with no diagram limits. dbdiagram.io uses a DSL (text-based) input rather than a purely visual editor."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I use SQL Designer to create ER diagrams?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. SQL Designer is an ERD (entity-relationship diagram) tool. You add tables as entities, define columns as attributes, and draw foreign key lines between tables to model relationships — all visually on a canvas, with chicken-foot notation."
                    }
                }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "SQL Designer",
            "url": "https://sql-designer.com",
            "logo": {
                "@type": "ImageObject",
                "url": "https://sql-designer.com/favicon-192x192.png"
            },
            "sameAs": [
                "https://gitlab.com/Snydi/sql-designer"
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "SQL Designer",
            "url": "https://sql-designer.com",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "https://sql-designer.com/blog?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@context": "https://schema.org",
            "@type": "SiteLinksSearchBox",
            "url": "https://sql-designer.com",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "https://sql-designer.com/blog?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "SQL Designer — Main Pages",
            "itemListElement": [
                {
                    "@type": "SiteLinksSearchBox",
                    "@id": "https://sql-designer.com/#sitelinks"
                },
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "url": "https://sql-designer.com/"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Features",
                    "url": "https://sql-designer.com/features"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "Try Demo",
                    "url": "https://sql-designer.com/demo"
                },
                {
                    "@type": "ListItem",
                    "position": 4,
                    "name": "Blog",
                    "url": "https://sql-designer.com/blog"
                }
            ]
        }
    ]
    @endverbatim
    </script>
    <style>
        body { overflow-y: auto; margin: 0; }

        .home-page {
            color: #2c3e50;
            background: #f9f9f9;
        }

        /* Hero */
        .hero {
            background: var(--color-primary);
            color: #fff;
            text-align: center;
            padding: 5rem 1.5rem 4rem;
        }

        .hero h1 {
            font-size: 2rem;
            margin: 0 0 1rem;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .hero p {
            font-size: 1rem;
            max-width: 560px;
            margin: 0 auto 1.2rem;
            text-transform: none;
            line-height: 1.7;
        }

        .hero-free-badge {
            display: inline-block;
            background: rgba(0,0,0,0.22);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 999px;
            padding: 0.3rem 1rem;
            font-size: 0.875rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1.8rem;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .btn-hero-primary {
            background: #fff;
            color: var(--color-primary);
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-hero-primary:hover { opacity: 0.85; }

        .btn-hero-secondary {
            background: transparent;
            color: #fff;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            border: 2px solid rgba(255,255,255,0.7);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: border-color 0.2s;
        }

        .btn-hero-secondary:hover { border-color: #fff; }

        /* Screenshot preview */
        .screenshot-section {
            background: #f0f0f0;
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .screenshot-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            border: 1px solid #ddd;
            line-height: 0;
        }

        .screenshot-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Features list */
        .features {
            max-width: 860px;
            margin: 0 auto;
            padding: 4rem 1.5rem;
        }

        .section-title {
            text-align: center;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-primary);
            margin: 0 0 2rem;
        }

        .features-list {
            columns: 2;
            column-gap: 3rem;
            list-style: none;
            max-width: 560px;
            margin: 0 auto 1.8rem;
            padding: 0;
        }

        .features-list li {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #333;
            padding: 0.45rem 0;
            break-inside: avoid;
            text-transform: none;
        }

        .features-list li::before {
            content: '';
            flex-shrink: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--color-primary);
            margin-top: 0.35em;
        }

        .features-more {
            text-align: center;
            font-size: 0.85rem;
        }

        .features-more a {
            color: var(--color-primary);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }

        .features-more a:hover { text-decoration: underline; }

        @media (max-width: 560px) { .features-list { columns: 1; } }

        /* Demo nudge */
        .demo-nudge {
            background: #fff;
            border-top: 1px solid #ebebeb;
            border-bottom: 1px solid #ebebeb;
            padding: 2.5rem 1.5rem;
            text-align: center;
        }

        .demo-nudge p {
            max-width: 520px;
            margin: 0 auto 1.2rem;
            font-size: 0.9rem;
            color: #555;
            line-height: 1.75;
            text-transform: none;
        }

        .btn-demo {
            display: inline-block;
            border: 2px solid var(--color-primary);
            color: var(--color-primary);
            padding: 0.6rem 1.6rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .btn-demo:hover {
            background: var(--color-primary);
            color: #fff;
        }

        /* FAQ */
        .faq {
            max-width: 760px;
            margin: 0 auto;
            padding: 4rem 1.5rem;
        }

        .faq-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .faq-item {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .faq-item summary {
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-primary);
            padding: 1.1rem 1.5rem;
            cursor: pointer;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-item summary::-webkit-details-marker { display: none; }

        .faq-item summary::after {
            content: '+';
            font-size: 1.2rem;
            font-weight: normal;
            flex-shrink: 0;
            margin-left: 1rem;
        }

        .faq-item[open] summary::after { content: '−'; }

        .faq-item p {
            font-size: 0.85rem;
            line-height: 1.7;
            color: #444;
            text-transform: none;
            margin: 0;
            padding: 0 1.5rem 1.2rem;
        }

        /* Feedback / community section */
        .feedback-section {
            background: #fff;
            border-top: 1px solid #ebebeb;
            border-bottom: 1px solid #ebebeb;
            padding: 3.5rem 1.5rem;
            text-align: center;
        }

        .feedback-section p {
            max-width: 540px;
            margin: 0 auto 1.5rem;
            font-size: 0.9rem;
            color: #555;
            line-height: 1.75;
            text-transform: none;
        }

        .feedback-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
        }

        .btn-feedback-email {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 2px solid var(--color-primary);
            color: var(--color-primary);
            padding: 0.6rem 1.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .btn-feedback-email:hover {
            background: var(--color-primary);
            color: #fff;
        }

        .btn-feedback-discord {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 2px solid #5865F2;
            color: #5865F2;
            padding: 0.6rem 1.4rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .btn-feedback-discord:hover {
            background: #5865F2;
            color: #fff;
        }

        /* CTA banner */
        .cta-banner {
            background: var(--color-primary);
            color: #fff;
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .cta-banner h2 {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 1.2rem;
        }

        /* Footer */
        .home-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.875rem;
            color: #666;
            text-transform: none;
            background: #f9f9f9;
        }
    </style>
@endsection

@section('content')
    <!-- Hero -->
    <section class="hero" aria-labelledby="hero-heading">
        <h1 id="hero-heading">Design Your MySQL or Postgres Database Schema — Visually</h1>
        <p>
            SQL Designer is a free web ERD tool and schema designer for MySQL and PostgreSQL.
            Drag tables onto a canvas, define columns and relationships,
            then export SQL script.
        </p>
        <div class="hero-actions">
            <a id="hero-btn-authed" class="btn-hero-primary" href="/diagrams" style="display:none">Open My Diagrams</a>
            <a id="hero-btn-register" class="btn-hero-primary" href="/register">Create an account</a>
            <a id="hero-btn-login" class="btn-hero-secondary" href="/login">Log In</a>
        </div>
        <script>
            if (localStorage.getItem('auth_token')) {
                document.getElementById('hero-btn-authed').style.display = 'inline-block';
                document.getElementById('hero-btn-register').style.display = 'none';
                document.getElementById('hero-btn-login').style.display = 'none';
            }
        </script>
    </section>

    <!-- Screenshot -->
    <div class="screenshot-section">
        <div class="screenshot-wrapper">
            <picture>
                <source
                    type="image/webp"
                    srcset="{{ asset('images/screenshot-600.webp') }} 600w, {{ asset('images/screenshot-750.webp') }} 750w, {{ asset('images/screenshot-1000.webp') }} 1000w, {{ asset('images/screenshot.webp') }} 2557w"
                    sizes="(max-width: 660px) calc(100vw - 3rem), 2000px">
                <img src="{{ asset('images/screenshot.png') }}" alt="SQL Designer diagram editor — tables with columns and foreign key relationships on a visual canvas" width="2557" height="1269" loading="eager" fetchpriority="high">
            </picture>
        </div>
    </div>

    <!-- Features -->
    <section class="features" aria-labelledby="features-heading">
        <h2 class="section-title" id="features-heading">Features</h2>
        <ul class="features-list">
            <li>Drag-and-drop canvas</li>
            <li>MySQL &amp; PostgreSQL SQL export</li>
            <li>Foreign key relationships</li>
            <li>PRIMARY KEY, UNIQUE, NOT NULL constraints</li>
            <li>Multiple diagrams per account</li>
            <li>Auto-save</li>
            <li>Crow's foot (ERD) notation</li>
            <li>Completely free, no limits</li>
        </ul>
        <p class="features-more"><a href="/features">See full feature list &rarr;</a></p>
    </section>

    <!-- Demo nudge -->
    <div class="demo-nudge" id="demo-nudge-guest">
        <p>Not sure if it's worth signing up? Try the demo first — no account needed. Just the editor, with a sample schema preloaded so you can poke around and see if the interface works for you.</p>
        <a class="btn-demo" href="/demo">Try the demo</a>
    </div>
    <script>
        if (localStorage.getItem('auth_token')) {
            document.getElementById('demo-nudge-guest').style.display = 'none';
        }
    </script>

    <!-- FAQ -->
    <section class="faq" aria-labelledby="faq-heading">
        <h2 class="section-title" id="faq-heading">Frequently Asked Questions</h2>
        <ul class="faq-list">
            <li class="faq-item">
                <details>
                    <summary>Is SQL Designer free?</summary>
                    <p>Yes, completely free. There is no subscription, no credit card required, and no hidden fees. Create an account with your email and start designing immediately.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>Do I need to install anything?</summary>
                    <p>No. SQL Designer runs entirely in your browser. There is nothing to download or install — just open the site and start designing.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>What SQL does it generate?</summary>
                    <p>SQL Designer generates MySQL and PostgreSQL <code>CREATE TABLE</code> scripts, including column definitions, data types, constraints (PRIMARY KEY, UNIQUE, NOT NULL), and foreign key relationships.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>Do I need to know SQL to use it?</summary>
                    <p>No. The visual interface lets you build your schema by clicking and dragging — no SQL knowledge required. The SQL is generated for you automatically.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>Is my work saved automatically?</summary>
                    <p>Yes. Changes to your diagram are saved automatically to your account. You can close the browser and pick up where you left off at any time.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>How many diagrams can I create?</summary>
                    <p>There is no limit. Create as many diagrams as you need — one per project, service, or database.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>Is SQL Designer a free MySQL Workbench alternative?</summary>
                    <p>Yes. SQL Designer is a free, browser-based alternative to MySQL Workbench for schema design. Unlike MySQL Workbench, it requires no download or installation — you design your schema visually in the browser and export a <code>CREATE TABLE</code> script in seconds.</p>
                </details>
            </li>
            <li class="faq-item">
                <details>
                    <summary>How does SQL Designer compare to dbdiagram.io?</summary>
                    <p>Both tools let you design database schemas visually and export SQL. SQL Designer uses a drag-and-drop canvas with a visual ERD layout, supports both MySQL and PostgreSQL, and is completely free with no diagram limits. dbdiagram.io uses a DSL (text-based) input rather than a purely visual editor.</p>
                </details>
            </li>
        </ul>
    </section>

    <!-- Blog -->
    <section style="background:#f0f0f0; padding:4rem 1.5rem;" aria-labelledby="blog-heading">
        <div style="max-width:960px; margin:0 auto;">
            <h2 class="section-title" id="blog-heading">From the Blog</h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1.5rem;">
                <a href="/blog/how-to-design-mysql-database-schema" style="background:#fff; border-radius:6px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); text-decoration:none; display:block;">
                    <p style="font-size:0.875rem; color:#666; text-transform:none; margin:0 0 0.5rem;">Schema Design</p>
                    <h3 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--color-primary); margin:0 0 0.5rem;">How to Design a MySQL Database Schema</h3>
                    <p style="font-size:0.875rem; color:#555; text-transform:none; line-height:1.7; margin:0;">A step-by-step guide covering entities, data types, primary keys, foreign keys, and normalization.</p>
                </a>
                <a href="/blog/er-diagram-tool-online" style="background:#fff; border-radius:6px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); text-decoration:none; display:block;">
                    <p style="font-size:0.875rem; color:#666; text-transform:none; margin:0 0 0.5rem;">ER Diagrams</p>
                    <h3 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--color-primary); margin:0 0 0.5rem;">Free ER Diagram Tool Online for MySQL</h3>
                    <p style="font-size:0.875rem; color:#555; text-transform:none; line-height:1.7; margin:0;">What ER diagrams are, why they matter, and how to build one visually in your browser.</p>
                </a>
                <a href="/blog/mysql-workbench-alternative" style="background:#fff; border-radius:6px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); text-decoration:none; display:block;">
                    <p style="font-size:0.875rem; color:#666; text-transform:none; margin:0 0 0.5rem;">Tools</p>
                    <h3 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.04em; color:var(--color-primary); margin:0 0 0.5rem;">MySQL Workbench Alternative Online</h3>
                    <p style="font-size:0.875rem; color:#555; text-transform:none; line-height:1.7; margin:0;">Heavy desktop tool not cutting it? Here are the best browser-based alternatives.</p>
                </a>
            </div>
            <p style="text-align:center; margin:2rem 0 0;"><a href="/blog" style="color:var(--color-primary-hover); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">View All Posts &rarr;</a></p>
        </div>
    </section>

    <!-- Feedback / Community -->
    <section class="feedback-section" aria-labelledby="feedback-heading">
        <h2 class="section-title" id="feedback-heading">Share Your Feedback</h2>
        <p>
            Hi, it's Dmitriy, the creator of SQL Designer. If you have suggestions, bug reports, or just want to say hi —
            I'd love to hear from you. Reach out by email or join the Discord community.
        </p>
        <div class="feedback-links">
            <a class="btn-feedback-email" href="mailto:snydi611@gmail.com">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/></svg>
                snydi611@gmail.com
            </a>
            <a class="btn-feedback-discord" href="https://discord.gg/vFwgX7qKqA" target="_blank" rel="noopener noreferrer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                Join Discord
            </a>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-banner" aria-labelledby="cta-heading">
        <h2 id="cta-heading">Start Designing Your Database Schema Today</h2>
        <a id="cta-btn-authed" class="btn-hero-primary" href="/diagrams" style="display:none">Go to My Diagrams</a>
        <a id="cta-btn-guest" class="btn-hero-primary" href="/register">Get Started</a>
        <script>
            if (localStorage.getItem('auth_token')) {
                document.getElementById('cta-btn-authed').style.display = 'inline-block';
                document.getElementById('cta-btn-guest').style.display = 'none';
            }
        </script>
    </section>
@endsection
