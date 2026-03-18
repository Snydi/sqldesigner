@extends('layouts.home')

@section('title', 'SQL Designer — Free Online MySQL Database Schema Designer')

@section('head')
    <meta name="description" content="Free online MySQL database schema designer. Create tables, define relationships, and export SQL scripts with a visual drag-and-drop interface. No installation required.">
    <meta name="keywords" content="MySQL schema designer, database diagram tool, SQL schema visualizer, ER diagram, entity relationship diagram, database design tool, free database designer, MySQL workbench alternative">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SQL Designer">
    <meta name="theme-color" content="#c05252">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:title" content="SQL Designer — Free Online MySQL Database Schema Designer">
    <meta property="og:description" content="Visually design and export MySQL database schemas with a drag-and-drop interface. Free, fast, and browser-based.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sql-designer.com/">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer — Free MySQL Schema Designer">
    <meta name="twitter:description" content="Design, visualize, and export your MySQL database schemas online for free.">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <link rel="canonical" href="https://sql-designer.com/">
    <script type="application/ld+json">
    [
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "SQL Designer",
            "url": "https://sql-designer.com",
            "description": "Free online MySQL database schema designer with a visual drag-and-drop interface and SQL export.",
            "applicationCategory": "DeveloperApplication",
            "operatingSystem": "Any",
            "screenshot": "https://sql-designer.com/images/screenshot.png",
            "featureList": [
                "Visual drag-and-drop schema editor",
                "MySQL CREATE TABLE SQL export",
                "Foreign key relationship modelling",
                "PRIMARY KEY, UNIQUE, NOT NULL constraints",
                "Multiple diagrams per account",
                "Auto-save"
            ],
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "USD"
            }
        },
        {
            "@context": "https://schema.org",
            "@type": "HowTo",
            "name": "How to design a MySQL database schema with SQL Designer",
            "description": "Step-by-step guide to creating a visual MySQL database schema and exporting a SQL script.",
            "step": [
                {
                    "@type": "HowToStep",
                    "position": 1,
                    "name": "Create a Diagram",
                    "text": "Sign up for free and create a new diagram for your project or database."
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
                    "text": "Click export to generate a clean MySQL CREATE TABLE script ready to run in your database."
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
                        "text": "SQL Designer generates MySQL CREATE TABLE scripts, including column definitions, data types, constraints (PRIMARY KEY, UNIQUE, NOT NULL), and foreign key relationships."
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
                }
            ]
        }
    ]
    </script>
    <style>
        body { overflow-y: auto; margin: 0; }

        .home-page {
            font-family: 'JetBrains Mono', monospace;
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
            font-size: 0.8rem;
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

        /* Features */
        .features {
            max-width: 960px;
            margin: 0 auto;
            padding: 4rem 1.5rem;
        }

        .section-title {
            text-align: center;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-primary);
            margin: 0 0 2.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: #fff;
            border-radius: 6px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            text-align: left;
        }

        .feature-card h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-primary);
            margin: 0 0 0.6rem;
        }

        .feature-card p {
            font-size: 0.85rem;
            line-height: 1.7;
            margin: 0;
            text-transform: none;
            color: #444;
        }

        /* How it works */
        .how-it-works {
            background: #fff;
            padding: 4rem 1.5rem;
        }

        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            max-width: 860px;
            margin: 0 auto;
        }

        .step {
            flex: 1 1 200px;
            max-width: 240px;
            text-align: center;
        }

        .step-number {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            line-height: 2.5rem;
            border-radius: 50%;
            background: var(--color-primary);
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 0.8rem;
        }

        .step h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 0.4rem;
        }

        .step p {
            font-size: 0.82rem;
            color: #555;
            text-transform: none;
            line-height: 1.6;
            margin: 0;
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
            font-size: 0.75rem;
            color: #999;
            text-transform: none;
            background: #f9f9f9;
        }
    </style>
@endsection

@section('content')
    <!-- Hero -->
    <section class="hero" aria-labelledby="hero-heading">
        <div class="hero-free-badge">100% Free &mdash; No subscription, no credit card</div>
        <h1 id="hero-heading">Design Your MySQL Database Schema — Visually</h1>
        <p>
            SQL Designer is a free, browser-based database schema designer.
            Drag and drop tables, define columns and relationships,
            then export a ready-to-run SQL script in seconds.
        </p>
        <p>
            Just create a free account and start designing immediately.
            No payment, no trial period, no limits.
        </p>
        <div class="hero-actions">
            <a id="hero-btn-authed" class="btn-hero-primary" href="/diagrams" style="display:none">Open My Diagrams</a>
            <a id="hero-btn-register" class="btn-hero-primary" href="/register" style="display:none">Create Free Account</a>
            <a id="hero-btn-login" class="btn-hero-secondary" href="/login" style="display:none">Log In</a>
        </div>
        <script>
            if (localStorage.getItem('auth_token')) {
                document.getElementById('hero-btn-authed').style.display = 'inline-block';
            } else {
                document.getElementById('hero-btn-register').style.display = 'inline-block';
                document.getElementById('hero-btn-login').style.display = 'inline-block';
            }
        </script>
    </section>

    <!-- Screenshot -->
    <section class="screenshot-section">
        <div class="screenshot-wrapper">
            <img src="{{ asset('images/screenshot.png') }}" alt="SQL Designer diagram editor — tables with columns and foreign key relationships on a visual canvas" width="2556" height="1271" loading="lazy">
        </div>
    </section>

    <!-- Features -->
    <section class="features" aria-labelledby="features-heading">
        <h2 class="section-title" id="features-heading">What You Can Do</h2>
        <div class="features-grid">
            <article class="feature-card">
                <h3>Visual Schema Editor</h3>
                <p>Add tables, define columns with types and constraints, and connect them with foreign key relationships — all with a drag-and-drop canvas.</p>
            </article>
            <article class="feature-card">
                <h3>SQL Export</h3>
                <p>Generate a valid MySQL <code>CREATE TABLE</code> script from your diagram at any time. Copy it directly into your database client or migration tool.</p>
            </article>
            <article class="feature-card">
                <h3>Multiple Diagrams</h3>
                <p>Organise your work into separate diagrams — one per project, service, or database. All diagrams are saved to your account and accessible anywhere.</p>
            </article>
            <article class="feature-card">
                <h3>Free Forever &mdash; No Credit Card</h3>
                <p>No installation, no subscription, no hidden fees. Create a free account with just your email and start designing immediately.</p>
            </article>
            <article class="feature-card">
                <h3>Relationships &amp; Constraints</h3>
                <p>Model <code>PRIMARY KEY</code>, <code>UNIQUE</code>, <code>NOT NULL</code>, and foreign key relationships visually — no need to write DDL by hand.</p>
            </article>
            <article class="feature-card">
                <h3>Fast Iteration</h3>
                <p>Changes are saved automatically. Rename a column, add a table, or restructure relationships in seconds without losing your work.</p>
            </article>
        </div>
    </section>

    <!-- How it works -->
    <section class="how-it-works" aria-labelledby="how-heading">
        <h2 class="section-title" id="how-heading">How It Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Create a Diagram</h3>
                <p>Sign up for free and create a new diagram for your project or database.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Add Tables &amp; Columns</h3>
                <p>Drag tables onto the canvas, add columns, choose data types, and set constraints.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Draw Relationships</h3>
                <p>Connect tables with foreign key lines to define your relational structure.</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Export SQL</h3>
                <p>Click export to generate a clean MySQL script ready to run in your database.</p>
            </div>
        </div>
    </section>

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
                    <p>SQL Designer generates MySQL <code>CREATE TABLE</code> scripts, including column definitions, data types, constraints (PRIMARY KEY, UNIQUE, NOT NULL), and foreign key relationships.</p>
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
        </ul>
    </section>

    <!-- CTA -->
    <section class="cta-banner" aria-labelledby="cta-heading">
        <h2 id="cta-heading">Start Designing Your Database Schema Today</h2>
        <p style="opacity:0.85; font-size:0.9rem; text-transform:none; margin: 0 auto 1.5rem; max-width:480px; line-height:1.6;">
            No subscription. No credit card. Just register with your email and you're in.
        </p>
        <a id="cta-btn-authed" class="btn-hero-primary" href="/diagrams" style="display:none">Go to My Diagrams</a>
        <a id="cta-btn-guest" class="btn-hero-primary" href="/register" style="display:none">Create a Free Account</a>
        <script>
            if (localStorage.getItem('auth_token')) {
                document.getElementById('cta-btn-authed').style.display = 'inline-block';
            } else {
                document.getElementById('cta-btn-guest').style.display = 'inline-block';
            }
        </script>
    </section>
@endsection
