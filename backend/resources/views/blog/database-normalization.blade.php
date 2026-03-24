@extends('layouts.main')

@section('title', 'Database Normalization Explained — 1NF, 2NF, and 3NF with Examples')

@section('head')
    <meta name="description" content="Learn database normalization with clear examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/database-normalization">
    <meta property="og:title" content="Database Normalization Explained — 1NF, 2NF, and 3NF with Examples">
    <meta property="og:description" content="Learn database normalization with clear examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/database-normalization">
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
            { "@type": "ListItem", "position": 3, "name": "Database Normalization Explained", "item": "https://sql-designer.com/blog/database-normalization" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "Database Normalization Explained — 1NF, 2NF, and 3NF with Examples",
        "description": "Learn database normalization with clear examples. Understand 1NF, 2NF, and 3NF, why they matter, and when it's acceptable to denormalize.",
        "url": "https://sql-designer.com/blog/database-normalization",
        "datePublished": "2026-03-19",
        "author": { "@type": "Organization", "name": "SQL Designer" },
        "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" }
    }
    ]
    </script>
    <style>
        body { overflow-y: auto; }
        .blog-post { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 5rem; }
        .blog-post .breadcrumb { font-size: 0.8rem; color: #aaa; text-transform: none; margin-bottom: 1.5rem; }
        .blog-post .breadcrumb a { color: var(--color-primary); text-decoration: none; }
        .blog-post .post-meta { font-size: 0.8rem; color: #aaa; text-transform: none; margin-bottom: 1rem; }
        .blog-post h1 { font-size: 1.6rem; text-transform: uppercase; letter-spacing: 0.04em; color: #1e293b; margin: 0 0 1rem; line-height: 1.3; }
        .blog-post .intro { font-size: 1rem; color: #444; text-transform: none; line-height: 1.8; margin-bottom: 2.5rem; border-left: 3px solid var(--color-primary); padding-left: 1.2rem; }
        .blog-post h2 { font-size: 1.05rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-primary); margin: 2.5rem 0 0.8rem; }
        .blog-post h3 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.04em; color: #1e293b; margin: 1.5rem 0 0.5rem; }
        .blog-post p { font-size: 0.9rem; color: #444; text-transform: none; line-height: 1.8; margin: 0 0 1rem; }
        .blog-post ul, .blog-post ol { margin: 0 0 1rem 1.5rem; padding: 0; }
        .blog-post li { font-size: 0.9rem; color: #444; text-transform: none; line-height: 1.8; margin-bottom: 0.3rem; }
        .blog-post code { background: #f1f5f9; padding: 0.1em 0.4em; border-radius: 3px; font-size: 0.85em; color: #1e293b; }
        .blog-post pre { background: #1e293b; color: #e2e8f0; border-radius: 6px; padding: 1.2rem 1.5rem; overflow-x: auto; margin: 1rem 0 1.5rem; font-size: 0.82rem; line-height: 1.6; }
        .blog-post pre code { background: none; padding: 0; color: inherit; font-size: inherit; }
        .blog-post table { width: 100%; border-collapse: collapse; margin: 0 0 1.5rem; font-size: 0.85rem; }
        .blog-post th { background: #1e293b; color: #e2e8f0; padding: 0.6rem 1rem; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .blog-post td { padding: 0.55rem 1rem; border-bottom: 1px solid #e5e7eb; color: #444; }
        .blog-post tr:nth-child(even) td { background: #f9f9f9; }
        .blog-post .label-bad { color: #dc2626; font-weight: bold; font-size: 0.75rem; text-transform: uppercase; }
        .blog-post .label-good { color: #16a34a; font-weight: bold; font-size: 0.75rem; text-transform: uppercase; }
        .blog-post .cta-box { background: var(--color-primary); color: #fff; border-radius: 6px; padding: 2rem; text-align: center; margin-top: 3rem; }
        .blog-post .cta-box h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.8rem; }
        .blog-post .cta-box p { color: rgba(255,255,255,0.85); margin: 0 0 1.2rem; font-size: 0.85rem; }
        .blog-post .btn-cta { background: #fff; color: var(--color-primary); padding: 0.6rem 1.8rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; }
    </style>
@endsection

@section('content')
<article class="blog-post">
    <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; Schema Design</p>
    <p class="post-meta">March 2026 &mdash; 8 min read</p>
    <h1>Database Normalization Explained — 1NF, 2NF, and 3NF with Examples</h1>

    <p class="intro">
        Normalization is the process of structuring a database schema to reduce data redundancy and improve integrity. A poorly normalized schema stores the same data in multiple places — meaning updates have to happen in multiple rows, and inconsistencies are inevitable. This guide walks through the first three normal forms with concrete before-and-after examples.
    </p>

    <h2>Why Normalization Matters</h2>
    <p>Consider a single <code>orders</code> table that stores everything:</p>
    <table>
        <tr><th>order_id</th><th>customer_name</th><th>customer_email</th><th>product</th><th>product_price</th></tr>
        <tr><td>1</td><td>Alice</td><td>alice@example.com</td><td>Widget</td><td>9.99</td></tr>
        <tr><td>2</td><td>Alice</td><td>alice@example.com</td><td>Gadget</td><td>24.99</td></tr>
        <tr><td>3</td><td>Bob</td><td>bob@example.com</td><td>Widget</td><td>9.99</td></tr>
    </table>
    <p>
        Problems: Alice's email is stored twice — if it changes, you must update every row. The Widget price is stored twice — a price change requires finding every row that references it. This is the kind of redundancy normalization eliminates.
    </p>

    <h2>First Normal Form (1NF)</h2>
    <p><strong>Rule:</strong> Every column must hold a single, atomic value. No repeating groups, no comma-separated lists in a cell.</p>

    <h3>Violation example</h3>
    <table>
        <tr><th>order_id</th><th>products</th></tr>
        <tr><td>1</td><td>Widget, Gadget, Sprocket</td></tr>
    </table>
    <p class="label-bad">✗ Not in 1NF</p>
    <p>The <code>products</code> column contains multiple values. You can't query "all orders containing a Widget" without a <code>LIKE</code> hack.</p>

    <h3>Fixed</h3>
    <table>
        <tr><th>order_id</th><th>product</th></tr>
        <tr><td>1</td><td>Widget</td></tr>
        <tr><td>1</td><td>Gadget</td></tr>
        <tr><td>1</td><td>Sprocket</td></tr>
    </table>
    <p class="label-good">✓ In 1NF</p>
    <p>Each row holds one value. The table now has a composite primary key of <code>(order_id, product)</code>.</p>

    <h2>Second Normal Form (2NF)</h2>
    <p><strong>Rule:</strong> The table must be in 1NF, and every non-key column must depend on the <em>entire</em> primary key — not just part of it. This only applies to tables with composite primary keys.</p>

    <h3>Violation example</h3>
    <table>
        <tr><th>order_id</th><th>product_id</th><th>quantity</th><th>product_name</th><th>product_price</th></tr>
        <tr><td>1</td><td>42</td><td>2</td><td>Widget</td><td>9.99</td></tr>
        <tr><td>2</td><td>42</td><td>1</td><td>Widget</td><td>9.99</td></tr>
    </table>
    <p class="label-bad">✗ Not in 2NF</p>
    <p>The primary key is <code>(order_id, product_id)</code>. But <code>product_name</code> and <code>product_price</code> depend only on <code>product_id</code> — not on the full composite key. They're stored redundantly in every order line.</p>

    <h3>Fixed — split into two tables</h3>
    <pre><code>-- order_items: only order-specific data
order_id | product_id | quantity

-- products: product data lives here once
product_id | product_name | product_price</code></pre>
    <p class="label-good">✓ In 2NF</p>
    <p>Now <code>product_name</code> and <code>product_price</code> are stored once in <code>products</code>. A price change updates one row.</p>

    <h2>Third Normal Form (3NF)</h2>
    <p><strong>Rule:</strong> The table must be in 2NF, and no non-key column should depend on another non-key column (no transitive dependencies).</p>

    <h3>Violation example</h3>
    <table>
        <tr><th>employee_id</th><th>department_id</th><th>department_name</th></tr>
        <tr><td>1</td><td>10</td><td>Engineering</td></tr>
        <tr><td>2</td><td>10</td><td>Engineering</td></tr>
        <tr><td>3</td><td>20</td><td>Marketing</td></tr>
    </table>
    <p class="label-bad">✗ Not in 3NF</p>
    <p><code>department_name</code> depends on <code>department_id</code>, not on <code>employee_id</code>. It's a transitive dependency through a non-key column. Renaming the department requires updating every employee row.</p>

    <h3>Fixed — extract the dependency</h3>
    <pre><code>-- employees
employee_id | department_id

-- departments
department_id | department_name</code></pre>
    <p class="label-good">✓ In 3NF</p>
    <p>Department names live in one place. Renaming "Engineering" is a single row update.</p>

    <h2>When to Denormalize</h2>
    <p>
        3NF is the right default for transactional databases. But sometimes you'll deliberately break the rules for performance:
    </p>
    <ul>
        <li><strong>Reporting and analytics</strong> — denormalized "wide" tables avoid expensive joins across many tables in read-heavy workloads.</li>
        <li><strong>Caching derived values</strong> — storing a pre-computed <code>order_total</code> avoids summing <code>order_items</code> on every page load, at the cost of keeping it in sync.</li>
        <li><strong>Historical snapshots</strong> — sometimes you <em>want</em> to store the product price at the time of purchase, not the current price. Denormalization is correct here by design.</li>
    </ul>
    <p>
        The key principle: normalize first, then denormalize deliberately and with documentation. Never denormalize out of laziness.
    </p>

    <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid #e5e7eb;">
        <p style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em; color:#aaa; margin:0 0 0.8rem;">Related Articles</p>
        <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
            <li><a href="/blog/how-to-design-mysql-database-schema" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">How to Design a MySQL Database Schema &rarr;</a></li>
            <li><a href="/blog/mysql-foreign-key" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
            <li><a href="/blog/how-to-draw-er-diagram" style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">How to Draw an ER Diagram Step by Step &rarr;</a></li>
        </ul>
    </nav>

    <div class="cta-box">
        <h3>Visualize your normalized schema</h3>
        <p>SQL Designer makes it easy to split tables correctly and draw the foreign key relationships between them. Free, browser-based, no installation required.</p>
        <a class="btn-cta" href="/register">Create a Free Account</a>
    </div>
</article>
@endsection
