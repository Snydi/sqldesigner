@extends('layouts.main')

@section('title', 'MySQL Foreign Key — Syntax, Examples, and Best Practices')

@section('head')
    <meta name="description" content="A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-foreign-key">
    <meta property="og:title" content="MySQL Foreign Key — Syntax, Examples, and Best Practices">
    <meta property="og:description" content="A complete guide to MySQL foreign keys: syntax, ON DELETE and ON UPDATE options, practical examples, and common mistakes to avoid.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-foreign-key">
    <meta property="og:image" content="https://sql-designer.com/images/screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://sql-designer.com/images/screenshot.png">
    <script type="application/ld+json">
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
        "image": "https://sql-designer.com/images/screenshot.png",
        "url": "https://sql-designer.com/blog/mysql-foreign-key",
        "datePublished": "2026-03-19",
        "dateModified": "2026-03-24",
        "author": { "@type": "Organization", "name": "SQL Designer" },
        "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
    }
    ]
    </script>
    <style>
        body { overflow-y: auto; }
        .blog-post { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 5rem; }
        .blog-post .breadcrumb { font-size: 0.875rem; color: #aaa; text-transform: none; margin-bottom: 1.5rem; }
        .blog-post .breadcrumb a { color: var(--color-primary); text-decoration: none; }
        .blog-post .post-meta { font-size: 0.875rem; color: #aaa; text-transform: none; margin-bottom: 1rem; }
        .blog-post h1 { font-size: 1.6rem; text-transform: uppercase; letter-spacing: 0.04em; color: #1e293b; margin: 0 0 1rem; line-height: 1.3; }
        .blog-post .intro { font-size: 1rem; color: #444; text-transform: none; line-height: 1.8; margin-bottom: 2.5rem; border-left: 3px solid var(--color-primary); padding-left: 1.2rem; }
        .blog-post h2 { font-size: 1.05rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary); margin: 2.5rem 0 0.8rem; }
        .blog-post p { font-size: 0.9rem; color: #444; text-transform: none; line-height: 1.8; margin: 0 0 1rem; }
        .blog-post ul, .blog-post ol { margin: 0 0 1rem 1.5rem; padding: 0; }
        .blog-post li { font-size: 0.9rem; color: #444; text-transform: none; line-height: 1.8; margin-bottom: 0.3rem; }
        .blog-post code { background: #f1f5f9; padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.85em; color: #1e293b; }
        .blog-post pre { background: #1e293b; color: #e2e8f0; border-radius: 6px; padding: 1.2rem 1.5rem; overflow-x: auto; margin: 1rem 0 1.5rem; font-size: 0.875rem; line-height: 1.6; }
        .blog-post pre code { background: none; padding: 0; color: inherit; font-size: inherit; }
        .blog-post .cta-box { background: var(--color-primary); color: #fff; border-radius: 6px; padding: 2rem; text-align: center; margin-top: 3rem; }
        .blog-post .cta-box h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.8rem; }
        .blog-post .cta-box p { color: rgba(255,255,255,0.85); margin: 0 0 1.2rem; font-size: 0.85rem; }
        .blog-post .btn-cta { background: #fff; color: var(--color-primary); padding: 0.6rem 1.8rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; }
    </style>
@endsection

@section('content')
<article class="blog-post">
    <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; MySQL</p>
    <p class="post-meta">March 2026 &mdash; 6 min read</p>
    <h1>MySQL Foreign Key — Syntax, Examples, and Best Practices</h1>

    <p class="intro">
        Foreign keys are the mechanism that turns a collection of isolated tables into a relational database. They enforce that relationships between rows are always valid, preventing orphaned records and data integrity bugs. This guide covers everything you need to know to use them correctly in MySQL.
    </p>

    <h2>What Is a Foreign Key?</h2>
    <p>
        A foreign key is a column (or group of columns) in one table that references the primary key of another table. It creates a constraint: MySQL will reject any insert or update that would create a reference to a row that doesn't exist in the parent table.
    </p>
    <p>
        For example: if you have an <code>orders</code> table with a <code>user_id</code> column, a foreign key constraint ensures that every <code>user_id</code> in <code>orders</code> corresponds to a real row in the <code>users</code> table.
    </p>

    <h2>Basic Syntax</h2>
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

    <h2>ON DELETE and ON UPDATE Options</h2>
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

    <h2>A Practical Example: E-commerce Schema</h2>
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

    <h2>Common Mistakes</h2>
    <ul>
        <li><strong>Mismatched data types.</strong> The child column and parent column must have exactly the same type, including sign. An <code>INT UNSIGNED</code> primary key requires an <code>INT UNSIGNED</code> foreign key — plain <code>INT</code> won't work.</li>
        <li><strong>Missing index on the child column.</strong> MySQL requires an index on the foreign key column. If you don't create one explicitly, MySQL creates one automatically — but it's good practice to be explicit.</li>
        <li><strong>Using MyISAM instead of InnoDB.</strong> Foreign key constraints are only enforced on InnoDB tables. MyISAM silently ignores them.</li>
        <li><strong>Circular dependencies.</strong> Be careful when two tables reference each other. Use <code>SET NULL</code> or carefully order inserts/deletes to avoid constraint violations.</li>
    </ul>

    <h2>Visualise Foreign Keys Before Writing DDL</h2>
    <p>
        For anything beyond a few tables, it's much easier to design your relationships visually first and generate the SQL from the diagram. Drawing the lines between tables makes cascade behaviour and cardinality immediately obvious — mistakes that would take hours to debug in raw SQL are visible at a glance.
    </p>

    <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid #e5e7eb;">
        <p style="font-size:0.875rem; text-transform:uppercase; letter-spacing:0.06em; color:#aaa; margin:0 0 0.8rem;">Related Articles</p>
        <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
            <li><a href="/blog/how-to-design-mysql-database-schema" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">How to Design a MySQL Database Schema &rarr;</a></li>
            <li><a href="/blog/er-diagram-tool-online" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Free ER Diagram Tool Online for MySQL &rarr;</a></li>
            <li><a href="/blog/mysql-data-types" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">MySQL Data Types Explained &rarr;</a></li>
        </ul>
    </nav>

    <div class="cta-box">
        <h3>Design foreign key relationships visually</h3>
        <p>SQL Designer lets you draw foreign key lines between tables and generates the correct MySQL DDL automatically. Free, no installation required.</p>
        <a class="btn-cta" href="/register">Create a Free Account</a>
    </div>
</article>
@endsection
