@extends('layouts.main')

@section('title', 'MySQL Foreign Key — Syntax, Examples, and Best Practices')

@section('head')
    <meta name="description"
          content="A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-foreign-key">
    <meta property="og:title" content="MySQL Foreign Key — Syntax, Examples, and Best Practices">
    <meta property="og:description"
          content="A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-foreign-key">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MySQL Foreign Key — Syntax, Examples, and Best Practices">
    <meta name="twitter:description" content="A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.">
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
                    { "@type": "ListItem", "position": 3, "name": "MySQL Foreign Key — Syntax, Examples, and Best Practices", "item": "https://sql-designer.com/blog/mysql-foreign-key" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "MySQL Foreign Key — Syntax, Examples, and Best Practices",
                "description": "A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.",
                "image": "https://sql-designer.com/images/designer_screenshot.png",
                "url": "https://sql-designer.com/blog/mysql-foreign-key",
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
        .article-body table { width: 100%; border-collapse: collapse; margin: 0 0 1.5rem; font-size: 0.85rem; }
        .article-body th { background: var(--bg-elevated); color: var(--text-primary); padding: 0.6rem 1rem; text-align: left; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid var(--border-strong); font-family: 'JetBrains Mono', monospace; }
        .article-body td { padding: 0.55rem 1rem; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); vertical-align: top; }
        .article-body tr:nth-child(even) td { background: var(--bg-elevated); }
        .article-body a { color: var(--color-primary-text); }
        .article-body strong { color: var(--text-primary); }

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
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>MySQL</span></p>
        <p class="post-eyebrow">March 2026 · 6 min read</p>
        <h1 class="page-h1">MySQL Foreign Key — Syntax, Examples, and Best Practices</h1>
        <p class="page-sub">Foreign keys are the mechanism that turns a collection of isolated tables into a relational database. They enforce that relationships between rows are always valid, preventing orphaned records and data integrity bugs. This guide covers everything you need to know to use them correctly in MySQL.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-is-a-foreign-key">What Is a Foreign Key?</a></li>
            <li><a href="#basic-syntax">Basic Syntax</a></li>
            <li><a href="#on-delete-and-on-update-options">ON DELETE and ON UPDATE</a></li>
            <li><a href="#a-practical-example-e-commerce-schema">Practical Example</a></li>
            <li><a href="#common-mistakes">Common Mistakes</a></li>
            <li><a href="#visualise-foreign-keys-before-writing-ddl">Visualise First</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <h2 id="what-is-a-foreign-key">What Is a Foreign Key?</h2>
        <p>
            A foreign key is a column (or group of columns) in one table that references the primary key of another table. It creates a constraint: MySQL will reject any insert or update that would create a reference to a row that doesn't exist in the parent table.
        </p>
        <p>
            For example: if you have an <code>orders</code> table with a <code>user_id</code> column, a foreign key constraint ensures that every <code>user_id</code> in <code>orders</code> corresponds to a real row in the <code>users</code> table.
        </p>

        <h2 id="basic-syntax">Basic Syntax</h2>
        <p>You can define a foreign key inline when creating a table, or add it separately with <code>ALTER TABLE</code>.</p>

        <p><strong>Inline (at table creation):</strong></p>
        <pre><code>CREATE TABLE orders (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);</code></pre>

        <p><strong>Added after table creation:</strong></p>
        <pre><code>ALTER TABLE orders
ADD CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;</code></pre>

        <h2 id="on-delete-and-on-update-options">ON DELETE and ON UPDATE Options</h2>
        <p>
            These clauses control what happens to child rows when the referenced parent row is deleted or its primary key is updated. Choose carefully — the wrong option can cause data loss or leave orphaned rows.
        </p>
        <ul>
            <li><code>CASCADE</code> — automatically delete or update child rows to match the parent. Use for tightly coupled data (e.g., <code>order_items</code> when an <code>order</code> is deleted).</li>
            <li><code>SET NULL</code> — set the foreign key column to <code>NULL</code> when the parent is deleted/updated. The column must allow <code>NULL</code>. Use when the child can exist independently (e.g., a post whose author was deleted).</li>
            <li><code>RESTRICT</code> — prevent deletion/update of the parent if child rows exist. This is the default if you omit the clause. Use when you want to force explicit cleanup.</li>
            <li><code>NO ACTION</code> — identical to <code>RESTRICT</code> in MySQL's InnoDB implementation.</li>
            <li><code>SET DEFAULT</code> — not supported by InnoDB; avoid it.</li>
        </ul>

        <h2 id="a-practical-example-e-commerce-schema">A Practical Example: E-commerce Schema</h2>
        <pre><code>CREATE TABLE users (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

CREATE TABLE orders (
    id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    total   DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE order_items (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id   INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity   INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    CONSTRAINT fk_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);</code></pre>
        <p>
            Here, deleting a user is blocked if they have orders (<code>RESTRICT</code>). Deleting an order automatically removes its line items (<code>CASCADE</code>). Updating a user's <code>id</code> propagates to all their orders (<code>CASCADE</code>).
        </p>

        <h2 id="common-mistakes">Common Mistakes</h2>
        <ul>
            <li><strong>Mismatched data types.</strong> The child column and parent column must have exactly the same type, including sign. An <code>INT UNSIGNED</code> primary key requires an <code>INT UNSIGNED</code> foreign key — plain <code>INT</code> won't work.</li>
            <li><strong>Missing index on the child column.</strong> MySQL requires an index on the foreign key column. If you don't create one explicitly, MySQL creates one automatically — but it's good practice to be explicit.</li>
            <li><strong>Using MyISAM instead of InnoDB.</strong> Foreign key constraints are only enforced on InnoDB tables. MyISAM silently ignores them.</li>
            <li><strong>Circular dependencies.</strong> Be careful when two tables reference each other. Use <code>SET NULL</code> or carefully order inserts/deletes to avoid constraint violations.</li>
        </ul>

        <h2 id="visualise-foreign-keys-before-writing-ddl">Visualise Foreign Keys Before Writing DDL</h2>
        <p>
            For anything beyond a few tables, it's much easier to <a href="/demo">design your relationships visually</a> first and generate the SQL from the diagram. Drawing the lines between tables makes cascade behaviour and cardinality immediately obvious — mistakes that would take hours to debug in raw SQL are visible at a glance.
        </p>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/how-to-design-mysql-database-schema">How to Design a MySQL Database Schema &rarr;</a></li>
                <li><a href="/blog/er-diagram-tool-online">Free ER Diagram Tool Online for MySQL &rarr;</a></li>
                <li><a href="/blog/mysql-data-types">MySQL Data Types Explained &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design foreign key relationships visually</h2>
    <p>SQL Designer lets you draw foreign key lines between tables and generates the correct MySQL DDL automatically. Free, no installation required.</p>
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
