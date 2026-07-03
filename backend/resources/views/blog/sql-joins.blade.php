@extends('layouts.main')

@section('title', 'SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL')

@section('head')
    <meta name="description"
          content="SQL is used by 58.6% of developers (Stack Overflow, 2025). Learn INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax with examples, NULL handling, and index tips.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/sql-joins">
    <meta property="og:title" content="SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL">
    <meta property="og:description"
          content="SQL is used by 58.6% of developers (Stack Overflow, 2025). Learn INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax with examples, NULL handling, and index tips.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/sql-joins">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL">
    <meta name="twitter:description" content="SQL is used by 58.6% of developers (Stack Overflow, 2025). Learn INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax with examples, NULL handling, and index tips.">
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
                    { "@type": "ListItem", "position": 3, "name": "SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL", "item": "https://sql-designer.com/blog/sql-joins" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "SQL JOIN Types Explained — INNER, LEFT, RIGHT, FULL",
                "description": "SQL is used by 58.6% of developers (Stack Overflow, 2025). Learn INNER, LEFT, RIGHT, FULL, and CROSS JOIN syntax with examples, NULL handling, and index tips.",
                "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
                "url": "https://sql-designer.com/blog/sql-joins",
                "datePublished": "2026-07-03",
                "dateModified": "2026-07-03",
                "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
                "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
                "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/sql-joins" }
            },
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "What is the difference between INNER JOIN and LEFT JOIN?",
                        "acceptedAnswer": { "@type": "Answer", "text": "INNER JOIN returns only rows that have a match in both tables — non-matching rows are dropped entirely. LEFT JOIN returns every row from the left table regardless of a match, filling unmatched right-table columns with NULL. Use LEFT JOIN when you need to keep 'orphan' rows, such as customers with zero orders." }
                    },
                    {
                        "@type": "Question",
                        "name": "How many types of SQL joins are there?",
                        "acceptedAnswer": { "@type": "Answer", "text": "The core types are INNER, LEFT (OUTER), RIGHT (OUTER), FULL (OUTER), and CROSS JOIN. SELF JOIN is not a distinct SQL keyword — it is any of the above join types applied to a table joined with itself, typically used for hierarchical or comparison queries." }
                    },
                    {
                        "@type": "Question",
                        "name": "Does MySQL support FULL OUTER JOIN?",
                        "acceptedAnswer": { "@type": "Answer", "text": "No. MySQL has no native FULL OUTER JOIN keyword, unlike PostgreSQL, Oracle, and SQL Server. The standard workaround is a LEFT JOIN UNION a RIGHT JOIN (or UNION ALL with a WHERE NULL filter on the second half) to simulate the same result set." }
                    },
                    {
                        "@type": "Question",
                        "name": "Why does a JOIN return duplicate rows?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Duplicate rows almost always mean the join condition matches more than one row on the other side — a one-to-many or many-to-many relationship that wasn't accounted for. Check for a missing filter, a non-unique join column, or an accidental CROSS JOIN caused by omitting the ON clause." }
                    },
                    {
                        "@type": "Question",
                        "name": "Are JOINs slow without indexes?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Yes. Without an index on the join column, the database engine often falls back to a nested loop scan that checks every row pair, which scales poorly past a few thousand rows. Indexing both sides of a join column typically lets the planner switch to a hash or merge join, cutting execution time by orders of magnitude on large tables." }
                    }
                ]
            }
            ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>SQL Joins</span></p>
        <p class="post-eyebrow">July 2026 · <time datetime="2026-07-03">Last updated: July 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 9 min read</p>
        <h1 class="page-h1">SQL JOIN Types Explained — INNER, LEFT, RIGHT, and FULL</h1>
        <p class="page-sub">A SQL <code>JOIN</code> combines rows from two or more tables based on a related column, and it's the single most-used operation for turning a normalized schema back into usable, denormalized query results. There are five core join types — <code>INNER</code>, <code>LEFT</code>, <code>RIGHT</code>, <code>FULL</code>, and <code>CROSS</code> — plus the <code>SELF JOIN</code> pattern, each returning a different row set when a match is missing on one side. This guide covers syntax, NULL behavior, a worked example, join algorithms, and the mistakes that cause duplicate or missing rows.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-is-a-sql-join">What Is a SQL JOIN?</a></li>
            <li><a href="#inner-join">INNER JOIN</a></li>
            <li><a href="#left-and-right-join">LEFT and RIGHT JOIN</a></li>
            <li><a href="#full-outer-join">FULL OUTER JOIN</a></li>
            <li><a href="#cross-join-and-self-join">CROSS JOIN and SELF JOIN</a></li>
            <li><a href="#how-the-database-executes-a-join">How Joins Execute</a></li>
            <li><a href="#common-join-mistakes">Common Mistakes</a></li>
            <li><a href="#visualise-joins-before-writing-sql">Visualise First</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li><code>INNER JOIN</code> keeps only matching rows; <code>LEFT</code>, <code>RIGHT</code>, and <code>FULL JOIN</code> keep unmatched rows and fill the gap with <code>NULL</code>.</li>
                <li>SQL is the <strong>third most-used language</strong> among all developers at <strong>58.6%</strong>, behind only JavaScript and HTML/CSS (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow Developer Survey 2025</a>).</li>
                <li>MySQL has <strong>no native <code>FULL OUTER JOIN</code></strong> — you simulate it with a <code>LEFT JOIN UNION RIGHT JOIN</code>.</li>
                <li>Unindexed join columns force a nested loop scan; indexing both sides lets the planner switch to a hash or merge join instead.</li>
            </ul>
        </div>

        <figure>
            <img src="https://images.pexels.com/photos/3803517/pexels-photo-3803517.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Overlapping translucent panes of glass representing how a SQL join overlays rows from two tables to produce a combined result set"
                 loading="lazy" width="1260" height="750">
            <figcaption>A JOIN overlays two row sets on a shared key — how much of each side survives depends on the join type. (Photo: Pexels)</figcaption>
        </figure>

        <h2 id="what-is-a-sql-join">What Is a SQL JOIN?</h2>
        <p>
            A SQL <code>JOIN</code> combines rows from two or more tables using a related column, typically a primary key on one side and a foreign key on the other. In 2025, SQL ranked as the third most-used programming language overall at 58.6% of respondents, trailing only JavaScript and HTML/CSS (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow Developer Survey 2025</a>). Joins are the mechanism that makes normalized schema design practical — you split data across tables to avoid duplication, then join them back together at query time.
        </p>
        <p>
            Without joins, every query against a normalized schema would require multiple round trips and manual merging in application code. That's slower and more error-prone than letting the database engine do the work in one pass. Why does that matter for schema design? Because a schema with too many tables and no clear join paths becomes painful to query, no matter how "correct" the normalization is on paper.
        </p>
        <p>
            The five core join types differ only in which unmatched rows they keep. <code>INNER JOIN</code> drops them. <code>LEFT</code>, <code>RIGHT</code>, and <code>FULL JOIN</code> keep them from one or both sides and pad the missing columns with <code>NULL</code>. <code>CROSS JOIN</code> ignores matching entirely and returns every possible row combination.
        </p>

        <figure style="margin: 1.2rem 0 1.8rem;">
            <figcaption style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.55rem; font-family: 'JetBrains Mono', monospace;">Most-used programming languages among developers — Stack Overflow Developer Survey 2025</figcaption>
            <svg viewBox="0 0 540 170" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Horizontal bar chart showing programming language usage: JavaScript 66%, HTML/CSS 61.9%, SQL 58.6%">
                <rect width="540" height="170" rx="8" fill="#181f2e"/>
                <text x="102" y="43" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">JavaScript</text>
                <text x="102" y="87" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">HTML/CSS</text>
                <text x="102" y="131" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">SQL</text>
                <!-- Bar area 108..500 = 392px. Max 66% => scale = 392/66 = 5.94 -->
                <rect x="108" y="25" width="392" height="22" rx="3" fill="#22c55e" opacity="0.82"/>
                <text x="499" y="40" text-anchor="end" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">66.0%</text>
                <rect x="108" y="69" width="368" height="22" rx="3" fill="#475569" opacity="0.9"/>
                <text x="476" y="84" text-anchor="end" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">61.9%</text>
                <rect x="108" y="113" width="348" height="22" rx="3" fill="#3b82f6" opacity="0.85"/>
                <text x="456" y="128" text-anchor="end" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">58.6%</text>
            </svg>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; font-style: italic;">Source: <a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener" style="color: var(--text-muted);">Stack Overflow Developer Survey 2025</a></p>
        </figure>

        <div class="citation-capsule">
            SQL ranks third among all programming languages at 58.6% usage, behind JavaScript (66%) and HTML/CSS (61.9%) (<a href="https://survey.stackoverflow.co/2025/technology" target="_blank" rel="noopener">Stack Overflow Developer Survey 2025</a>). Joins are the operation developers reach for most often once they're inside SQL, since almost no production schema fits in a single table.
        </div>

        <h2 id="inner-join">INNER JOIN</h2>
        <p>
            <code>INNER JOIN</code> returns only the rows where the join condition matches on both sides. Any row in either table without a corresponding match is excluded from the result entirely. This is the default join type — plain <code>JOIN</code> without a keyword means <code>INNER JOIN</code> in every major SQL dialect.
        </p>
        <pre><code>SELECT orders.id, customers.name, orders.total
FROM orders
INNER JOIN customers ON orders.customer_id = customers.id;</code></pre>
        <p>
            This query returns only orders that have a matching customer row. If <code>customer_id</code> is <code>NULL</code> or references a customer that was deleted, that order simply won't appear in the results. That's usually what you want for reporting, but it's a common source of confusion when row counts don't match expectations — the missing rows aren't a bug, they're rows with no match.
        </p>

        <div class="citation-capsule">
            <code>INNER JOIN</code> is the SQL standard default: writing <code>JOIN</code> without a modifier is functionally identical to <code>INNER JOIN</code> across MySQL, PostgreSQL, SQL Server, and Oracle. It returns the intersection of two row sets on the join key — rows present in only one table are silently excluded, never padded with <code>NULL</code>.
        </div>

        <h2 id="left-and-right-join">LEFT and RIGHT JOIN</h2>
        <p>
            <code>LEFT JOIN</code> (also written <code>LEFT OUTER JOIN</code>) returns every row from the left table, whether or not it has a match, padding unmatched right-table columns with <code>NULL</code>. This preserves "orphan" rows that an <code>INNER JOIN</code> would silently drop — customers with zero orders, products never purchased, users who never logged in.
        </p>
        <pre><code>SELECT customers.name, orders.id AS order_id
FROM customers
LEFT JOIN orders ON customers.id = orders.customer_id;</code></pre>
        <p>
            Every customer appears at least once, even those with no orders — <code>order_id</code> is <code>NULL</code> for them. To find customers who have never ordered, add <code>WHERE orders.id IS NULL</code> after the join. That pattern, called an anti-join, is one of the most practical uses of <code>LEFT JOIN</code>.
        </p>
        <p>
            <code>RIGHT JOIN</code> is the mirror image: it keeps every row from the right table instead. In practice, most developers use <code>LEFT JOIN</code> exclusively and reorder the tables rather than switch to <code>RIGHT JOIN</code>, since it reads more naturally left-to-right and keeps queries consistent across a codebase.
        </p>

        <figure>
            <img src="https://images.unsplash.com/photo-1489875347897-49f64b51c1f8?fm=jpg&q=60&w=1600&auto=format&fit=crop"
                 alt="Rows of connected data points on a dark background illustrating how a LEFT JOIN preserves unmatched rows from the primary table"
                 loading="lazy" width="1600" height="900">
            <figcaption>LEFT JOIN keeps every row on the preserved side, filling gaps with NULL instead of dropping the row.</figcaption>
        </figure>

        <div class="citation-capsule">
            <code>LEFT JOIN</code> preserves every row from the left table regardless of a match, filling unmatched right-side columns with <code>NULL</code> — the standard pattern for finding rows with no related record via <code>WHERE right_table.id IS NULL</code>. <code>RIGHT JOIN</code> does the identical operation with the table roles reversed, but most style guides prefer rewriting a <code>RIGHT JOIN</code> as a <code>LEFT JOIN</code> with swapped table order for readability.
        </div>

        <h2 id="full-outer-join">FULL OUTER JOIN</h2>
        <p>
            <code>FULL OUTER JOIN</code> returns every row from both tables, matching where possible and padding with <code>NULL</code> on whichever side lacks a match. It's the union of what <code>LEFT JOIN</code> and <code>RIGHT JOIN</code> would each return separately. PostgreSQL, SQL Server, and Oracle all support it natively with the <code>FULL JOIN</code> keyword.
        </p>
        <pre><code>-- PostgreSQL, SQL Server, Oracle
SELECT customers.name, orders.id AS order_id
FROM customers
FULL OUTER JOIN orders ON customers.id = orders.customer_id;</code></pre>
        <p>
            MySQL has no native <code>FULL OUTER JOIN</code>. The standard workaround unions a <code>LEFT JOIN</code> with a <code>RIGHT JOIN</code> (or a second <code>LEFT JOIN</code> with the tables swapped), deduplicating the overlap:
        </p>
        <pre><code>-- MySQL workaround
SELECT customers.name, orders.id AS order_id
FROM customers LEFT JOIN orders ON customers.id = orders.customer_id
UNION
SELECT customers.name, orders.id AS order_id
FROM customers RIGHT JOIN orders ON customers.id = orders.customer_id;</code></pre>
        <p>
            <code>UNION</code> (not <code>UNION ALL</code>) removes the duplicate rows that both halves of the query would otherwise produce for every matched row. This behavior — and other core syntax differences between dialects — is covered in more depth in the <a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL comparison</a>.
        </p>

        <div class="citation-capsule">
            MySQL is the only major relational database among the top five most-used engines that lacks a native <code>FULL OUTER JOIN</code> keyword — PostgreSQL, SQL Server, and Oracle all support it directly. The standard MySQL workaround unions a <code>LEFT JOIN</code> and a <code>RIGHT JOIN</code> against the same pair of tables, using <code>UNION</code> rather than <code>UNION ALL</code> to drop the duplicated matched rows.
        </div>

        <h2 id="cross-join-and-self-join">CROSS JOIN and SELF JOIN</h2>
        <p>
            <code>CROSS JOIN</code> returns the Cartesian product of two tables — every row from the first table paired with every row from the second, with no matching condition at all. A 100-row table crossed with a 50-row table produces 5,000 result rows. It's intentional for generating combinations (sizes × colors for a product catalog) and accidental almost everywhere else.
        </p>
        <pre><code>SELECT sizes.label, colors.name
FROM sizes
CROSS JOIN colors;</code></pre>
        <p>
            <code>SELF JOIN</code> isn't a separate keyword — it's any join type applied to a table joined with itself, using table aliases to distinguish the two roles. It's the standard pattern for hierarchical data, like an <code>employees</code> table where each row has a <code>manager_id</code> pointing to another row in the same table.
        </p>
        <pre><code>SELECT e.name AS employee, m.name AS manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.id;</code></pre>
        <p>
            The most common accidental <code>CROSS JOIN</code> in production code isn't written on purpose — it happens when a developer lists two tables in a <code>WHERE</code>-style old syntax (<code>FROM a, b</code>) and forgets the filtering condition entirely. Modern SQL style guides ban comma-joins for exactly this reason: an explicit <code>JOIN ... ON</code> makes a missing condition a syntax error instead of a silent Cartesian product.
        </p>

        <h2 id="how-the-database-executes-a-join">How the Database Executes a Join</h2>
        <p>
            PostgreSQL chooses between three join algorithms at query planning time — nested loop, hash join, and merge join — and the choice depends heavily on whether the join columns are indexed (<a href="https://www.cybertec-postgresql.com/en/join-strategies-and-performance-in-postgresql/" target="_blank" rel="noopener">CYBERTEC PostgreSQL, Join Strategies and Performance</a>). Nested loop scans one table row by row, checking the other table for each match — fine for small tables, expensive as row counts grow.
        </p>
        <p>
            Hash join builds an in-memory hash table from the smaller table, then streams the larger table through it, which is efficient as long as the hash table fits in the configured working memory. Merge join sorts both inputs on the join key first, then walks them in parallel — fast when the data is already sorted via an existing index, expensive when PostgreSQL has to sort from scratch.
        </p>
        <p>
            You can see which algorithm the planner picked with <code>EXPLAIN ANALYZE</code>, which executes the query and reports both the estimated and actual cost of each plan node (<a href="https://www.postgresql.org/docs/current/using-explain.html" target="_blank" rel="noopener">PostgreSQL Documentation, Using EXPLAIN</a>):
        </p>
        <pre><code>EXPLAIN ANALYZE
SELECT orders.id, customers.name
FROM orders
JOIN customers ON orders.customer_id = customers.id;</code></pre>
        <p>
            The single highest-leverage fix for a slow join, in practice, is almost always a missing index on the foreign key column — not a query rewrite. Once both sides of a join column are indexed, the planner has the option to switch away from nested loop, and query time on multi-million-row tables routinely drops from seconds to milliseconds.
        </p>

        <div class="citation-capsule">
            PostgreSQL selects between nested loop, hash, and merge join at plan time based on table size, available memory, and existing sort order (<a href="https://www.cybertec-postgresql.com/en/join-strategies-and-performance-in-postgresql/" target="_blank" rel="noopener">CYBERTEC PostgreSQL</a>). Indexing the join columns on both sides is the most reliable way to give the planner cheaper options than a full nested loop scan, and <code>EXPLAIN ANALYZE</code> is the standard tool for confirming which algorithm actually ran (<a href="https://www.postgresql.org/docs/current/using-explain.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>).
        </div>

        <h2 id="common-join-mistakes">Common Join Mistakes</h2>
        <ul>
            <li><strong>Forgetting the <code>ON</code> clause.</strong> Omitting the join condition turns any join into an accidental <code>CROSS JOIN</code>, producing a row count that looks like an explosion rather than a bug.</li>
            <li><strong>Confusing <code>WHERE</code> and <code>ON</code> with outer joins.</strong> Filtering a <code>LEFT JOIN</code>'s right-table column in <code>WHERE</code> instead of <code>ON</code> silently converts it back into an <code>INNER JOIN</code>, since <code>NULL</code> rows fail most <code>WHERE</code> conditions.</li>
            <li><strong>Not indexing the join column.</strong> A missing index on either side of the join condition forces a nested loop scan, which scales badly once tables pass a few thousand rows. See the <a href="/blog/mysql-foreign-key">foreign key guide</a> for how MySQL indexes FK columns automatically.</li>
            <li><strong>Unexpected duplicate rows.</strong> Joining against a table where the join column isn't unique multiplies matching rows — always confirm cardinality (one-to-one, one-to-many, many-to-many) before joining, ideally at schema design time.</li>
            <li><strong>Chaining too many joins without checking the plan.</strong> Five or more joined tables in one query can produce a plan the optimizer struggles to estimate accurately. Break the query apart or add targeted indexes once <code>EXPLAIN ANALYZE</code> shows a mismatch between estimated and actual rows.</li>
        </ul>

        <h2 id="visualise-joins-before-writing-sql">Visualise Joins Before Writing SQL</h2>
        <p>
            Join bugs are cardinality bugs in disguise — a query that "should" return one row per customer but returns three is almost always a many-to-many relationship nobody modeled explicitly. Drawing the schema first makes that relationship visible before it becomes a production incident.
        </p>
        <p>
            The <a href="/blog/crowfoot-notation">crow's foot notation guide</a> explains how to read the cardinality symbols that predict exactly how many rows a join will return. Once the relationships are mapped, you can <a href="/demo">design your schema visually</a> and export the correct <code>CREATE TABLE</code> and foreign key DDL directly, so every join you write later has a clear, indexed path to follow.
        </p>
        <p>
            For a broader library of schema patterns with the joins already worked out, see the <a href="/blog/database-schema-examples">database schema examples post</a>, and the <a href="/blog/database-normalization">normalization guide</a> for when splitting tables (and therefore requiring more joins) actually pays off.
        </p>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3 class="faq-q">What is the difference between INNER JOIN and LEFT JOIN?</h3>
                <p class="faq-a"><code>INNER JOIN</code> returns only rows that have a match in both tables — non-matching rows are dropped entirely. <code>LEFT JOIN</code> returns every row from the left table regardless of a match, filling unmatched right-table columns with <code>NULL</code>. Use <code>LEFT JOIN</code> when you need to keep "orphan" rows, such as customers with zero orders.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How many types of SQL joins are there?</h3>
                <p class="faq-a">The core types are <code>INNER</code>, <code>LEFT (OUTER)</code>, <code>RIGHT (OUTER)</code>, <code>FULL (OUTER)</code>, and <code>CROSS JOIN</code>. <code>SELF JOIN</code> is not a distinct keyword — it's any of the above join types applied to a table joined with itself, typically used for hierarchical or comparison queries.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Does MySQL support FULL OUTER JOIN?</h3>
                <p class="faq-a">No. MySQL has no native <code>FULL OUTER JOIN</code> keyword, unlike PostgreSQL, Oracle, and SQL Server. The standard workaround is a <code>LEFT JOIN UNION</code> a <code>RIGHT JOIN</code> (or <code>UNION ALL</code> with a <code>WHERE NULL</code> filter on the second half) to simulate the same result set.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Why does a JOIN return duplicate rows?</h3>
                <p class="faq-a">Duplicate rows almost always mean the join condition matches more than one row on the other side — a one-to-many or many-to-many relationship that wasn't accounted for. Check for a missing filter, a non-unique join column, or an accidental <code>CROSS JOIN</code> caused by omitting the <code>ON</code> clause.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Are JOINs slow without indexes?</h3>
                <p class="faq-a">Yes. Without an index on the join column, the database engine often falls back to a nested loop scan that checks every row pair, which scales poorly past a few thousand rows. Indexing both sides of a join column typically lets the planner switch to a hash or merge join, cutting execution time by orders of magnitude on large tables.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax, Examples, and Best Practices &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization: 1NF, 2NF, and 3NF &rarr;</a></li>
                <li><a href="/blog/crowfoot-notation">Crow's Foot Notation Explained &rarr;</a></li>
                <li><a href="/blog/database-schema-examples">Database Schema Examples &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences &rarr;</a></li>
                <li><a href="/blog/mysql-data-types">MySQL Data Types Explained &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">Free Online ER Diagram Maker &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design join-ready schemas visually</h2>
    <p>SQL Designer lets you draw foreign key relationships between tables and generates correct, indexed DDL automatically — so every join you write later has a clean path to follow. Free, no installation required.</p>
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
