@extends('layouts.main')

@section('title', 'Database Schema Examples — MySQL & PostgreSQL Templates')

@section('head')
    <meta name="description"
          content="Schema examples for e-commerce, blog, SaaS, and more. MySQL and PostgreSQL CREATE TABLE templates you can copy or build visually in SQL Designer.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/database-schema-examples">
    <meta property="og:title" content="Database Schema Examples — MySQL & PostgreSQL Templates">
    <meta property="og:description"
          content="Real-world database schema examples for e-commerce, blog, SaaS, and more — with MySQL and PostgreSQL SQL you can use directly.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/database-schema-examples">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Database Schema Examples — MySQL & PostgreSQL Templates">
    <meta name="twitter:description" content="Real-world database schema examples for e-commerce, blog, SaaS, and more — with MySQL and PostgreSQL SQL.">
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
                    { "@type": "ListItem", "position": 3, "name": "Database Schema Examples", "item": "https://sql-designer.com/blog/database-schema-examples" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "Database Schema Examples — MySQL & PostgreSQL Templates",
                "description": "Real-world database schema examples for e-commerce, blog, SaaS, and more — with MySQL and PostgreSQL SQL you can use directly.",
                "image": "https://sql-designer.com/images/designer_screenshot.png",
                "url": "https://sql-designer.com/blog/database-schema-examples",
                "datePublished": "2026-04-02",
                "dateModified": "2026-04-02",
                "author": { "@type": "Organization", "name": "SQL Designer" },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
            }
            ]
        @endverbatim
    </script>
    <style>
        body {
            overflow-y: auto;
        }

        .blog-post {
            max-width: 760px;
            margin: 0 auto;
            padding: 3rem 1.5rem 5rem;
        }

        .blog-post .breadcrumb {
            font-size: 0.875rem;
            color: #767676;
            background-color: transparent;
            text-transform: none;
            margin-bottom: 1.5rem;
        }

        .blog-post .breadcrumb a {
            color: var(--color-primary);
        }

        .blog-post .post-meta {
            font-size: 0.875rem;
            color: #767676;
            background-color: transparent;
            text-transform: none;
            margin-bottom: 1rem;
        }

        .blog-post h1 {
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-primary);
            background-color: transparent;
            margin: 0 0 1rem;
            line-height: 1.3;
        }

        .blog-post .intro {
            font-size: 1rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            border-left: 3px solid var(--color-primary);
            padding-left: 1.2rem;
        }

        .blog-post h2 {
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-primary);
            background-color: transparent;
            margin: 2.5rem 0 0.8rem;
        }

        .blog-post h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-primary);
            background-color: transparent;
            margin: 1.5rem 0 0.6rem;
        }

        .blog-post p {
            font-size: 0.9rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin: 0 0 1rem;
        }

        .blog-post ul {
            margin: 0 0 1rem 1.5rem;
            padding: 0;
        }

        .blog-post li {
            font-size: 0.9rem;
            color: var(--text-secondary);
            background-color: transparent;
            text-transform: none;
            line-height: 1.8;
            margin-bottom: 0.3rem;
        }

        .blog-post pre {
            background: var(--bg-elevated);
            border-radius: 6px;
            padding: 1.2rem 1.5rem;
            overflow-x: auto;
            margin: 0 0 1.5rem;
        }

        .blog-post pre code {
            background: none;
            padding: 0;
            font-size: 0.82rem;
            color: var(--text-primary);
            line-height: 1.7;
        }

        .blog-post code {
            background: var(--bg-elevated);
            padding: 0.1em 0.4em;
            border-radius: 3px;
            font-size: 0.85em;
            color: var(--text-primary);
        }

        .blog-post .schema-section {
            background: var(--bg-surface);
            border-radius: 6px;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            margin-bottom: 2rem;
        }

        .blog-post .schema-section h2 {
            margin-top: 0;
        }

        .blog-post .cta-box {
            background: var(--color-primary-hover);
            color: #fff;
            border-radius: 6px;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        .blog-post .cta-box h3 {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 0.8rem;
        }

        .blog-post .cta-box p {
            color: #fff;
            background-color: transparent;
            margin: 0 0 1.2rem;
            font-size: 0.85rem;
        }

        .blog-post .btn-cta {
            background: var(--bg-surface);
            color: var(--color-primary);
            padding: 0.6rem 1.8rem;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
        }

        .blog-post .btn-cta:hover {
            opacity: 0.9;
        }
    </style>
@endsection

@section('content')
    <article class="blog-post">
        <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; Schema Design</p>
        <p class="post-meta"><time datetime="2026-04-02">April 2026</time> &mdash; 9 min read</p>
        <h1>Database Schema Examples — MySQL &amp; PostgreSQL Templates</h1>

        <p class="intro">
            Starting a new database from scratch is easier when you have a concrete example to work from. Below are
            five common real-world database schema examples — e-commerce, blog, SaaS user management, task tracker,
            and messaging — with MySQL and PostgreSQL <code>CREATE TABLE</code> scripts you can copy directly, or
            open as entity relationship diagram examples in <a href="/demo">SQL Designer</a>.
        </p>

        <div class="schema-section">
            <h2>1. E-Commerce Schema</h2>
            <p>
                A minimal but complete schema for an online store: products with categories, customers, orders, and
                order line items. The <code>order_items</code> table captures the price at time of purchase so that
                historical orders aren't affected by future price changes.
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>price_at_purchase</code> on <code>order_items</code> — stores the price snapshot, not a
                    live foreign key to the current product price.</li>
                <li><code>status</code> as a <code>VARCHAR</code> ENUM-equivalent — use <code>ENUM</code> in MySQL
                    or a <code>CHECK</code> constraint in PostgreSQL for tighter validation.</li>
                <li>Soft-delete pattern via <code>deleted_at</code> on products — allows retiring a product without
                    breaking order history.</li>
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
            <h2>2. Blog / CMS Schema</h2>
            <p>
                A blog platform with authors, posts, categories, tags, and comments. Posts can belong to one category
                and have many tags via a junction table. Comments support basic threading with a
                <code>parent_id</code> self-reference.
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>published_at</code> allows scheduling posts — <code>NULL</code> means draft.</li>
                <li><code>post_tags</code> is a junction table for the many-to-many relationship between posts and
                    tags.</li>
                <li><code>parent_id</code> on comments enables threaded replies without a separate table.</li>
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
            <h2>3. SaaS User &amp; Subscription Schema</h2>
            <p>
                A multi-tenant SaaS schema: organisations with multiple members, subscription plans, and a
                subscription record linking an organisation to its current plan. Tracks billing dates and status.
            </p>

            <h3>Key design decisions</h3>
            <ul>
                <li><code>role</code> on <code>memberships</code> — <code>owner</code>, <code>admin</code>,
                    <code>member</code> controls what each user can do within an organisation.</li>
                <li><code>renews_at</code> on <code>subscriptions</code> — used to trigger billing reminders and
                    access revocation.</li>
                <li>Separate <code>plans</code> table — changing a plan's price doesn't retroactively alter existing
                    subscriptions because the subscription stores <code>price_per_month</code> at sign-up time.</li>
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

        <div class="schema-section">
            <h2>4. Task Tracker Schema</h2>
            <p>
                Projects with tasks, assignees, and labels. Tasks can have sub-tasks (same self-referencing pattern as
                the comment example). Labels are shared across a project.
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
            <h2>5. Messaging / Chat Schema</h2>
            <p>
                Direct messages and group conversations. A <code>conversation_members</code> junction table connects
                users to conversations. Messages reference a conversation and a sender.
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

        <h2>Tips for Adapting These Schemas</h2>
        <ul>
            <li><strong>Add <code>updated_at</code> columns</strong> wherever you'll need to detect changes — use
                <code>ON UPDATE CURRENT_TIMESTAMP</code> in MySQL or a trigger in PostgreSQL.</li>
            <li><strong>Use <code>TIMESTAMPTZ</code> in PostgreSQL</strong> instead of <code>TIMESTAMP</code> —
                it stores UTC and converts correctly per session timezone.</li>
            <li><strong>Index your foreign keys</strong> — MySQL does this automatically; PostgreSQL does not. Add
                indexes on any column you'll use in a <code>WHERE</code> or <code>JOIN</code>.</li>
            <li><strong>Consider soft deletes carefully</strong> — a <code>deleted_at</code> column is convenient but
                requires filtering in every query. Use it only where you genuinely need an audit trail.</li>
            <li><strong>Design visually first</strong> — it's easier to spot missing relationships and redundant tables
                in a diagram than in a text script. Use the <a href="/demo">SQL Designer demo</a> to drag, connect, and
                adjust before committing to DDL.</li>
        </ul>

        <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border-color);">
            <p style="font-size:0.875rem; text-transform:uppercase; letter-spacing:0.06em; color:#767676; margin:0 0 0.8rem;">
                Related Articles</p>
            <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
                <li><a href="/blog/how-to-design-mysql-database-schema"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">How to Design a MySQL Database Schema &rarr;</a></li>
                <li><a href="/blog/database-normalization"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Database Normalization — 1NF, 2NF, 3NF Explained &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">MySQL Foreign Key — Syntax and Best Practices &rarr;</a></li>
            </ul>
        </nav>

        <div class="cta-box">
            <h3>Build these schemas visually</h3>
            <p>Drag and drop tables, connect relationships, and export a MySQL or PostgreSQL <code>CREATE TABLE</code>
                script — free, no install, no credit card.</p>
            <a class="btn-cta" href="/demo">Try the Demo</a>
        </div>
    </article>
@endsection
