@extends('layouts.main')

@section('title', 'MySQL Indexes Explained — B-Tree, Composite, EXPLAIN')

@section('head')
    <meta name="description"
          content="MySQL indexes cut lookups from full table scans to 2-3 B-tree page reads. Learn CREATE INDEX syntax, composite indexes, and EXPLAIN diagnostics.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/mysql-indexes">
    <meta property="og:title" content="MySQL Indexes Explained — B-Tree, Composite, EXPLAIN">
    <meta property="og:description"
          content="MySQL indexes cut lookups from full table scans to 2-3 B-tree page reads. Learn CREATE INDEX syntax, composite indexes, and EXPLAIN diagnostics.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/mysql-indexes">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="MySQL Indexes Explained — B-Tree, Composite, EXPLAIN">
    <meta name="twitter:description" content="MySQL indexes cut lookups from full table scans to 2-3 B-tree page reads. Learn CREATE INDEX syntax, composite indexes, and EXPLAIN diagnostics.">
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
                    { "@type": "ListItem", "position": 3, "name": "MySQL Indexes Explained — B-Tree, Composite, EXPLAIN", "item": "https://sql-designer.com/blog/mysql-indexes" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "MySQL Indexes Explained — B-Tree, Composite, EXPLAIN",
                "description": "MySQL indexes cut lookups from full table scans to 2-3 B-tree page reads. Learn CREATE INDEX syntax, composite indexes, and EXPLAIN diagnostics.",
                "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
                "url": "https://sql-designer.com/blog/mysql-indexes",
                "datePublished": "2026-07-03",
                "dateModified": "2026-07-03",
                "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
                "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
                "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/mysql-indexes" }
            },
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "What is the syntax to create an index in MySQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "CREATE INDEX idx_name ON table_name (column_name); creates a standalone index. You can also add one inline during CREATE TABLE, or with ALTER TABLE table_name ADD INDEX idx_name (column_name);. Use CREATE UNIQUE INDEX to also enforce uniqueness on the indexed column." }
                    },
                    {
                        "@type": "Question",
                        "name": "Why does MySQL ignore my index and do a full table scan?",
                        "acceptedAnswer": { "@type": "Answer", "text": "The optimizer skips an index when it estimates the query will touch a large percentage of the table's rows, since a table scan needs fewer random seeks than an equivalent index lookup at that volume. It also ignores indexes on tables with very few rows, and skips a composite index entirely if the query doesn't filter on its leftmost column." }
                    },
                    {
                        "@type": "Question",
                        "name": "What is the leftmost prefix rule for composite indexes?",
                        "acceptedAnswer": { "@type": "Answer", "text": "A composite index on (a, b, c) can serve queries filtering on a, on a and b, or on a, b, and c — but not queries that filter on b or c alone without a. MySQL can only use a composite index starting from its leftmost column, since that's the order the B-tree is physically sorted by." }
                    },
                    {
                        "@type": "Question",
                        "name": "How many indexes should a MySQL table have?",
                        "acceptedAnswer": { "@type": "Answer", "text": "There's no fixed number — it depends on read/write ratio. Every index speeds up matching SELECT queries but adds overhead to every INSERT, UPDATE, and DELETE, since MySQL must update each index alongside the table data. Add indexes to match actual query patterns found via EXPLAIN or slow query logs, not preemptively." }
                    },
                    {
                        "@type": "Question",
                        "name": "How do I find unused indexes in MySQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Query sys.schema_unused_indexes, a view built into MySQL 8.0's sys schema that lists indexes with zero recorded reads since the last server restart or Performance Schema reset. Cross-check with a longer observation window before dropping anything, since seasonal or infrequent queries might rely on an index that looks unused over a short sample." }
                    }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "VideoObject",
                "name": "MySQL: INDEXES are awesome",
                "description": "A practical tutorial on MySQL index syntax, B-tree structure, and composite indexes — Bro Code on YouTube (2022).",
                "thumbnailUrl": "https://img.youtube.com/vi/t0grczCICMk/hqdefault.jpg",
                "uploadDate": "2022-11-11T00:00:00+00:00",
                "embedUrl": "https://www.youtube.com/embed/t0grczCICMk",
                "url": "https://www.youtube.com/watch?v=t0grczCICMk"
            }
            ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>MySQL</span></p>
        <p class="post-eyebrow">July 2026 · <time datetime="2026-07-03">Last updated: July 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 9 min read</p>
        <h1 class="page-h1">MySQL Indexes Explained — B-Tree, Composite, and EXPLAIN</h1>
        <p class="page-sub">A MySQL index is a separate on-disk structure, typically a <code>B-tree</code>, that lets the storage engine locate matching rows without scanning the entire table. A well-placed index can turn a lookup on a million-row table into 2-3 page reads instead of a full sequential scan. This guide covers <code>CREATE INDEX</code> syntax, composite indexes and the leftmost-prefix rule, reading <code>EXPLAIN</code> output, and the mistakes that leave indexes unused.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-is-a-mysql-index">What Is a MySQL Index?</a></li>
            <li><a href="#how-b-tree-indexes-work">How B-Tree Indexes Work</a></li>
            <li><a href="#creating-and-managing-indexes">Creating and Managing Indexes</a></li>
            <li><a href="#composite-indexes-and-the-leftmost-prefix-rule">Composite Indexes</a></li>
            <li><a href="#when-mysql-ignores-your-index">When MySQL Ignores Your Index</a></li>
            <li><a href="#reading-explain-output">Reading EXPLAIN Output</a></li>
            <li><a href="#common-mistakes">Common Mistakes</a></li>
            <li><a href="#visualise-indexes-before-writing-ddl">Visualise First</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>A B-tree index turns a lookup on a <strong>1-million-row table into roughly 2-3 page reads</strong>, versus scanning every row sequentially (<a href="https://dev.mysql.com/doc/refman/8.0/en/mysql-indexes.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>).</li>
                <li>A composite index only serves queries that filter on its <strong>leftmost column</strong> — an index on <code>(a, b, c)</code> can't be used by a query filtering on <code>b</code> alone.</li>
                <li>When a query would touch a large share of a table's rows, MySQL's optimizer often <strong>chooses a full table scan over the index</strong>, since fewer random seeks are needed.</li>
                <li>MySQL 8.0's <code>sys.schema_unused_indexes</code> view lists indexes with zero recorded reads, making it the fastest way to find dead weight slowing down writes.</li>
            </ul>
        </div>

        <figure>
            <img src="https://images.pexels.com/photos/256517/pexels-photo-256517.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Rows of labelled books on library shelves representing how a MySQL index sorts data for fast lookup, similar to a library catalog"
                 loading="lazy" width="1260" height="750">
            <figcaption>An index is a sorted lookup structure, much like a library catalog — it points you straight to a shelf instead of making you check every book. (Photo: Pexels)</figcaption>
        </figure>

        <h2 id="what-is-a-mysql-index">What Is a MySQL Index?</h2>
        <p>
            A MySQL index is a separate, sorted data structure that stores a copy of one or more column values alongside a pointer back to the full row. Instead of reading every row to find a match, the storage engine walks the much smaller, sorted index structure and jumps straight to the matching entries.
        </p>
        <p>
            InnoDB, MySQL's default storage engine, implements every index as a <code>B-tree</code>. The <a href="https://dev.mysql.com/doc/refman/8.0/en/mysql-indexes.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a> notes that a B-tree index traversal on a table with roughly a million rows typically takes only 2-3 page reads, compared with reading every single row in sequence. That gap is the entire reason indexes exist.
        </p>
        <p>
            Without an index, MySQL has exactly one option for finding matching rows: read the table from start to end and check every row against the condition. That's a full table scan, and it's the default behaviour for any column without an index.
        </p>

        <div class="citation-capsule">
            InnoDB stores every index, including the primary key, as a B-tree structure ordered by the indexed column's values. A B-tree lookup on a table with about one million rows needs only 2-3 page reads to locate a match, since each page in the tree holds many keys and points to further pages, keeping the tree shallow even as row counts grow into the millions (<a href="https://dev.mysql.com/doc/refman/8.0/en/mysql-indexes.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>).
        </div>

        <h2 id="how-b-tree-indexes-work">How B-Tree Indexes Work</h2>
        <p>
            A B-tree keeps its structure shallow by packing many keys into each 16 KB page and using high fanout — every internal page can point to dozens of child pages. That's why the tree stays only 3-4 levels deep even at millions of rows, and why a lookup costs a handful of page reads rather than scaling linearly with table size.
        </p>
        <p>
            In InnoDB specifically, the primary key <em>is</em> the table — data rows live directly in the primary key's B-tree, a design called a clustered index. Every secondary index (any index other than the primary key) stores the indexed column plus the primary key value, then does a second lookup into the clustered index to fetch the rest of the row. That's why a secondary index lookup is technically two B-tree traversals, not one, though both are still fast.
        </p>

        <figure style="margin: 1.2rem 0 1.8rem;">
            <figcaption style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.55rem; font-family: 'JetBrains Mono', monospace;">Page reads to find one row in a 1-million-row table</figcaption>
            <svg viewBox="0 0 540 200" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart comparing page reads: B-tree indexed lookup at approximately 3 pages versus full table scan at approximately 15625 pages for a 1 million row table">
                <rect width="540" height="200" rx="8" fill="#181f2e"/>
                <text x="140" y="43" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">Indexed lookup</text>
                <text x="140" y="97" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">Full table scan</text>
                <rect x="146" y="25" width="6" height="22" rx="3" fill="#22c55e" opacity="0.9"/>
                <text x="160" y="40" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~3 pages</text>
                <rect x="146" y="79" width="392" height="22" rx="3" fill="#ef4444" opacity="0.82"/>
                <text x="248" y="94" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~15,625 pages (every row read)</text>
                <text x="270" y="150" text-anchor="middle" fill="#64748b" font-size="10.5" font-family="JetBrains Mono,monospace">Assumes 64 rows/page, 1,000,000-row table, single-row lookup</text>
                <text x="270" y="172" text-anchor="middle" fill="#475569" font-size="9" font-family="JetBrains Mono,monospace">Source: MySQL 8.0 Reference Manual — How MySQL Uses Indexes</text>
            </svg>
        </figure>

        <p>
            The chart above isn't to scale — the indexed bar would be invisible if it were — but it makes the point: an index turns an <code>O(n)</code> scan into something close to <code>O(log n)</code>. On small tables the difference doesn't matter. On a table with millions of rows, it's the difference between a query that returns instantly and one that times out.
        </p>

        <h2 id="creating-and-managing-indexes">Creating and Managing Indexes</h2>
        <p>
            MySQL gives you three equivalent ways to add an index: inline during <code>CREATE TABLE</code>, standalone with <code>CREATE INDEX</code>, or appended later with <code>ALTER TABLE</code>. All three produce the same on-disk structure.
        </p>

        <p><strong>Inline during table creation:</strong></p>
        <pre><code>CREATE TABLE orders (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    status     VARCHAR(20) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_orders_user (user_id),
    INDEX idx_orders_status (status)
);</code></pre>

        <p><strong>Standalone, on an existing table:</strong></p>
        <pre><code>CREATE INDEX idx_orders_created_at ON orders (created_at);</code></pre>

        <p><strong>Added with ALTER TABLE:</strong></p>
        <pre><code>ALTER TABLE orders ADD INDEX idx_orders_status (status);</code></pre>

        <p>
            To enforce uniqueness alongside the lookup speedup, use <code>UNIQUE</code>:
        </p>
        <pre><code>CREATE UNIQUE INDEX idx_users_email ON users (email);</code></pre>

        <p>
            Name your indexes with a consistent pattern, such as <code>idx_table_column</code>. It keeps <code>SHOW INDEX FROM table_name</code> readable and makes dropping a specific index straightforward: <code>DROP INDEX idx_orders_status ON orders;</code>. This mirrors the same naming discipline covered in the <a href="/blog/mysql-foreign-key">foreign key guide</a> for constraint names.
        </p>

        <div class="video-wrap">
            <iframe
                loading="lazy"
                src="https://www.youtube-nocookie.com/embed/t0grczCICMk"
                title="MySQL: INDEXES are awesome — Bro Code"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                aria-label="YouTube video tutorial: MySQL indexes explained by Bro Code">
            </iframe>
        </div>
        <p class="video-label" style="text-align:center;">MySQL: INDEXES are awesome — Bro Code (YouTube, 2022)</p>
        <noscript><a href="https://www.youtube.com/watch?v=t0grczCICMk">Watch: MySQL indexes tutorial on YouTube</a></noscript>

        <div class="citation-capsule">
            MySQL supports creating an index inline during <code>CREATE TABLE</code>, standalone via <code>CREATE INDEX</code>, or after the fact with <code>ALTER TABLE ... ADD INDEX</code> — all three produce an identical on-disk B-tree structure. Adding <code>UNIQUE</code> before <code>INDEX</code> enforces that no two rows share the same indexed value while retaining the same lookup speedup (<a href="https://dev.mysql.com/doc/refman/8.0/en/mysql-indexes.html" target="_blank" rel="noopener">MySQL 8.0 Reference Manual</a>).
        </div>

        <h2 id="composite-indexes-and-the-leftmost-prefix-rule">Composite Indexes and the Leftmost Prefix Rule</h2>
        <p>
            A composite index spans multiple columns, sorted first by the first column, then by the second within each group of matching first-column values, and so on. <code>INDEX idx_orders_lookup (user_id, status, created_at)</code> sorts first by <code>user_id</code>, then by <code>status</code> within each user, then by <code>created_at</code> within each status.
        </p>
        <pre><code>CREATE INDEX idx_orders_lookup ON orders (user_id, status, created_at);</code></pre>
        <p>
            That physical ordering is why the leftmost prefix rule exists. MySQL can use this index for a query filtering on <code>user_id</code> alone, on <code>user_id</code> and <code>status</code>, or on all three columns — because each of those is a contiguous slice of the sorted structure. It can't use the index for a query filtering on <code>status</code> alone, or on <code>created_at</code> alone, because neither is a leftmost prefix.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Query filters on</th>
                    <th>Can use idx_orders_lookup (user_id, status, created_at)?</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>user_id</code></td>
                    <td>Yes — leftmost column</td>
                </tr>
                <tr>
                    <td><code>user_id, status</code></td>
                    <td>Yes — leftmost two columns</td>
                </tr>
                <tr>
                    <td><code>user_id, status, created_at</code></td>
                    <td>Yes — full index</td>
                </tr>
                <tr>
                    <td><code>status</code> only</td>
                    <td>No — not a leftmost prefix</td>
                </tr>
                <tr>
                    <td><code>created_at</code> only</td>
                    <td>No — not a leftmost prefix</td>
                </tr>
            </tbody>
        </table>

        <p>
            The practical implication is that column order in a composite index is a design decision, not an afterthought. Put the column your application filters on most often — usually a foreign key like <code>user_id</code> — first, and reserve the rightmost position for the column that benefits most from range queries, like a timestamp, since range conditions stop the leftmost-prefix matching at that point.
        </p>

        <div class="citation-capsule">
            A composite index physically sorts rows by its first column, then by its second column within each group of matching first-column values, and so on. MySQL can only use a composite index for query conditions that form a contiguous leftmost prefix of its column list — filtering on the second or third column alone gets no benefit from the index at all.
        </div>

        <h2 id="when-mysql-ignores-your-index">When MySQL Ignores Your Index</h2>
        <p>
            An index existing doesn't guarantee MySQL uses it. The query optimizer estimates the cost of each available access path and picks the cheapest one — and sometimes that's a full table scan, even with a matching index in place. Why would the optimizer skip a structure built specifically to speed up the query?
        </p>
        <p>
            When the optimizer estimates a query will touch a large percentage of a table's rows, a table scan's sequential reads often beat an index's random reads, since random I/O is far more expensive per page than sequential I/O on spinning disks and still carries overhead on SSDs. Percona's benchmarking found that in a disk-bound scenario where the working set didn't fit in memory, a full table scan completed in about 4 seconds while a full index scan on the same query took about 30 seconds — the opposite of what most developers expect.
        </p>

        <figure style="margin: 1.2rem 0 1.8rem;">
            <figcaption style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.55rem; font-family: 'JetBrains Mono', monospace;">Execution time: disk-bound query, table doesn't fit in memory</figcaption>
            <svg viewBox="0 0 540 170" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Horizontal bar chart showing full table scan completing in approximately 4 seconds versus full index scan taking approximately 30 seconds when data does not fit in memory">
                <rect width="540" height="170" rx="8" fill="#181f2e"/>
                <text x="150" y="43" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">Full table scan</text>
                <text x="150" y="97" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">Full index scan</text>
                <rect x="156" y="25" width="52" height="22" rx="3" fill="#22c55e" opacity="0.85"/>
                <text x="216" y="40" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~4 sec</text>
                <rect x="156" y="79" width="392" height="22" rx="3" fill="#ef4444" opacity="0.82"/>
                <text x="500" y="94" text-anchor="end" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~30 sec</text>
                <text x="270" y="140" text-anchor="middle" fill="#475569" font-size="9" font-family="JetBrains Mono,monospace">Source: Percona, Full Table Scan vs Full Index Scan Performance</text>
            </svg>
        </figure>

        <p>
            This is the benchmark result that surprises developers building a schema visually for the first time: adding an index isn't always a free win. Random reads scattered across a disk-bound table can cost far more than reading the whole table in sequence, so index design has to account for row count, memory, and how selective the condition actually is — not just "does a matching column have an index."
        </p>
        <p>
            The optimizer also skips indexes on very small tables — typically under about ten rows — since a direct scan is cheaper than the overhead of a B-tree traversal. And as covered above, it ignores a composite index entirely once a query's filter conditions fall outside the leftmost prefix.
        </p>

        <div class="citation-capsule">
            Percona's benchmarking of a disk-bound query where the table didn't fit in memory found a full table scan completed in roughly 4 seconds, while the equivalent full index scan took roughly 30 seconds — a result that runs counter to the common assumption that an index always makes a query faster (<a href="https://www.percona.com/blog/full-table-scan-vs-full-index-scan-performance/" target="_blank" rel="noopener">Percona, Full Table Scan vs Full Index Scan Performance</a>).
        </div>

        <h2 id="reading-explain-output">Reading EXPLAIN Output</h2>
        <p>
            <code>EXPLAIN</code> shows the execution plan MySQL chose for a query, including which index (if any) it used. Run it by prefixing any <code>SELECT</code>:
        </p>
        <pre><code>EXPLAIN SELECT * FROM orders WHERE user_id = 42 AND status = 'shipped';</code></pre>
        <p>
            The <code>key</code> column tells you which index MySQL actually chose — <code>NULL</code> means no index was used, which is your signal to investigate. The <code>rows</code> column estimates how many rows MySQL expects to examine; a number close to the table's total row count on a query that should be selective is a red flag. <code>Extra</code> showing <code>Using filesort</code> or <code>Using temporary</code> often points to a missing index on an <code>ORDER BY</code> or <code>GROUP BY</code> column.
        </p>
        <p>
            For a deeper look at actual (not estimated) execution timing, <code>EXPLAIN ANALYZE</code> (available since MySQL 8.0.18) runs the query and reports real cost per plan node rather than the optimizer's estimate:
        </p>
        <pre><code>EXPLAIN ANALYZE SELECT * FROM orders WHERE user_id = 42 AND status = 'shipped';</code></pre>
        <p>
            To find indexes that exist but are never used — pure write overhead with no read benefit — query MySQL 8.0's built-in <code>sys</code> schema:
        </p>
        <pre><code>SELECT * FROM sys.schema_unused_indexes;</code></pre>
        <p>
            This view surfaces indexes with zero recorded reads in Performance Schema since the last restart or stats reset. Confirm over a reasonably long observation window before dropping anything — a monthly report query still needs its index even if it hasn't run in the last hour.
        </p>

        <div class="citation-capsule">
            MySQL 8.0's <code>sys.schema_unused_indexes</code> view, built on top of Performance Schema's <code>table_io_waits_summary_by_index_usage</code> table, lists every index with zero recorded read operations since the last server restart or statistics reset — the standard starting point for finding indexes that only add write overhead.
        </div>

        <h2 id="common-mistakes">Common Mistakes</h2>
        <ul>
            <li><strong>Indexing every column "just in case."</strong> Every index MySQL must update on every <code>INSERT</code>, <code>UPDATE</code>, and <code>DELETE</code>. Add indexes to match queries you've actually observed via <code>EXPLAIN</code> or the slow query log, not speculatively.</li>
            <li><strong>Wrong column order in a composite index.</strong> An index on <code>(status, user_id)</code> is nearly useless for a query that filters mostly on <code>user_id</code>, since <code>user_id</code> isn't the leftmost column. Match the order to your most common filter pattern.</li>
            <li><strong>Functions wrapped around indexed columns.</strong> <code>WHERE YEAR(created_at) = 2026</code> can't use an index on <code>created_at</code>, because MySQL must evaluate the function per row before it can compare. Rewrite as a range: <code>WHERE created_at >= '2026-01-01' AND created_at < '2027-01-01'</code>.</li>
            <li><strong>Ignoring cardinality.</strong> An index on a low-cardinality column like a boolean <code>is_active</code> flag rarely helps, since it can't narrow the row set enough to beat a table scan. Reserve indexes for columns with many distinct values.</li>
            <li><strong>Never revisiting indexes after schema changes.</strong> Indexes that made sense a year ago can become dead weight once query patterns shift. Periodically check <code>sys.schema_unused_indexes</code> as part of routine maintenance, the same way you'd review <a href="/blog/mysql-foreign-key">foreign key constraints</a> after a schema refactor.</li>
        </ul>

        <figure>
            <img src="https://images.pexels.com/photos/5480781/pexels-photo-5480781.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Server racks in a data center representing the storage infrastructure where MySQL B-tree indexes are persisted to disk"
                 loading="lazy" width="1260" height="750">
            <figcaption>Every index is a real on-disk structure InnoDB has to maintain — more indexes mean faster reads but heavier writes. (Photo: Brett Sayles / Pexels)</figcaption>
        </figure>

        <h2 id="visualise-indexes-before-writing-ddl">Visualise Indexes Before Writing DDL</h2>
        <p>
            Index planning is easier once the schema's query paths are visible. A diagram makes it obvious which foreign key columns need composite indexes, and which lookup columns are worth indexing before you've written a single <code>CREATE INDEX</code> statement.
        </p>
        <p>
            You can <a href="/demo">design your schema visually</a> and export the correct MySQL DDL, including primary keys and foreign key indexes, directly from the diagram. The <a href="/blog/crowfoot-notation">crow's foot notation guide</a> explains how to read the relationship lines that typically indicate where a foreign key index belongs, and the <a href="/blog/database-schema-examples">database schema examples post</a> shows complete indexed schemas across common application types.
        </p>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3 class="faq-q">What is the syntax to create an index in MySQL?</h3>
                <p class="faq-a"><code>CREATE INDEX idx_name ON table_name (column_name);</code> creates a standalone index. You can also add one inline during <code>CREATE TABLE</code>, or with <code>ALTER TABLE table_name ADD INDEX idx_name (column_name);</code>. Use <code>CREATE UNIQUE INDEX</code> to also enforce uniqueness on the indexed column.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">Why does MySQL ignore my index and do a full table scan?</h3>
                <p class="faq-a">The optimizer skips an index when it estimates the query will touch a large percentage of the table's rows, since a table scan needs fewer random seeks than an equivalent index lookup at that volume. It also ignores indexes on tables with very few rows, and skips a composite index entirely if the query doesn't filter on its leftmost column.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What is the leftmost prefix rule for composite indexes?</h3>
                <p class="faq-a">A composite index on <code>(a, b, c)</code> can serve queries filtering on <code>a</code>, on <code>a</code> and <code>b</code>, or on <code>a</code>, <code>b</code>, and <code>c</code> — but not queries that filter on <code>b</code> or <code>c</code> alone without <code>a</code>. MySQL can only use a composite index starting from its leftmost column, since that's the order the B-tree is physically sorted by.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How many indexes should a MySQL table have?</h3>
                <p class="faq-a">There's no fixed number — it depends on read/write ratio. Every index speeds up matching <code>SELECT</code> queries but adds overhead to every <code>INSERT</code>, <code>UPDATE</code>, and <code>DELETE</code>, since MySQL must update each index alongside the table data. Add indexes to match actual query patterns found via <code>EXPLAIN</code> or slow query logs, not preemptively.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How do I find unused indexes in MySQL?</h3>
                <p class="faq-a">Query <code>sys.schema_unused_indexes</code>, a view built into MySQL 8.0's sys schema that lists indexes with zero recorded reads since the last server restart or Performance Schema reset. Cross-check with a longer observation window before dropping anything, since seasonal or infrequent queries might rely on an index that looks unused over a short sample.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/postgresql-indexes">PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax, Examples, and Best Practices &rarr;</a></li>
                <li><a href="/blog/mysql-data-types">MySQL Data Types Explained &rarr;</a></li>
                <li><a href="/blog/sql-joins">SQL JOIN Types Explained &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization: 1NF, 2NF, and 3NF &rarr;</a></li>
                <li><a href="/blog/crowfoot-notation">Crow's Foot Notation Explained &rarr;</a></li>
                <li><a href="/blog/database-schema-examples">Database Schema Examples &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences &rarr;</a></li>
                <li><a href="/blog/database-ddl-comparison">DDL Syntax Comparison: MySQL, PostgreSQL &amp; More &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design indexed schemas visually</h2>
    <p>SQL Designer lets you mark primary and foreign keys as you draw your tables, then exports the correct MySQL DDL — indexes included. Free, no installation required.</p>
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
