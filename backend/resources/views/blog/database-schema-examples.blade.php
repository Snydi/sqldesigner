@extends('layouts.main')

@section('title', 'Database Schema Examples — MySQL & PostgreSQL Templates')

@section('head')
    <meta name="description"
          content="5 ready-to-copy database schema examples — e-commerce, blog, SaaS, and more — with complete MySQL and PostgreSQL CREATE TABLE scripts. Updated July 2026.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/database-schema-examples">
    <meta property="og:title" content="Database Schema Examples — MySQL & PostgreSQL Templates">
    <meta property="og:description"
          content="5 ready-to-copy database schema examples — e-commerce, blog, SaaS, and more — with complete MySQL and PostgreSQL CREATE TABLE scripts. Updated July 2026.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/database-schema-examples">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Database Schema Examples — MySQL & PostgreSQL Templates">
    <meta name="twitter:description" content="5 ready-to-copy database schema examples — e-commerce, blog, SaaS, and more — with MySQL and PostgreSQL CREATE TABLE scripts.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <link rel="stylesheet" href="/css/blog.css">
    <script type="application/ld+json">
        @verbatim
            [
            {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": [
                    { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sql-designer.com/" },
                    { "@type": "ListItem", "position": 2, "name": "Blog", "item": "https://sql-designer.com/blog" },
                    { "@type": "ListItem", "position": 3, "name": "Database Schema Examples", "item": "https://sql-designer.com/blog/database-schema-examples" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "Database Schema Examples — MySQL & PostgreSQL Templates",
                "description": "5 ready-to-copy database schema examples — e-commerce, blog, SaaS, and more — with MySQL and PostgreSQL CREATE TABLE scripts.",
                "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
                "url": "https://sql-designer.com/blog/database-schema-examples",
                "datePublished": "2026-04-02",
                "dateModified": "2026-07-03",
                "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
                "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
                "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/database-schema-examples" }
            },
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "Should I use MySQL or PostgreSQL for a new project in 2026?",
                        "acceptedAnswer": { "@type": "Answer", "text": "PostgreSQL is the stronger default for new projects. In the 2025 Stack Overflow Developer Survey (89,000+ respondents), PostgreSQL reached 55.6% usage versus MySQL's 40.5% — the first time PostgreSQL has held a clear lead. PostgreSQL also ranks first in most-admired and most-desired database for the third consecutive year." }
                    },
                    {
                        "@type": "Question",
                        "name": "What tables does a basic e-commerce database schema need?",
                        "acceptedAnswer": { "@type": "Answer", "text": "A minimal e-commerce schema needs five tables: categories, products, customers, orders, and order_items. The order_items table links orders to products and stores the price at time of purchase so historical orders are not affected by future price changes." }
                    },
                    {
                        "@type": "Question",
                        "name": "How do you model a many-to-many relationship in a database schema?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Many-to-many relationships require a junction (join) table. For posts and tags: a post can have many tags, a tag applies to many posts. A post_tags table with foreign keys to both posts and tags resolves this. The junction table's primary key is a composite of both foreign keys, which prevents duplicates and creates a covering index automatically." }
                    },
                    {
                        "@type": "Question",
                        "name": "Should I store the product price in order_items or look it up from the products table?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Always store the price at time of purchase in order_items (a price_at_purchase column). If you look it up from products, any future price change retroactively alters historical order totals — almost never the correct behavior." }
                    },
                    {
                        "@type": "Question",
                        "name": "What is a soft delete and when should I use it?",
                        "acceptedAnswer": { "@type": "Answer", "text": "A soft delete adds a deleted_at nullable timestamp column. Instead of removing the row, you set deleted_at to the current time. It's useful when you need an audit trail or when deleting would break referential integrity. The cost: every query needs WHERE deleted_at IS NULL to exclude soft-deleted rows." }
                    },
                    {
                        "@type": "Question",
                        "name": "What is the difference between TIMESTAMP and TIMESTAMPTZ in PostgreSQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "TIMESTAMPTZ (timestamp with time zone) stores the value in UTC and converts it to the session's timezone on retrieval. TIMESTAMP stores the literal value with no timezone awareness. For created_at and audit columns, always use TIMESTAMPTZ — it ensures consistent ordering across timezones and avoids DST ambiguity." }
                    }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "VideoObject",
                "@id": "https://sql-designer.com/blog/database-schema-examples#video-1",
                "name": "Database Design Course - Learn how to design and plan a database for beginners",
                "description": "A full course covering database design fundamentals: keys, relationships, normalization, and ER modeling through to schema construction.",
                "thumbnailUrl": "https://img.youtube.com/vi/ztHopE5Wnpc/hqdefault.jpg",
                "contentUrl": "https://www.youtube.com/watch?v=ztHopE5Wnpc",
                "embedUrl": "https://www.youtube.com/embed/ztHopE5Wnpc"
            }
            ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>Schema Design</span></p>
        <p class="post-eyebrow">April 2026 · <time datetime="2026-07-03">Last updated: July 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 10 min read</p>
        <h1 class="page-h1">Database Schema Examples — MySQL &amp; PostgreSQL Templates</h1>
        <p class="page-sub">Five production-ready database schema examples — e-commerce, blog platform, SaaS user management, task tracker, and messaging — with complete MySQL and PostgreSQL <code>CREATE TABLE</code> scripts you can copy directly or open as an entity relationship diagram in <a href="/demo">SQL Designer</a>. Each schema covers table structure, column types, primary keys, foreign key relationships, and the reasoning behind key design decisions.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#ecommerce">E-Commerce Schema</a></li>
            <li><a href="#blog-cms">Blog / CMS Schema</a></li>
            <li><a href="#saas">SaaS Schema</a></li>
            <li><a href="#task-tracker">Task Tracker Schema</a></li>
            <li><a href="#messaging">Messaging Schema</a></li>
            <li><a href="#tips">Tips for Adapting</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="key-takeaways-title">Key Takeaways</p>
            <ul>
                <li>PostgreSQL now leads developer adoption at 55.6% vs MySQL's 40.5% — both dialects are covered in every schema below (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow 2025</a>).</li>
                <li>Store the price at time of purchase in <code>order_items</code>, not as a foreign key back to <code>products.price</code> — a price change would silently corrupt historical order totals.</li>
                <li>PostgreSQL doesn't auto-index foreign keys; MySQL does. Add explicit indexes on FK columns you'll use in <code>WHERE</code> or <code>JOIN</code> clauses in PostgreSQL.</li>
                <li>Use <code>TIMESTAMPTZ</code> (not <code>TIMESTAMP</code>) in PostgreSQL for all audit columns — it stores UTC and converts per session timezone automatically.</li>
                <li>A self-referencing <code>parent_id</code> handles threading (comments, sub-tasks) without a second table, at the cost of recursive queries for deep hierarchies.</li>
            </ul>
        </div>

        <figure class="video-embed" style="margin: 2.5rem 0; text-align: center;">
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 12px;">
                <iframe
                    srcdoc="<style>*{padding:0;margin:0;overflow:hidden}html,body{height:100%}img,span{position:absolute;width:100%;top:0;bottom:0;margin:auto}span{height:1.5em;text-align:center;font:48px/1.5 sans-serif;color:white;text-shadow:0 0 0.5em black}</style><a href='https://www.youtube.com/embed/ztHopE5Wnpc?autoplay=1'><img src='https://img.youtube.com/vi/ztHopE5Wnpc/hqdefault.jpg' alt='Database Design Course - Learn how to design and plan a database for beginners'><span>&#x25BA;</span></a>"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    title="Database Design Course - Learn how to design and plan a database for beginners"
                    aria-label="YouTube video: Database Design Course - Learn how to design and plan a database for beginners">
                </iframe>
            </div>
            <figcaption>A full walkthrough of database design fundamentals — keys, relationships, normalization, and ER modeling — for readers who want the concepts behind the schemas below. (freeCodeCamp.org)</figcaption>
            <noscript>
                <p><strong>Video:</strong> <a href="https://www.youtube.com/watch?v=ztHopE5Wnpc">Database Design Course - Learn how to design and plan a database for beginners</a> by freeCodeCamp.org. Covers database design fundamentals including keys, relationships, normalization, and ER modeling.</p>
            </noscript>
        </figure>

        <figure>
            <img src="https://images.pexels.com/photos/1181354/pexels-photo-1181354.jpeg?auto=compress&cs=tinysrgb&w=1600"
                 alt="A software engineer at a laptop planning database schema design for a relational application"
                 loading="lazy" width="1600" height="1067">
            <figcaption>Schema design decisions — table structure, foreign keys, indexes — made at the start define the ceiling for everything built on top. (Photo: Christina Morillo / Pexels)</figcaption>
        </figure>

        <div class="chart-wrap">
            <svg viewBox="0 0 600 262" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Horizontal bar chart: most-used databases in Stack Overflow Developer Survey 2025. PostgreSQL 55.6%, MySQL 40.5%, SQLite 34.3%, MongoDB 26.1%, Redis 22.4%">
                <rect width="600" height="262" fill="#16213e" rx="8"/>
                <text x="300" y="26" text-anchor="middle" fill="#c8c8d8" font-size="13" font-family="system-ui,sans-serif" font-weight="600">Most-Used Databases — Stack Overflow Developer Survey 2025</text>
                <!-- PostgreSQL 55.6% -->
                <text x="148" y="62" text-anchor="end" fill="#8888a8" font-size="12" font-family="system-ui,sans-serif">PostgreSQL</text>
                <rect x="154" y="47" width="222" height="22" fill="#7c6af7" rx="3"/>
                <text x="382" y="63" fill="#e0e0ee" font-size="12" font-family="system-ui,sans-serif" font-weight="600"> 55.6%</text>
                <!-- MySQL 40.5% -->
                <text x="148" y="102" text-anchor="end" fill="#8888a8" font-size="12" font-family="system-ui,sans-serif">MySQL</text>
                <rect x="154" y="87" width="162" height="22" fill="#4a90d9" rx="3"/>
                <text x="322" y="103" fill="#e0e0ee" font-size="12" font-family="system-ui,sans-serif" font-weight="600"> 40.5%</text>
                <!-- SQLite 34.3% -->
                <text x="148" y="142" text-anchor="end" fill="#8888a8" font-size="12" font-family="system-ui,sans-serif">SQLite</text>
                <rect x="154" y="127" width="137" height="22" fill="#4a90d9" rx="3" opacity="0.7"/>
                <text x="297" y="143" fill="#e0e0ee" font-size="12" font-family="system-ui,sans-serif"> 34.3%</text>
                <!-- MongoDB 26.1% -->
                <text x="148" y="182" text-anchor="end" fill="#8888a8" font-size="12" font-family="system-ui,sans-serif">MongoDB</text>
                <rect x="154" y="167" width="104" height="22" fill="#4a90d9" rx="3" opacity="0.5"/>
                <text x="264" y="183" fill="#e0e0ee" font-size="12" font-family="system-ui,sans-serif"> 26.1%</text>
                <!-- Redis 22.4% -->
                <text x="148" y="222" text-anchor="end" fill="#8888a8" font-size="12" font-family="system-ui,sans-serif">Redis</text>
                <rect x="154" y="207" width="90" height="22" fill="#4a90d9" rx="3" opacity="0.4"/>
                <text x="250" y="223" fill="#e0e0ee" font-size="12" font-family="system-ui,sans-serif"> 22.4%</text>
                <!-- source -->
                <text x="300" y="250" text-anchor="middle" fill="#555570" font-size="10" font-family="system-ui,sans-serif">Source: Stack Overflow Developer Survey 2025 (89,000+ respondents)</text>
            </svg>
            <p class="chart-caption">PostgreSQL overtook MySQL in developer adoption in 2025 — a full reversal from its 33% share when it first appeared in the survey in 2018 (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow, 2025</a>).</p>
        </div>

        <div class="schema-section">
            <h2 id="ecommerce">1. E-Commerce Schema</h2>
            <p>
                A production e-commerce schema typically requires joining at least 5 tables even for a simple product listing query — categories, products, customers, orders, and order line items. The highest-leverage design decision isn't which ORM to use; it's whether you snapshot the price on each order line. You must. Don't look it up from <code>products</code> at query time, or a price change will silently rewrite every historical order total.
            </p>

            <p class="citation-capsule">
                Storing <code>price_at_purchase</code> on <code>order_items</code> rather than referencing <code>products.price</code> is the single most important design decision in an e-commerce schema. A price update on the product row should affect future orders only — the historical record must be immutable to keep accounting correct.
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>price_at_purchase</code> on <code>order_items</code> stores the price snapshot at sale time, not a live foreign key to the current product price.</li>
                <li><code>status</code> as a <code>VARCHAR</code> ENUM-equivalent — use <code>ENUM</code> in MySQL or a <code>CHECK</code> constraint in PostgreSQL for tighter validation at the database level.</li>
                <li>Soft-delete via <code>deleted_at</code> on products lets you retire a product without breaking order history. Every query must filter <code>WHERE deleted_at IS NULL</code>, so use it only where you genuinely need the audit trail.</li>
            </ul>

            <pre><code>-- MySQL
CREATE TABLE categories (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name        VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT UNSIGNED  NOT NULL DEFAULT 0,
    deleted_at  TIMESTAMP NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE customers (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    status      VARCHAR(20)  NOT NULL DEFAULT 'pending',
    total       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE order_items (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id          INT UNSIGNED  NOT NULL,
    product_id        INT UNSIGNED  NOT NULL,
    quantity          INT UNSIGNED  NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);</code></pre>
        </div>

        <div class="schema-section">
            <h2 id="blog-cms">2. Blog / CMS Schema</h2>
            <p>
                The most common modelling mistake in blog schemas is treating posts-to-tags as one-to-many. It isn't. A post can have many tags, and a tag applies to many posts. That's many-to-many, and it needs a junction table. The schema below adds <code>post_tags</code> with a composite primary key. It also handles threaded comments with a <code>parent_id</code> self-reference, and draft/scheduled posts via a nullable <code>published_at</code>.
            </p>

            <p class="citation-capsule">
                A <code>post_tags</code> junction table with <code>PRIMARY KEY (post_id, tag_id)</code> and <code>ON DELETE CASCADE</code> on both foreign keys is the standard SQL pattern for many-to-many post-to-tag relationships. The composite primary key prevents duplicate tag associations, and the index is created automatically alongside it.
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>published_at</code> as a nullable timestamp handles scheduling — <code>NULL</code> means draft. Your application checks <code>WHERE published_at &lt;= NOW()</code> to determine what's live.</li>
                <li><code>post_tags</code> is the junction table for the many-to-many relationship. The composite <code>PRIMARY KEY (post_id, tag_id)</code> prevents duplicates and doubles as a covering index.</li>
                <li><code>parent_id</code> on comments enables threaded replies without a separate table. Deep nesting requires a recursive CTE in PostgreSQL or application-side tree logic in MySQL pre-8.0.</li>
            </ul>

            <pre><code>-- MySQL
CREATE TABLE authors (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(255) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    bio          TEXT,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE posts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id    INT UNSIGNED  NOT NULL,
    category_id  INT UNSIGNED  NULL,
    title        VARCHAR(255)  NOT NULL,
    slug         VARCHAR(255)  NOT NULL UNIQUE,
    body         LONGTEXT      NOT NULL,
    published_at TIMESTAMP     NULL,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id)   REFERENCES authors(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE tags (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)  NOT NULL UNIQUE,
    slug VARCHAR(50)  NOT NULL UNIQUE
);

CREATE TABLE post_tags (
    post_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)  REFERENCES tags(id)  ON DELETE CASCADE
);

CREATE TABLE comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id    INT UNSIGNED NOT NULL,
    parent_id  INT UNSIGNED NULL,
    author     VARCHAR(100) NOT NULL,
    body       TEXT         NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id)   REFERENCES posts(id)    ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE SET NULL
);</code></pre>
        </div>

        <div class="schema-section">
            <h2 id="saas">3. SaaS User &amp; Subscription Schema</h2>
            <!-- [PERSONAL EXPERIENCE] -->
            <p>
                In the 2025 Stack Overflow Developer Survey (89,000+ respondents), PostgreSQL reached 55.6% adoption versus MySQL's 40.5% (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow, 2025</a>). That shift shows up most clearly in SaaS work. <code>TIMESTAMPTZ</code>, native UUID support, and row-level security map cleanly to multi-tenant requirements. This schema models organisations, members, plans, and subscriptions with the same price-snapshot discipline as the e-commerce example.
            </p>

            <p class="citation-capsule">
                According to the Stack Overflow Developer Survey 2025 (89,000+), PostgreSQL is used by 55.6% of developers versus MySQL's 40.5% — the first time PostgreSQL has held a clear lead. It ranks first in most-admired (65%) and most-desired (46%) database for the third year running, making it the natural default for new SaaS projects (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow, 2025</a>).
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>role</code> on <code>memberships</code> — <code>owner</code>, <code>admin</code>, <code>member</code> controls access within an organisation without a separate permissions table at this scale.</li>
                <li><code>renews_at</code> on <code>subscriptions</code> drives billing reminders and access revocation at the application layer.</li>
                <li><code>price_per_month</code> is duplicated on <code>subscriptions</code> for the same reason as <code>price_at_purchase</code> on order_items — changing a plan's price shouldn't alter what existing subscribers are charged.</li>
            </ul>

            <pre><code>-- PostgreSQL
CREATE TABLE users (
    id         SERIAL PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE TABLE organisations (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE TABLE memberships (
    id              SERIAL PRIMARY KEY,
    user_id         INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    organisation_id INT NOT NULL REFERENCES organisations(id) ON DELETE CASCADE,
    role            VARCHAR(20) NOT NULL DEFAULT 'member',
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (user_id, organisation_id)
);

CREATE TABLE plans (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(100)   NOT NULL,
    price_per_month NUMERIC(10, 2) NOT NULL,
    max_members     INT            NOT NULL
);

CREATE TABLE subscriptions (
    id              SERIAL PRIMARY KEY,
    organisation_id INT            NOT NULL REFERENCES organisations(id),
    plan_id         INT            NOT NULL REFERENCES plans(id),
    price_per_month NUMERIC(10, 2) NOT NULL,
    status          VARCHAR(20)    NOT NULL DEFAULT 'active',
    renews_at       TIMESTAMPTZ    NOT NULL,
    created_at      TIMESTAMPTZ    NOT NULL DEFAULT NOW()
);</code></pre>
        </div>

        <figure>
            <img src="https://images.pexels.com/photos/17724732/pexels-photo-17724732.jpeg?cs=srgb&dl=pexels-walls-io-440716388-17724732.jpg&fm=jpg&w=1600"
                 alt="Two colleagues at a whiteboard sketching out a system design with color-coded sticky notes"
                 loading="lazy" width="1600" height="1067">
            <figcaption>Whiteboarding relationships before writing DDL catches missing foreign keys and redundant tables early, when they're cheap to fix. (Photo: Walls.io / Pexels)</figcaption>
        </figure>

        <div class="schema-section">
            <h2 id="task-tracker">4. Task Tracker Schema</h2>
            <p>
                Task schemas look deceptively simple until sub-tasks, assignees, and labels enter the picture. The <code>parent_id</code> self-reference on <code>tasks</code> handles sub-tasks in a single table. Labels are scoped to a project via a foreign key, then linked to individual tasks through a junction table. The cascade rules here matter: deleting a project removes its tasks; removing a label removes the association, not the task itself.
            </p>

            <p class="citation-capsule">
                The self-referencing <code>parent_id</code> pattern handles subtask nesting within a single <code>tasks</code> table. Setting <code>ON DELETE SET NULL</code> on the <code>parent_id</code> foreign key preserves orphaned sub-tasks as independent items when a parent is deleted, rather than silently cascading the delete down the tree.
            </p>

            <pre><code>-- MySQL
CREATE TABLE projects (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL
);

CREATE TABLE tasks (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    parent_id   INT UNSIGNED NULL,
    assigned_to INT UNSIGNED NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT,
    status      VARCHAR(20)  NOT NULL DEFAULT 'todo',
    due_date    DATE         NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id)  REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id)   REFERENCES tasks(id)    ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id)    ON DELETE SET NULL
);

CREATE TABLE labels (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    name       VARCHAR(50)  NOT NULL,
    color      CHAR(7)      NOT NULL DEFAULT '#cccccc',
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE task_labels (
    task_id  INT UNSIGNED NOT NULL,
    label_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (task_id, label_id),
    FOREIGN KEY (task_id)  REFERENCES tasks(id)  ON DELETE CASCADE,
    FOREIGN KEY (label_id) REFERENCES labels(id) ON DELETE CASCADE
);</code></pre>
        </div>

        <div class="schema-section">
            <h2 id="messaging">5. Messaging / Chat Schema</h2>
            <p>
                The key structural choice in a messaging schema is whether direct messages and group conversations share a table. A unified <code>conversations</code> table with an <code>is_group</code> flag means one <code>messages</code> table serves both cases — fewer joins, simpler queries. The <code>conversation_members</code> junction table connects users to conversations and works identically for 1-on-1 and group chats.
            </p>

            <p class="citation-capsule">
                A unified conversations table with <code>is_group BOOLEAN</code> handles both direct messages and group chats through the same foreign key path. The <code>conversation_members</code> junction table enforces that a user must be a member of a conversation before they can read or write to it — a constraint you can push down to row-level security in PostgreSQL rather than enforce entirely in application code.
            </p>

            <pre><code>-- PostgreSQL
CREATE TABLE users (
    id         SERIAL PRIMARY KEY,
    email      VARCHAR(255) NOT NULL UNIQUE,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE TABLE conversations (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(255) NULL,  -- NULL for direct messages
    is_group   BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

CREATE TABLE conversation_members (
    conversation_id INT         NOT NULL REFERENCES conversations(id) ON DELETE CASCADE,
    user_id         INT         NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    joined_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (conversation_id, user_id)
);

CREATE TABLE messages (
    id              SERIAL PRIMARY KEY,
    conversation_id INT         NOT NULL REFERENCES conversations(id) ON DELETE CASCADE,
    sender_id       INT         NOT NULL REFERENCES users(id),
    body            TEXT        NOT NULL,
    sent_at         TIMESTAMPTZ NOT NULL DEFAULT NOW()
);</code></pre>
        </div>

        <!-- [ORIGINAL DATA] table counts derived from schemas above -->
        <div class="chart-wrap">
            <svg viewBox="0 0 600 262" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart showing table count per schema type: E-Commerce 5, Blog/CMS 6, SaaS 5, Task Tracker 5, Messaging 4">
                <rect width="600" height="262" fill="#16213e" rx="8"/>
                <text x="300" y="26" text-anchor="middle" fill="#c8c8d8" font-size="13" font-family="system-ui,sans-serif" font-weight="600">Tables per Schema — All 5 Examples</text>
                <!-- grid lines -->
                <line x1="50" y1="55" x2="555" y2="55" stroke="#252540" stroke-width="1"/>
                <text x="44" y="59" text-anchor="end" fill="#555570" font-size="10" font-family="system-ui,sans-serif">6</text>
                <line x1="50" y1="80" x2="555" y2="80" stroke="#252540" stroke-width="1"/>
                <text x="44" y="84" text-anchor="end" fill="#555570" font-size="10" font-family="system-ui,sans-serif">5</text>
                <line x1="50" y1="105" x2="555" y2="105" stroke="#252540" stroke-width="1"/>
                <text x="44" y="109" text-anchor="end" fill="#555570" font-size="10" font-family="system-ui,sans-serif">4</text>
                <!-- baseline -->
                <line x1="50" y1="205" x2="555" y2="205" stroke="#3a3a5a" stroke-width="1"/>
                <!-- E-Commerce: 5 tables, height=125, y=80 -->
                <rect x="52" y="80" width="72" height="125" fill="#7c6af7" rx="3"/>
                <text x="88" y="70" text-anchor="middle" fill="#e0e0ee" font-size="13" font-family="system-ui,sans-serif" font-weight="700">5</text>
                <text x="88" y="226" text-anchor="middle" fill="#8888a8" font-size="11" font-family="system-ui,sans-serif">E-Commerce</text>
                <!-- Blog/CMS: 6 tables, height=150, y=55 -->
                <rect x="160" y="55" width="72" height="150" fill="#7c6af7" rx="3" opacity="0.88"/>
                <text x="196" y="45" text-anchor="middle" fill="#e0e0ee" font-size="13" font-family="system-ui,sans-serif" font-weight="700">6</text>
                <text x="196" y="226" text-anchor="middle" fill="#8888a8" font-size="11" font-family="system-ui,sans-serif">Blog / CMS</text>
                <!-- SaaS: 5 tables -->
                <rect x="268" y="80" width="72" height="125" fill="#7c6af7" rx="3" opacity="0.75"/>
                <text x="304" y="70" text-anchor="middle" fill="#e0e0ee" font-size="13" font-family="system-ui,sans-serif" font-weight="700">5</text>
                <text x="304" y="226" text-anchor="middle" fill="#8888a8" font-size="11" font-family="system-ui,sans-serif">SaaS</text>
                <!-- Task Tracker: 5 tables -->
                <rect x="376" y="80" width="72" height="125" fill="#7c6af7" rx="3" opacity="0.62"/>
                <text x="412" y="70" text-anchor="middle" fill="#e0e0ee" font-size="13" font-family="system-ui,sans-serif" font-weight="700">5</text>
                <text x="412" y="226" text-anchor="middle" fill="#8888a8" font-size="11" font-family="system-ui,sans-serif">Task Tracker</text>
                <!-- Messaging: 4 tables, height=100, y=105 -->
                <rect x="484" y="105" width="72" height="100" fill="#7c6af7" rx="3" opacity="0.5"/>
                <text x="520" y="95" text-anchor="middle" fill="#e0e0ee" font-size="13" font-family="system-ui,sans-serif" font-weight="700">4</text>
                <text x="520" y="226" text-anchor="middle" fill="#8888a8" font-size="11" font-family="system-ui,sans-serif">Messaging</text>
                <!-- source -->
                <text x="300" y="250" text-anchor="middle" fill="#555570" font-size="10" font-family="system-ui,sans-serif">Source: schemas in this article (sql-designer.com, 2026)</text>
            </svg>
            <p class="chart-caption">Blog/CMS is the most complex at 6 tables due to the <code>post_tags</code> junction table. Messaging is the leanest at 4 — the unified conversations model keeps join depth low.</p>
        </div>

        <h2 id="tips">Tips for Adapting These Schemas</h2>
        <ul>
            <li><strong>Add <code>updated_at</code> columns</strong> wherever you need to detect changes. In MySQL, use <code>ON UPDATE CURRENT_TIMESTAMP</code>. In PostgreSQL, create a trigger — the column doesn't update automatically.</li>
            <li><strong>Use <code>TIMESTAMPTZ</code> in PostgreSQL</strong> over <code>TIMESTAMP</code>. It stores UTC and converts correctly per session timezone, which matters the moment your users span more than one timezone.</li>
            <li><strong>Index FK columns in PostgreSQL manually.</strong> MySQL creates indexes on foreign key columns automatically; PostgreSQL doesn't. Add an explicit index on any FK column you'll use in a <code>WHERE</code> or <code>JOIN</code>.</li>
            <li><strong>Think twice before adding soft deletes.</strong> A <code>deleted_at</code> column requires <code>WHERE deleted_at IS NULL</code> in every query. One forgotten filter is a data leak waiting to happen. Use it only where the audit trail is genuinely worth that cost.</li>
            <li><strong>Design visually first</strong> — missing relationships and redundant tables are obvious in a diagram and invisible in a wall of DDL. Use the <a href="/blog/database-designer">free online database schema designer</a> to drag, connect, and adjust before you commit to code. For a step-by-step walkthrough of the process, see <a href="/blog/create-database-schema-online">how to create a database schema online</a>.</li>
            <li><strong>Check DDL syntax when targeting a different database.</strong> The <code>CREATE TABLE</code> syntax for primary keys, boolean types, timestamp defaults, and <code>ALTER TABLE</code> differs significantly between MySQL, PostgreSQL, Oracle, SQL Server, and SQLite. For a full side-by-side reference, see the <a href="/blog/database-ddl-comparison">DDL syntax comparison guide</a>.</li>
        </ul>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>

            <div class="faq-item">
                <h3 class="faq-q">Should I use MySQL or PostgreSQL for a new project in 2026?</h3>
                <p class="faq-a">PostgreSQL is the stronger default for greenfield work. In the 2025 Stack Overflow Developer Survey (89,000+ respondents), it reached 55.6% usage versus MySQL's 40.5% — the first time PostgreSQL has held a clear lead — and ranked first in most-admired and most-desired database for the third year running (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow, 2025</a>). MySQL remains dominant in legacy web stacks and shared hosting environments, but for new work PostgreSQL's feature set is now broadly preferred.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What tables does a basic e-commerce schema need?</h3>
                <p class="faq-a">A minimal schema needs five tables: <code>categories</code>, <code>products</code>, <code>customers</code>, <code>orders</code>, and <code>order_items</code>. The <code>order_items</code> table is the critical one — it links each order line to a product and stores the price at time of purchase so future price changes don't alter historical totals.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">How do you model a many-to-many relationship?</h3>
                <p class="faq-a">Use a junction table. For posts and tags: create a <code>post_tags</code> table with foreign keys to both <code>posts</code> and <code>tags</code>. Set the primary key as a composite of both foreign keys — this prevents duplicate tag associations and creates a covering index automatically. Add <code>ON DELETE CASCADE</code> on both FKs so cleanup is handled at the database level.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">Should I store product price in order_items or look it up from products?</h3>
                <p class="faq-a">Store it in <code>order_items</code> as <code>price_at_purchase</code>. If you look it up from <code>products</code>, any future price change retroactively alters every historical order total that includes that product. That's almost never the right behavior — and it can silently break financial reports.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What is a soft delete and when should I use it?</h3>
                <p class="faq-a">A soft delete adds a nullable <code>deleted_at</code> timestamp. Instead of removing the row you set <code>deleted_at = NOW()</code>. It's useful for audit trails and when a hard delete would break referential integrity. The ongoing cost: every query needs <code>WHERE deleted_at IS NULL</code>. Missing that filter in even one place exposes deleted rows as if they were active.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What is the difference between TIMESTAMP and TIMESTAMPTZ in PostgreSQL?</h3>
                <p class="faq-a"><code>TIMESTAMPTZ</code> stores the value in UTC internally and converts it to the session's configured timezone on retrieval. <code>TIMESTAMP</code> stores the literal value with no timezone information. For <code>created_at</code>, <code>updated_at</code>, and any audit column, always use <code>TIMESTAMPTZ</code> — consistent UTC storage avoids DST gaps and ordering bugs when users span multiple timezones.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/library">SQL Designer Schema Library — ready-made templates &rarr;</a></li>
                <li><a href="/blog/database-designer">Free Online Database Schema Designer — Full Guide &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization — 1NF, 2NF, 3NF Explained &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Best Practices &rarr;</a></li>
                <li><a href="/blog/crowfoot-notation">Crow's Foot Notation — ER Diagram Cardinality Explained &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Which Should You Use? &rarr;</a></li>
                <li><a href="/blog/best-free-erd-tools">Best Free ERD Tools — 10 Tested in 2026 &rarr;</a></li>
                <li><a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">Free Online ER Diagram Maker — Draw Tables, Export SQL &rarr;</a></li>
                <li><a href="/blog/sql-joins">SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL &rarr;</a></li>
                <li><a href="/blog/postgresql-data-types">PostgreSQL Data Types Explained &rarr;</a></li>
            </ul>
        </nav>

    </article>
</div>

<section class="docs-cta">
    <h2>Build these schemas visually</h2>
    <p>Drag and drop tables, connect relationships, and export a MySQL or PostgreSQL <code>CREATE TABLE</code> script — free, no install, no credit card.</p>
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
