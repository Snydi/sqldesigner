@extends('layouts.main')

@section('title', 'How to Draw an ER Diagram Step by Step')

@section('head')
    <meta name="description" content="A step-by-step guide to drawing ER diagrams: entities, attributes, relationships, cardinality notation, and practical tips for MySQL.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/how-to-draw-er-diagram">
    <meta property="og:title" content="How to Draw an ER Diagram Step by Step">
    <meta property="og:description" content="A step-by-step guide to drawing entity-relationship (ER) diagrams for MySQL databases, including notation, cardinality, and practical tips.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/how-to-draw-er-diagram">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="How to Draw an ER Diagram Step by Step">
    <meta name="twitter:description" content="A step-by-step guide to drawing ER diagrams: entities, attributes, relationships, cardinality notation, and practical tips for MySQL.">
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
            { "@type": "ListItem", "position": 3, "name": "How to Draw an ER Diagram Step by Step", "item": "https://sql-designer.com/blog/how-to-draw-er-diagram" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "TechArticle",
        "headline": "How to Draw an ER Diagram Step by Step",
        "description": "A step-by-step guide to drawing entity-relationship (ER) diagrams for MySQL databases.",
        "image": "https://sql-designer.com/images/designer_screenshot.png",
        "url": "https://sql-designer.com/blog/how-to-draw-er-diagram",
        "datePublished": "2026-03-19",
        "dateModified": "2026-03-24",
        "author": { "@type": "Organization", "name": "SQL Designer" },
        "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
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
        .article-body p { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin: 0 0 1rem; text-wrap: pretty; }
        .article-body ul, .article-body ol { margin: 0 0 1rem 1.5rem; padding: 0; }
        .article-body li { font-size: 0.93rem; color: var(--text-secondary); line-height: 1.75; margin-bottom: 0.35rem; }
        .article-body code { background: var(--bg-elevated); padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.83em; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }
        .article-body pre { background: #181f2e; color: #e2e8f0; border-radius: 8px; padding: 1.2rem 1.5rem; overflow-x: auto; margin: 1rem 0 1.5rem; font-size: 0.875rem; line-height: 1.65; border: 1px solid var(--border-color); }
        .article-body pre code { background: none; padding: 0; color: inherit; font-size: inherit; font-family: 'JetBrains Mono', monospace; }
        .article-body a { color: var(--color-primary-text); }
        .article-body strong { color: var(--text-primary); }
        .step-block { background: var(--bg-surface); border-radius: 8px; border-left: 3px solid var(--color-primary); padding: 1.1rem 1.4rem; margin-bottom: 1rem; }
        .step-block h3 { font-size: 0.88rem; font-weight: 600; color: var(--color-primary-text); margin: 0 0 0.4rem; text-transform: none; letter-spacing: normal; }
        .step-block p { margin: 0; font-size: 0.88rem; }

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
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>ER Diagrams</span></p>
        <p class="post-eyebrow">March 2026 · 6 min read</p>
        <h1 class="page-h1">How to Draw an ER Diagram Step by Step</h1>
        <p class="page-sub">An entity-relationship (ER) diagram is the standard way to plan a database before writing any code. Getting the diagram right first saves you from painful schema migrations later. This guide walks through the process from a blank page to a complete ER diagram, using a practical example throughout.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-goes-into-an-er-diagram">What Goes In</a></li>
            <li><a href="#cardinality-notation">Cardinality Notation</a></li>
            <li><a href="#step-by-step-drawing-an-er-diagram">Step-by-Step</a></li>
            <li><a href="#common-er-diagram-mistakes">Common Mistakes</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <h2 id="what-goes-into-an-er-diagram">What Goes into an ER Diagram</h2>
        <p>An ER diagram has three building blocks:</p>
        <ul>
            <li><strong>Entities</strong> — the things you store data about, represented as rectangles. In a database, each entity becomes a table.</li>
            <li><strong>Attributes</strong> — the properties of each entity, shown as columns inside the rectangle. Each attribute has a name and a data type.</li>
            <li><strong>Relationships</strong> — lines connecting entities that share a foreign key reference. The symbols at each end of the line show the cardinality.</li>
        </ul>

        <h2 id="cardinality-notation">Cardinality Notation</h2>
        <p>
            Cardinality describes how many rows in one table can relate to rows in another. The most common notation is crow's foot (also called IE notation), which uses symbols at the end of each relationship line:
        </p>
        <ul>
            <li><strong>One</strong> — a single vertical line <code>|</code></li>
            <li><strong>Many</strong> — a crow's foot (three lines fanning out)</li>
            <li><strong>Zero or one</strong> — a circle and a vertical line</li>
            <li><strong>One or more</strong> — a vertical line and a crow's foot</li>
            <li><strong>Zero or more</strong> — a circle and a crow's foot</li>
        </ul>
        <p>
            For example, a one-to-many relationship between <code>users</code> and <code>orders</code> (one user, many orders) would show a single line on the <code>users</code> side and a crow's foot on the <code>orders</code> side.
        </p>

        <h2 id="step-by-step-drawing-an-er-diagram">Step-by-Step: Drawing an ER Diagram</h2>
        <p>We'll use a simple blog platform as our example: users write posts, and posts have comments.</p>

        <div class="step-block">
            <h3>Step 1 — List your entities</h3>
            <p>Write down the things your system needs to store. For a blog: <strong>User</strong>, <strong>Post</strong>, <strong>Comment</strong>. Each becomes a rectangle in your diagram.</p>
        </div>

        <div class="step-block">
            <h3>Step 2 — Add attributes to each entity</h3>
            <p>For each entity, list the columns you need. Start with the primary key, then add the meaningful attributes:</p>
            <ul style="margin-top:0.5rem;">
                <li><strong>User:</strong> <code>id</code>, <code>email</code>, <code>name</code>, <code>created_at</code></li>
                <li><strong>Post:</strong> <code>id</code>, <code>title</code>, <code>body</code>, <code>published_at</code>, <code>user_id</code></li>
                <li><strong>Comment:</strong> <code>id</code>, <code>body</code>, <code>created_at</code>, <code>post_id</code>, <code>user_id</code></li>
            </ul>
        </div>

        <div class="step-block">
            <h3>Step 3 — Identify the relationships</h3>
            <p>Ask: which entities reference each other?</p>
            <ul style="margin-top:0.5rem;">
                <li>A <strong>User</strong> writes many <strong>Posts</strong> → one-to-many (FK: <code>posts.user_id</code>)</li>
                <li>A <strong>Post</strong> has many <strong>Comments</strong> → one-to-many (FK: <code>comments.post_id</code>)</li>
                <li>A <strong>User</strong> writes many <strong>Comments</strong> → one-to-many (FK: <code>comments.user_id</code>)</li>
            </ul>
        </div>

        <div class="step-block">
            <h3>Step 4 — Draw the relationship lines</h3>
            <p>Connect the entities with lines. The foreign key column always lives on the "many" side of the relationship. Add crow's foot notation at the "many" end and a single line at the "one" end.</p>
        </div>

        <div class="step-block">
            <h3>Step 5 — Mark primary keys and constraints</h3>
            <p>Mark each primary key (usually with PK). Mark columns that can't be null (NN), columns with unique constraints (UQ), and auto-increment columns (AI). This gives anyone reading the diagram a complete picture of the schema.</p>
        </div>

        <div class="step-block">
            <h3>Step 6 — Review for normalization</h3>
            <p>Look for columns that store the same data in multiple places (redundancy), columns that depend on non-key columns (3NF violation), or any comma-separated values (1NF violation). Restructure before you build.</p>
        </div>

        <div class="step-block">
            <h3>Step 7 — Generate the SQL</h3>
            <p>Once the diagram looks correct, generate your <code>CREATE TABLE</code> DDL from it. A <a href="/demo">diagram tool that exports SQL</a> eliminates transcription errors between design and implementation.</p>
        </div>

        <h2 id="common-er-diagram-mistakes">Common ER Diagram Mistakes</h2>
        <ul>
            <li><strong>Missing many-to-many join tables.</strong> A student can enrol in many courses, and a course can have many students. This needs a <code>enrolments</code> join table — you can't model it with a single foreign key.</li>
            <li><strong>Putting the foreign key on the wrong side.</strong> The FK always goes on the "many" side. An order has one user, so <code>orders.user_id</code> references <code>users.id</code> — not the other way around.</li>
            <li><strong>Forgetting optional relationships.</strong> If a post can exist without an author (e.g., guest posts), <code>user_id</code> should allow <code>NULL</code>. Mark this in your diagram.</li>
            <li><strong>Treating the diagram as final.</strong> ER diagrams should evolve with your understanding. It's much cheaper to redraw a line than to run a production migration.</li>
        </ul>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/er-diagram-tool-online">Free ER Diagram Tool Online for MySQL &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization Explained &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Draw your ER diagram in the browser — for free</h2>
    <p>SQL Designer gives you a drag-and-drop canvas to draw entities, add attributes, and connect relationships. Export MySQL DDL when you're done. No installation required.</p>
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
