@extends('layouts.main')

@section('title', 'Database Schema Library — MySQL & PostgreSQL Schema Examples')

@section('head')
    <meta name="description" content="Browse real MySQL and PostgreSQL database schema examples shared by the SQL Designer community. Find inspiration or share your own diagram.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/library">
    <meta property="og:title" content="Database Schema Library — Real MySQL &amp; PostgreSQL Schema Examples | SQL Designer">
    <meta property="og:description" content="Browse real MySQL and PostgreSQL database schema examples shared by the SQL Designer community. Find inspiration or share your own diagram.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="https://sql-designer.com/library">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — database schema library with real MySQL and PostgreSQL examples">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Database Schema Library — Real MySQL &amp; PostgreSQL Schema Examples | SQL Designer">
    <meta name="twitter:description" content="Browse real MySQL and PostgreSQL database schema examples shared by the SQL Designer community. Find inspiration or share your own diagram.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <script type="application/ld+json">
    @verbatim
    [
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home",           "item": "https://sql-designer.com/" },
            { "@type": "ListItem", "position": 2, "name": "Schema Library", "item": "https://sql-designer.com/library" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "SQL Designer",
        "url": "https://sql-designer.com",
        "applicationCategory": "DeveloperApplication",
        "operatingSystem": "Any",
        "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
        "sameAs": [
            "https://gitlab.com/Snydi/sql-designer",
            "https://discord.gg/vFwgX7qKqA"
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Database Schema Library — SQL Designer",
        "url": "https://sql-designer.com/library",
        "dateModified": "2026-04-27",
        "description": "Browse real MySQL and PostgreSQL database schema examples shared by the SQL Designer community. Find inspiration or share your own diagram.",
        "isPartOf": { "@type": "WebSite", "name": "SQL Designer", "url": "https://sql-designer.com" },
        "about": { "@type": "SoftwareApplication", "name": "SQL Designer", "url": "https://sql-designer.com" }
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
                "name": "What is the SQL Designer schema library?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "The SQL Designer schema library is a public collection of MySQL and PostgreSQL database diagrams shared by the SQL Designer community. Browse real-world schema examples for blogs, e-commerce platforms, SaaS applications, analytics systems, and more."
                }
            },
            {
                "@type": "Question",
                "name": "How do I add my schema to the library?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Open the share dialog in the SQL Designer diagram editor and tick 'Share in library'. Your diagram will appear in the Shared by users section of the library, publicly viewable by anyone."
                }
            },
            {
                "@type": "Question",
                "name": "How do I get my schema featured in the library?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Embed your diagram on your own site using the iframe embed code from the share dialog, then contact SQL Designer. Schemas with a live backlink from an external site are eligible for the Featured row, which includes a dofollow backlink back to your site."
                }
            },
            {
                "@type": "Question",
                "name": "Are the schemas in the library free to view?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. All schemas in the SQL Designer library are publicly viewable without an account. You can browse, open, and inspect any shared diagram for free."
                }
            },
            {
                "@type": "Question",
                "name": "Can I use a schema from the library in my own project?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Open any shared diagram from the library to view its full interactive ERD. You can use it as a reference or copy the generated SQL export to bootstrap your own project."
                }
            }
        ]
    }
    @endverbatim
    </script>
    <style>
        :root {
            --maxw: 1120px;
            --gutter: clamp(1.25rem, 4vw, 2.5rem);
        }

        body { overflow-y: auto; }

        /* ── Page intro ── */
        .page-intro {
            padding: clamp(2.5rem, 5vw, 4rem) var(--gutter) clamp(1.5rem, 3vw, 2.5rem);
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
            max-width: var(--maxw);
            margin: 0 auto;
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            align-items: end;
            justify-content: space-between;
        }
        .breadcrumb {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 1rem;
        }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--color-primary-text, #5db583); }
        .breadcrumb .sep { margin: 0 0.4rem; color: var(--border-strong); }
        h1.page-h1 {
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            line-height: 1.1;
            letter-spacing: -0.025em;
            font-weight: 600;
            margin: 0 0 0.7rem;
            text-wrap: balance;
        }
        h1.page-h1 em { font-style: normal; color: var(--color-primary-text, #5db583); }
        .page-sub { font-size: 1rem; color: var(--text-secondary); margin: 0; max-width: 50ch; }
        .intro-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 44px;
            padding: 0.55rem 0.95rem;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            line-height: 1;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 120ms, border-color 120ms, color 120ms;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-outline {
            color: var(--text-primary);
            border-color: var(--border-strong);
        }
        .btn-outline:hover { border-color: var(--text-primary); }
        .btn-solid {
            background: var(--color-primary-text, #5db583);
            color: #0c1f15;
        }
        .btn-solid:hover { background: #6dc290; }
        .btn-lg { min-height: 48px; padding: 0.75rem 1.15rem; font-size: 1rem; border-radius: 7px; }

        /* ── Filter bar ── */
        .filterbar {
            border-bottom: 1px solid var(--border-light);
            padding: 0.9rem var(--gutter);
            position: sticky;
            top: 56px;
            z-index: 40;
            background: rgba(31,31,31,0.97);
        }
        .filterbar-inner {
            max-width: var(--maxw);
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
        }
        .filterbar-inner .label { color: var(--text-muted); margin-right: 0.3rem; }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 44px;
            padding: 0.35rem 0.7rem;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            background: var(--bg-surface);
            color: var(--text-secondary);
            font-family: inherit;
            font-size: 1rem;
            cursor: pointer;
            transition: border-color 120ms, color 120ms, background 120ms;
        }
        .chip:hover { color: var(--text-primary); border-color: var(--border-strong); }
        .chip[aria-pressed="true"] {
            background: rgba(93,181,131,0.1);
            border-color: var(--color-primary-text, #5db583);
            color: var(--color-primary-text, #5db583);
        }
        .chip[aria-current="page"] {
            background: rgba(93,181,131,0.1);
            border-color: var(--color-primary-text, #5db583);
            color: var(--color-primary-text, #5db583);
        }
        .chip .count { color: var(--text-muted); font-size: 1rem; }

        /* ── Sections ── */
        .lib-section {
            padding: clamp(2rem, 4vw, 3rem) var(--gutter);
        }
        .lib-section + .lib-section { padding-top: 0; }
        .lib-section-inner {
            max-width: var(--maxw);
            margin: 0 auto;
        }
        .section-head {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin: 0 0 0.4rem;
        }
        h2.section-h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            font-weight: 600;
            margin: 0;
        }
        .section-pill {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.18rem 0.5rem;
            border-radius: 3px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }
        .section-pill.featured {
            background: rgba(201,168,106,0.1);
            border-color: rgba(201,168,106,0.4);
            color: var(--accent-fk, #c9a86a);
        }
        .section-desc {
            font-size: 1rem;
            color: var(--text-secondary);
            margin: 0 0 1.4rem;
            max-width: 60ch;
        }
        .section-desc em { font-style: italic; }

        /* ── Grid ── */
        .lib-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 860px) { .lib-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .lib-grid { grid-template-columns: 1fr; } }

        /* ── Cards ── */
        .lib-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: var(--bg-surface);
            color: inherit;
            text-decoration: none;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: border-color 120ms, transform 120ms, box-shadow 120ms;
        }
        .lib-card:hover {
            border-color: var(--color-primary-text, #5db583);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -15px rgba(0,0,0,0.6);
        }
        .lib-card-preview {
            display: block;
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: radial-gradient(circle at 30% 0%, rgba(93,181,131,0.05), transparent 50%), var(--bg-page);
            border-bottom: 1px solid var(--border-color);
        }
        .lib-card-preview::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, #2e2e2e 1px, transparent 1px);
            background-size: 18px 18px;
            opacity: 0.5;
            pointer-events: none;
        }
        .lib-card-preview iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 300%;
            height: 300%;
            border: none;
            transform: scale(calc(1 / 3));
            transform-origin: top left;
            pointer-events: none;
        }
        .lib-card-body {
            padding: 0.85rem 1rem 0.95rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .lib-card-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.005em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-decoration: none;
        }
        .lib-card-name:hover { color: var(--color-primary-text, #5db583); }
        .lib-card-meta {
            display: flex;
            gap: 0.6rem;
            align-items: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
        }
        .lib-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.2rem;
        }
        .like-button {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex: 0 0 auto;
            min-height: 44px;
            padding: 0.25rem 0.45rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: transparent;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            line-height: 1;
            cursor: pointer;
            transition: border-color 120ms, color 120ms, background 120ms;
        }
        .like-button:hover {
            border-color: var(--border-strong);
            color: var(--text-primary);
        }
        .like-button[aria-pressed="true"] {
            border-color: rgba(93,181,131,0.45);
            background: rgba(93,181,131,0.1);
            color: var(--color-primary-text, #5db583);
        }
        .like-button:disabled { opacity: 0.55; cursor: wait; }
        .like-button svg { width: 14px; height: 14px; }
        .like-button[aria-pressed="true"] svg { fill: currentColor; }
        .lib-card-backlink {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--accent-fk, #c9a86a);
            text-decoration: none;
            margin-top: 0.2rem;
        }
        .lib-card-backlink:hover { text-decoration: underline; }

        /* ── Empty state ── */
        .lib-empty {
            border: 1px dashed var(--border-color);
            border-radius: 10px;
            padding: 2.5rem 2rem;
            text-align: center;
            color: var(--text-muted);
            background: var(--bg-surface);
        }
        .lib-empty .glyph {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.4rem;
            color: var(--border-strong);
            margin-bottom: 0.6rem;
        }
        .lib-empty p { margin: 0 0 0.4rem; font-size: 1rem; color: var(--text-secondary); }
        .lib-empty .hint { font-size: 1rem; color: var(--text-muted); }

        /* ── Submit strip ── */
        .submit-strip {
            padding: clamp(2.5rem, 5vw, 3.5rem) var(--gutter);
            border-top: 1px solid var(--border-color);
        }
        .submit-strip-inner {
            max-width: var(--maxw);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: clamp(1.5rem, 4vw, 3rem);
            align-items: center;
        }
        @media (max-width: 720px) { .submit-strip-inner { grid-template-columns: 1fr; } }
        .submit-strip .section-eyebrow { margin-bottom: 0.4rem; }
        .submit-strip h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            margin: 0 0 0.5rem;
        }
        .submit-strip p { color: var(--text-secondary); margin: 0 0 1rem; max-width: 50ch; }
        .submit-code {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.9rem 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-secondary);
            white-space: pre;
            overflow-x: auto;
        }
        .submit-code .tok-tag { color: var(--color-primary-text, #5db583); }
        .submit-code .tok-attr { color: var(--accent-fk, #c9a86a); }
        .submit-code .tok-str { color: #b8b8b8; }
        .submit-code .tok-com { color: var(--text-muted); }

        /* ── Pagination ── */
        .lib-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 1.5rem 0 0.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
        }
        .lib-pagination a,
        .lib-pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 44px;
            padding: 0 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-secondary);
            text-decoration: none;
            background: var(--bg-surface);
            transition: border-color 120ms, color 120ms;
        }
        .lib-pagination a:hover { border-color: var(--border-strong); color: var(--text-primary); }
        .lib-pagination span[aria-current="page"] {
            background: rgba(93,181,131,0.1);
            border-color: var(--color-primary-text, #5db583);
            color: var(--color-primary-text, #5db583);
        }
        .lib-pagination span.disabled {
            opacity: 0.35;
            cursor: default;
        }
        .lib-pagination span.dots { border: none; background: none; }
    </style>
@endsection

@section('content')

<!-- PAGE INTRO -->
<section class="page-intro">
    <div class="intro-inner">
        <div>
            <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><span>Library</span></p>
            <h1 class="page-h1">Real schemas, <em>shared</em> by the community.</h1>
            <p class="page-sub">Browse MySQL and PostgreSQL examples — blogs, e-commerce, SaaS, analytics. Built with <a href="/features" style="color:var(--color-primary-text,#5db583)">SQL Designer</a>.</p>
        </div>
    </div>
</section>

<!-- SORTING -->
<nav class="filterbar" aria-label="Library sorting">
    <div class="filterbar-inner">
        <span class="label">Sort community schemas:</span>
        <a class="chip" href="/library" @if($sort === 'likes') aria-current="page" @endif>Most liked</a>
        <a class="chip" href="/library?sort=newest" @if($sort === 'newest') aria-current="page" @endif>Newest</a>
    </div>
</nav>

<!-- FEATURED -->
<section class="lib-section">
    <div class="lib-section-inner">
        <div class="section-head">
            <h2 class="section-h2">Featured schemas</h2>
        </div>
        <p class="section-desc">
            Two ways for you diagram to appear here: <br>
            1. Use the <em>embed</em> code option in the share dialog, then <a href="mailto:dmitriy@sql-designer.com" style="color: var(--color-primary-text); text-decoration: underline;">contact me</a> and I'll feature your schema here with a DO FOLLOW backlink, leading to your site. <br>
            2. I just like your diagram and place it here myself, because I think it deserves the spot.
        </p>
        @if($featured->isEmpty())
            <div class="lib-empty">
                <div class="glyph">★</div>
                <p>No featured schemas yet.</p>
                <p class="hint">Embed your diagram on your site to get featured here.</p>
            </div>
        @else
            <div class="lib-grid">
                @foreach($featured as $diagram)
                    <div class="lib-card">
                        <a href="/diagrams/{{ $diagram->share_token }}" target="_blank" rel="noopener noreferrer" class="lib-card-preview">
                            <iframe
                                data-src="/embed/{{ $diagram->share_token }}"
                                title="{{ $diagram->name }} preview"
                                tabindex="-1"
                            ></iframe>
                        </a>
                        <div class="lib-card-body">
                            <a class="lib-card-name" href="/diagrams/{{ $diagram->share_token }}" target="_blank" rel="noopener noreferrer">{{ $diagram->name }}</a>
                            <div class="lib-card-footer">
                                @if($diagram->featured_url)
                                    <a class="lib-card-backlink" href="{{ $diagram->featured_url }}" target="_blank" rel="dofollow noopener noreferrer">
                                        ↗ {{ parse_url($diagram->featured_url, PHP_URL_HOST) }}
                                    </a>
                                @else
                                    <span></span>
                                @endif
                                <button
                                    type="button"
                                    class="like-button"
                                    data-diagram-id="{{ $diagram->id }}"
                                    aria-pressed="false"
                                    aria-label="Like {{ $diagram->name }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
                                    <span data-like-count>{{ $diagram->likes_count }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- SHARED BY USERS -->
<section class="lib-section">
    <div class="lib-section-inner">
        <div class="section-head">
            <h2 class="section-h2">Shared by users</h2>
        </div>
        <p class="section-desc">Public schemas anyone can browse. To add yours, tick <em>"Share in library"</em> inside share dialog in designer.</p>

        @if($diagrams->isEmpty())
            <div class="lib-empty">
                <div class="glyph">◈</div>
                <p>No public schemas yet.</p>
                <p class="hint">Share a diagram and opt in to the library to be first.</p>
            </div>
        @else
            <div class="lib-grid">
                @foreach($diagrams as $diagram)
                    <div class="lib-card">
                        <a class="lib-card-preview" href="/diagrams/{{ $diagram->share_token }}" target="_blank" rel="noopener noreferrer">
                            <iframe
                                data-src="/embed/{{ $diagram->share_token }}"
                                title="{{ $diagram->name }} preview"
                                tabindex="-1"
                            ></iframe>
                        </a>
                        <div class="lib-card-body">
                            <a class="lib-card-name" href="/diagrams/{{ $diagram->share_token }}" target="_blank" rel="noopener noreferrer">{{ $diagram->name }}</a>
                            <div class="lib-card-footer">
                                <div class="lib-card-meta">
                                    <span>Added {{ $diagram->created_at->diffForHumans() }}</span>
                                </div>
                                <button
                                    type="button"
                                    class="like-button"
                                    data-diagram-id="{{ $diagram->id }}"
                                    aria-pressed="false"
                                    aria-label="Like {{ $diagram->name }}"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"/></svg>
                                    <span data-like-count>{{ $diagram->likes_count }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        {{ $diagrams->links('components.pagination', ['navClass' => 'lib-pagination']) }}
    @endif
    </div>
</section>

<!-- SUBMIT STRIP -->
<section class="submit-strip">
    <div class="submit-strip-inner">
        <div>
            <h2>Embed your diagram, get featured.</h2>
            <p>Drop a public diagram into your blog post or docs site with the embed snippet. Diagrams with a live backlink land in the Featured row.</p>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                <a class="btn btn-solid" href="/diagrams/new">Make a diagram</a>
            </div>
        </div>
        <pre class="submit-code"><span class="tok-com">&lt;!-- paste anywhere --&gt;</span>
<span class="tok-tag">&lt;iframe</span>
  <span class="tok-attr">src</span>=<span class="tok-str">"https://sql-designer.com/embed/&lt;token&gt;"</span>
  <span class="tok-attr">width</span>=<span class="tok-str">"100%"</span>
  <span class="tok-attr">height</span>=<span class="tok-str">"480"</span>
  <span class="tok-attr">frameborder</span>=<span class="tok-str">"0"</span><span class="tok-tag">&gt;&lt;/iframe&gt;</span></pre>
    </div>
</section>

<script>
    // Lazy-load iframe previews as they scroll into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const iframe = entry.target;
                if (iframe.dataset.src) iframe.src = iframe.dataset.src;
                observer.unobserve(iframe);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('.lib-card-preview iframe[data-src]').forEach(el => observer.observe(el));

    const authToken = localStorage.getItem('auth_token');
    const likeButtons = document.querySelectorAll('.like-button');

    const setLiked = (button, liked) => {
        button.setAttribute('aria-pressed', liked ? 'true' : 'false');
        button.setAttribute('aria-label', `${liked ? 'Unlike' : 'Like'} this diagram`);
    };

    if (authToken && likeButtons.length) {
        fetch('/api/library/likes', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${authToken}`,
            },
        }).then(response => response.ok ? response.json() : null)
            .then(data => {
                const likedIds = new Set((data?.diagram_ids ?? []).map(String));
                likeButtons.forEach(button => setLiked(button, likedIds.has(button.dataset.diagramId)));
            });
    }

    likeButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const liked = button.getAttribute('aria-pressed') === 'true';
            button.disabled = true;

            try {
                const response = await fetch(`/api/library/diagrams/${button.dataset.diagramId}/like`, {
                    method: liked ? 'DELETE' : 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                    },
                });

                if (response.status === 401) {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                    return;
                }
                if (!response.ok) return;

                const data = await response.json();
                setLiked(button, data.liked);
                button.querySelector('[data-like-count]').textContent = data.likes_count;
            } finally {
                button.disabled = false;
            }
        });
    });
</script>

@endsection
