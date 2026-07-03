@extends('layouts.main')

@section('title', 'PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST')

@section('head')
    <meta name="description"
          content="PostgreSQL's B-tree, GIN, GiST, and BRIN indexes each fit different data. A BRIN index can be 4,000x smaller than B-tree on 10M sorted rows.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/postgresql-indexes">
    <meta property="og:title" content="PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST">
    <meta property="og:description"
          content="PostgreSQL's B-tree, GIN, GiST, and BRIN indexes each fit different data. A BRIN index can be 4,000x smaller than B-tree on 10M sorted rows.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/postgresql-indexes">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST">
    <meta name="twitter:description" content="PostgreSQL's B-tree, GIN, GiST, and BRIN indexes each fit different data. A BRIN index can be 4,000x smaller than B-tree on 10M sorted rows.">
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
                    { "@type": "ListItem", "position": 3, "name": "PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST", "item": "https://sql-designer.com/blog/postgresql-indexes" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, GiST",
                "description": "PostgreSQL's B-tree, GIN, GiST, and BRIN indexes each fit different data. A BRIN index can be 4,000x smaller than B-tree on 10M sorted rows.",
                "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
                "url": "https://sql-designer.com/blog/postgresql-indexes",
                "datePublished": "2026-07-03",
                "dateModified": "2026-07-03",
                "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
                "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
                "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/postgresql-indexes" }
            },
            {
                "@context": "https://schema.org",
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": "What is the default index type in PostgreSQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "B-tree. Running CREATE INDEX without specifying USING creates a B-tree index, which handles equality and range queries on sortable data and covers the large majority of indexing needs — primary keys, foreign keys, and most WHERE clause lookups." }
                    },
                    {
                        "@type": "Question",
                        "name": "When should I use a GIN index instead of B-tree?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Use GIN for columns holding multiple values per row that you search inside: JSONB documents, text arrays, and full-text search tsvector columns. GIN stores one index entry per element rather than per row, making it fast for containment queries but slower to write than a B-tree." }
                    },
                    {
                        "@type": "Question",
                        "name": "What is a BRIN index good for in PostgreSQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "BRIN indexes suit very large, append-only tables where the indexed column correlates with physical row order, such as a created_at column on a time-series table. A BRIN index can be roughly 4,000 times smaller than an equivalent B-tree on a 10-million-row table, at the cost of less precise row filtering." }
                    },
                    {
                        "@type": "Question",
                        "name": "How do I create an index without locking the table in PostgreSQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Use CREATE INDEX CONCURRENTLY. A plain CREATE INDEX takes a lock that blocks writes to the table for the build's duration. CONCURRENTLY builds the index in the background without blocking INSERT, UPDATE, or DELETE, at the cost of a longer build time and the requirement to run it outside a transaction block." }
                    },
                    {
                        "@type": "Question",
                        "name": "How do I find unused indexes in PostgreSQL?",
                        "acceptedAnswer": { "@type": "Answer", "text": "Query pg_stat_user_indexes and look for rows where idx_scan equals zero. Confirm the statistics have accumulated over a representative period first, since a freshly reset counter or a recently restarted server will show every index as unused regardless of whether it's actually needed." }
                    }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "VideoObject",
                "name": "PostgreSQL indexes - B-Tree, GIN, BRIN. What's the difference? Easy explanation.",
                "description": "A practical explanation of PostgreSQL's B-Tree, GIN, and BRIN index types and when to use each (2025).",
                "thumbnailUrl": "https://img.youtube.com/vi/_HG2eB27j00/hqdefault.jpg",
                "uploadDate": "2025-06-30T00:00:00+00:00",
                "embedUrl": "https://www.youtube.com/embed/_HG2eB27j00",
                "url": "https://www.youtube.com/watch?v=_HG2eB27j00"
            }
            ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>PostgreSQL</span></p>
        <p class="post-eyebrow">July 2026 · <time datetime="2026-07-03">Last updated: July 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 9 min read</p>
        <h1 class="page-h1">PostgreSQL Indexes Explained — B-Tree, GIN, BRIN, and GiST</h1>
        <p class="page-sub">PostgreSQL ships with more index types than any other mainstream relational database: <code>B-tree</code> for the default equality and range lookups, <code>GIN</code> for JSONB, arrays, and full-text search, <code>GiST</code> for spatial and range data, and <code>BRIN</code> for huge append-only tables where a full B-tree would be wasteful. Picking the wrong one wastes disk space or leaves a query scanning rows it didn't need to touch. This guide covers each index type, composite indexes, partial and covering indexes, reading <code>EXPLAIN ANALYZE</code>, and the mistakes that leave indexes unused.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-is-a-postgresql-index">What Is a PostgreSQL Index?</a></li>
            <li><a href="#index-types-b-tree-gin-gist-brin">Index Types</a></li>
            <li><a href="#creating-and-managing-indexes">Creating and Managing Indexes</a></li>
            <li><a href="#composite-partial-and-covering-indexes">Composite, Partial, Covering</a></li>
            <li><a href="#when-postgresql-chooses-a-sequential-scan">Sequential Scan vs Index</a></li>
            <li><a href="#reading-explain-analyze-output">Reading EXPLAIN ANALYZE</a></li>
            <li><a href="#common-mistakes">Common Mistakes</a></li>
            <li><a href="#visualise-indexes-before-writing-ddl">Visualise First</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>PostgreSQL supports <strong>six index access methods</strong> — B-tree, Hash, GiST, SP-GiST, GIN, and BRIN — each optimized for a different data shape and query pattern.</li>
                <li>A <strong>BRIN index can be roughly 4,000x smaller</strong> than an equivalent B-tree on a 10-million-row, naturally-ordered table (<a href="https://www.cybertec-postgresql.com/en/btree-vs-brin-2-options-for-indexing-in-postgresql-data-warehouses/" target="_blank" rel="noopener">CYBERTEC PostgreSQL</a>).</li>
                <li>When a query is estimated to return more than roughly <strong>10% of a table's rows</strong>, the planner usually picks a sequential scan over an index scan.</li>
                <li><code>CREATE INDEX CONCURRENTLY</code> builds an index without blocking writes — the standard choice for adding an index to a live production table.</li>
            </ul>
        </div>

        <figure>
            <img src="https://images.pexels.com/photos/6801648/pexels-photo-6801648.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Magnifying glass placed on top of a printed document representing how a PostgreSQL index narrows a search to matching rows instead of scanning every page"
                 loading="lazy" width="1260" height="750">
            <figcaption>An index narrows the search before PostgreSQL ever touches the table's actual rows. (Photo: Pexels)</figcaption>
        </figure>

        <h2 id="what-is-a-postgresql-index">What Is a PostgreSQL Index?</h2>
        <p>
            A PostgreSQL index is a separate on-disk structure that stores a sorted or otherwise organized copy of one or more column values, plus a pointer back to the matching table row. Instead of reading every row in sequence, called a sequential scan, the query planner can walk the much smaller index structure and jump directly to matching rows.
        </p>
        <p>
            Unlike MySQL's InnoDB, PostgreSQL doesn't store table rows inside the primary key structure. Every PostgreSQL index, including one on the primary key, is a separate structure that points back into the table's heap. That's an architectural difference worth knowing if you're moving between the two engines — see the <a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL comparison</a> for more.
        </p>
        <p>
            <a href="https://www.postgresql.org/docs/current/indexes-types.html" target="_blank" rel="noopener">PostgreSQL's own documentation</a> lists six built-in index access methods: B-tree, Hash, GiST, SP-GiST, GIN, and BRIN. Each targets a different indexable clause — equality, range, containment, proximity, or physical correlation — and picking the wrong one either wastes storage or leaves the planner unable to use the index at all.
        </p>

        <div class="citation-capsule">
            PostgreSQL implements six index access methods — B-tree, Hash, GiST, SP-GiST, GIN, and BRIN — each suited to a different class of indexable operator (<a href="https://www.postgresql.org/docs/current/indexes-types.html" target="_blank" rel="noopener">PostgreSQL Documentation, Index Types</a>). Unlike MySQL's InnoDB, PostgreSQL never stores table data inside an index structure; every index, including on the primary key, is a separate structure pointing back to the table heap.
        </div>

        <h2 id="index-types-b-tree-gin-gist-brin">Index Types: B-Tree, GIN, GiST, and BRIN</h2>
        <p>
            <code>CREATE INDEX</code> without a <code>USING</code> clause defaults to B-tree, and that default covers most schemas. The other three types below solve specific problems B-tree can't handle efficiently.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Index type</th>
                    <th>Best for</th>
                    <th>Weak point</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>B-tree</code></td>
                    <td>Equality and range queries on sortable scalar data — the default for most columns</td>
                    <td>Not designed for multi-valued columns like JSONB or arrays</td>
                </tr>
                <tr>
                    <td><code>GIN</code></td>
                    <td>JSONB containment, array membership, full-text search (<code>tsvector</code>)</td>
                    <td>Slower to write; index maintenance cost is higher per row</td>
                </tr>
                <tr>
                    <td><code>GiST</code></td>
                    <td>Range types, geometric/spatial data (PostGIS), nearest-neighbor queries</td>
                    <td>Lossy for some operator classes, requiring a recheck of the actual row</td>
                </tr>
                <tr>
                    <td><code>BRIN</code></td>
                    <td>Very large, append-only tables where the column correlates with row insertion order</td>
                    <td>Only fast when data is physically ordered; useless on shuffled data</td>
                </tr>
            </tbody>
        </table>

        <p>
            GIN stores one index entry per element rather than one per row — a JSONB document with 10 keys produces roughly 10 index entries. That's what makes containment queries like <code>WHERE data @&gt; '{"status": "active"}'</code> fast, but it also means every write has to update potentially many entries, which is why GIN indexes are noticeably slower to maintain than B-tree.
        </p>
        <p>
            BRIN takes the opposite approach: instead of indexing every row, it stores only a min/max summary for each range of physical table pages (128 pages by default). A query can skip entire page ranges whose summary doesn't overlap the search condition, without ever pointing to an individual row. That's why BRIN indexes are tiny compared to B-tree, but only useful when the indexed column's values roughly track the order rows were inserted — a <code>created_at</code> timestamp on an append-only log table is the textbook case.
        </p>

        <figure style="margin: 1.2rem 0 1.8rem;">
            <figcaption style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.55rem; font-family: 'JetBrains Mono', monospace;">Index size on a 10-million-row, naturally-ordered table</figcaption>
            <svg viewBox="0 0 540 200" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart comparing index size: B-tree index approximately 214 megabytes versus BRIN index approximately 48 kilobytes on a 10 million row table">
                <rect width="540" height="200" rx="8" fill="#181f2e"/>
                <text x="140" y="43" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">B-tree index</text>
                <text x="140" y="97" text-anchor="end" fill="#94a3b8" font-size="11.5" font-family="JetBrains Mono,monospace">BRIN index</text>
                <rect x="146" y="25" width="392" height="22" rx="3" fill="#ef4444" opacity="0.82"/>
                <text x="500" y="40" text-anchor="end" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~214 MB</text>
                <rect x="146" y="79" width="4" height="22" rx="2" fill="#22c55e" opacity="0.9"/>
                <text x="158" y="94" fill="#f1f5f9" font-size="11" font-family="JetBrains Mono,monospace" font-weight="600">~48 KB (about 4,000x smaller)</text>
                <text x="270" y="150" text-anchor="middle" fill="#64748b" font-size="10.5" font-family="JetBrains Mono,monospace">10-million-row table, column values physically correlated with insertion order</text>
                <text x="270" y="172" text-anchor="middle" fill="#475569" font-size="9" font-family="JetBrains Mono,monospace">Source: CYBERTEC PostgreSQL — btree vs. BRIN index in data warehouses</text>
            </svg>
        </figure>

        <p>
            The size gap looks like a pure win for BRIN, but it comes with a real trade-off: BRIN only stores page-range summaries, so a lookup still has to re-check every row inside a matching range. On data that isn't naturally ordered, that recheck cost erases the benefit entirely — BRIN is a bet on physical correlation, not a universal replacement for B-tree.
        </p>

        <div class="citation-capsule">
            On a 10-million-row table with values that correlate with physical insertion order, a B-tree index measured roughly 214 MB while an equivalent BRIN index measured roughly 48 KB — about 4,000 times smaller (<a href="https://www.cybertec-postgresql.com/en/btree-vs-brin-2-options-for-indexing-in-postgresql-data-warehouses/" target="_blank" rel="noopener">CYBERTEC PostgreSQL</a>). BRIN achieves this by storing only a summary per block range rather than an entry per row, so it works only when the indexed column tracks the table's physical row order.
        </div>

        <h2 id="creating-and-managing-indexes">Creating and Managing Indexes</h2>
        <p>
            The base syntax is the same across all six index types — only the <code>USING</code> clause changes.
        </p>

        <p><strong>Default B-tree:</strong></p>
        <pre><code>CREATE INDEX idx_orders_user ON orders (user_id);</code></pre>

        <p><strong>GIN, for a JSONB column:</strong></p>
        <pre><code>CREATE INDEX idx_orders_metadata ON orders USING GIN (metadata);</code></pre>

        <p><strong>BRIN, for an append-only timestamp column:</strong></p>
        <pre><code>CREATE INDEX idx_events_created_at ON events USING BRIN (created_at);</code></pre>

        <p><strong>Unique index:</strong></p>
        <pre><code>CREATE UNIQUE INDEX idx_users_email ON users (email);</code></pre>

        <p>
            On a live production table, always add <code>CONCURRENTLY</code>:
        </p>
        <pre><code>CREATE INDEX CONCURRENTLY idx_orders_status ON orders (status);</code></pre>
        <p>
            A plain <code>CREATE INDEX</code> takes a lock that blocks writes for the entire build. <code>CONCURRENTLY</code> builds the index without that lock, at the cost of roughly double the build time and a requirement that it run outside a transaction block. If the build fails partway through, it can leave behind an invalid index — check <code>pg_index.indisvalid</code> and drop it before retrying.
        </p>

        <div class="video-wrap">
            <iframe
                loading="lazy"
                src="https://www.youtube-nocookie.com/embed/_HG2eB27j00"
                title="PostgreSQL indexes - B-Tree, GIN, BRIN. What's the difference? Easy explanation."
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                aria-label="YouTube video: PostgreSQL B-Tree, GIN, and BRIN indexes explained">
            </iframe>
        </div>
        <p class="video-label" style="text-align:center;">PostgreSQL indexes - B-Tree, GIN, BRIN. What's the difference? (YouTube, 2025)</p>
        <noscript><a href="https://www.youtube.com/watch?v=_HG2eB27j00">Watch: PostgreSQL index types explained on YouTube</a></noscript>

        <div class="citation-capsule">
            Every PostgreSQL index type shares the same <code>CREATE INDEX ... USING method (column)</code> syntax, defaulting to B-tree when <code>USING</code> is omitted. <code>CREATE INDEX CONCURRENTLY</code> avoids the write-blocking lock a normal index build takes, making it the standard approach for adding indexes to tables already serving production traffic (<a href="https://www.postgresql.org/docs/current/indexes-types.html" target="_blank" rel="noopener">PostgreSQL Documentation</a>).
        </div>

        <h2 id="composite-partial-and-covering-indexes">Composite, Partial, and Covering Indexes</h2>
        <p>
            A composite index spans multiple columns and follows the same leftmost-prefix rule as MySQL: an index on <code>(user_id, status, created_at)</code> serves queries filtering on <code>user_id</code> alone, on <code>user_id</code> and <code>status</code>, or on all three — but not on <code>status</code> or <code>created_at</code> alone, since neither is a leftmost prefix of the sorted structure.
        </p>
        <pre><code>CREATE INDEX idx_orders_lookup ON orders (user_id, status, created_at);</code></pre>
        <p>
            PostgreSQL adds two refinements MySQL doesn't have natively: partial indexes and covering indexes via <code>INCLUDE</code>. A partial index only indexes rows matching a <code>WHERE</code> condition, which keeps the index small when you only ever query a subset of rows:
        </p>
        <pre><code>CREATE INDEX idx_orders_pending ON orders (created_at)
WHERE status = 'pending';</code></pre>
        <p>
            A covering index adds extra columns purely for retrieval, without making them part of the sort key, letting the planner satisfy a query entirely from the index without touching the table heap (an <em>index-only scan</em>):
        </p>
        <pre><code>CREATE INDEX idx_orders_covering ON orders (user_id)
INCLUDE (status, total);</code></pre>
        <p>
            Partial indexes are consistently underused in production schemas. A queue table where 95% of rows are already <code>completed</code> and only the <code>pending</code> rows are ever queried is exactly the case where a partial index cuts both the index size and the write overhead dramatically, since completed rows never enter the index at all.
        </p>

        <div class="citation-capsule">
            A partial index in PostgreSQL indexes only the rows matching a <code>WHERE</code> predicate specified at creation time, reducing both index size and write maintenance cost when queries only ever target a known subset of rows. An <code>INCLUDE</code> clause adds non-key columns to an index purely for retrieval, enabling index-only scans that never touch the table heap.
        </div>

        <h2 id="when-postgresql-chooses-a-sequential-scan">When PostgreSQL Chooses a Sequential Scan</h2>
        <p>
            Having an index doesn't guarantee the planner uses it. PostgreSQL's cost-based optimizer estimates the cost of a sequential scan against the cost of an index scan for each query and picks whichever is cheaper — and for a large fraction of matching rows, a sequential scan usually wins.
        </p>
        <p>
            An index scan is fastest when a query needs only a small fraction of a table's rows, since it avoids reading the rest of the table entirely. Once a query is estimated to return roughly 10% or more of a table's rows, the sequential scan's simpler, more predictable I/O pattern typically outperforms the random-access pattern of following an index across that many matches.
        </p>
        <p>
            Small tables see the same effect for a different reason: if a table fits in a handful of pages, PostgreSQL just reads all of them, since the overhead of consulting an index isn't worth it. Why keep an index at all if the optimizer routinely ignores it? Because the row estimate that triggers a sequential scan today changes as the table grows — an index that looks unnecessary on a 500-row table becomes essential once that table hits 5 million rows.
        </p>

        <div class="citation-capsule">
            PostgreSQL's planner favors a sequential scan over an index scan once the estimated result set approaches roughly 10% of a table's total rows, since sequential I/O has lower per-row overhead than following an index across a large fraction of the table (<a href="https://www.crunchydata.com/blog/postgres-scan-types-in-explain-plans" target="_blank" rel="noopener">Crunchy Data, How to Read Postgres EXPLAIN</a>). The same threshold effect applies to small tables, where a full scan of a few pages beats the overhead of an index lookup entirely.
        </div>

        <h2 id="reading-explain-analyze-output">Reading EXPLAIN ANALYZE Output</h2>
        <p>
            <code>EXPLAIN</code> shows the planner's chosen execution plan without running the query; <code>EXPLAIN ANALYZE</code> actually executes it and reports real elapsed time per plan node alongside the planner's estimate.
        </p>
        <pre><code>EXPLAIN ANALYZE
SELECT * FROM orders WHERE user_id = 42 AND status = 'shipped';</code></pre>
        <p>
            Look for <code>Seq Scan</code> versus <code>Index Scan</code> (or <code>Bitmap Index Scan</code>, used when the planner expects a moderate number of matches) in the plan's top line. A large gap between the estimated row count and the actual row count usually means the table's statistics are stale — run <code>ANALYZE table_name;</code> to refresh them.
        </p>
        <p>
            To find indexes nobody's querying against, check <code>pg_stat_user_indexes</code>:
        </p>
        <pre><code>SELECT relname AS table_name, indexrelname AS index_name, idx_scan
FROM pg_stat_user_indexes
WHERE idx_scan = 0
ORDER BY relname;</code></pre>
        <p>
            An <code>idx_scan</code> of zero means the index has never been used by the planner since the statistics were last reset. As with MySQL's <code>sys.schema_unused_indexes</code>, confirm the observation window is long enough — check <code>stats_reset</code> in <code>pg_stat_database</code> — before dropping anything a monthly report query might still depend on.
        </p>

        <div class="citation-capsule">
            The <code>pg_stat_user_indexes</code> system view exposes an <code>idx_scan</code> counter per index, incremented every time the planner uses that index to answer a query; a value of zero across a representative time window flags the index as a pure write-overhead candidate for removal (<a href="https://www.postgresql.org/docs/current/monitoring-stats.html" target="_blank" rel="noopener">PostgreSQL Documentation, Cumulative Statistics System</a>).
        </div>

        <h2 id="common-mistakes">Common Mistakes</h2>
        <ul>
            <li><strong>Using B-tree on a JSONB column you query for containment.</strong> A default B-tree index on a JSONB column can only support equality on the whole value. Use <code>GIN</code> for <code>@&gt;</code>, <code>?</code>, and similar containment operators.</li>
            <li><strong>Skipping <code>CONCURRENTLY</code> on a production table.</strong> A plain <code>CREATE INDEX</code> blocks all writes to the table for the duration of the build, which on a large table can mean minutes of blocked <code>INSERT</code> and <code>UPDATE</code> statements.</li>
            <li><strong>Choosing BRIN for shuffled data.</strong> BRIN only pays off when the indexed column correlates with physical row order. Applying it to a randomly-ordered column, like a UUID primary key, produces an index that can't meaningfully narrow a search.</li>
            <li><strong>Wrong column order in a composite index.</strong> Just like MySQL, the leftmost column should match your most common filter condition. An index on <code>(status, user_id)</code> gives little benefit to queries that mostly filter on <code>user_id</code>.</li>
            <li><strong>Never checking for unused indexes.</strong> Every index adds write overhead with zero benefit if the planner never selects it. Review <code>pg_stat_user_indexes</code> periodically, the same discipline covered in the <a href="/blog/mysql-indexes">MySQL indexes guide</a> for <code>sys.schema_unused_indexes</code>.</li>
        </ul>

        <figure>
            <img src="https://images.pexels.com/photos/5156696/pexels-photo-5156696.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Boxes organized on warehouse shelves representing how a BRIN index summarizes ranges of physically ordered table pages instead of indexing every row"
                 loading="lazy" width="1260" height="750">
            <figcaption>A BRIN index works like labeling warehouse aisles instead of every box — fast to scan, but only useful if items are actually stored in order. (Photo: Pexels)</figcaption>
        </figure>

        <h2 id="visualise-indexes-before-writing-ddl">Visualise Indexes Before Writing DDL</h2>
        <p>
            Index type decisions get easier once your schema's actual query patterns are visible. Marking which columns are foreign keys, which hold JSONB, and which are append-only timestamps is a design-time decision that's far clearer on a diagram than buried in a migration file.
        </p>
        <p>
            You can <a href="/demo">design your schema visually</a> and export the correct PostgreSQL DDL, including primary keys and foreign key indexes, straight from the diagram. See the <a href="/blog/postgresql-data-types">PostgreSQL data types guide</a> for choosing the right column type before you decide which index method fits it, and the <a href="/blog/database-schema-examples">database schema examples post</a> for complete indexed schemas across common application types.
        </p>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3 class="faq-q">What is the default index type in PostgreSQL?</h3>
                <p class="faq-a">B-tree. Running <code>CREATE INDEX</code> without specifying <code>USING</code> creates a B-tree index, which handles equality and range queries on sortable data and covers the large majority of indexing needs — primary keys, foreign keys, and most <code>WHERE</code> clause lookups.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">When should I use a GIN index instead of B-tree?</h3>
                <p class="faq-a">Use <code>GIN</code> for columns holding multiple values per row that you search inside: JSONB documents, text arrays, and full-text search <code>tsvector</code> columns. GIN stores one index entry per element rather than per row, making it fast for containment queries but slower to write than a B-tree.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">What is a BRIN index good for in PostgreSQL?</h3>
                <p class="faq-a">BRIN indexes suit very large, append-only tables where the indexed column correlates with physical row order, such as a <code>created_at</code> column on a time-series table. A BRIN index can be roughly 4,000 times smaller than an equivalent B-tree on a 10-million-row table, at the cost of less precise row filtering.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How do I create an index without locking the table in PostgreSQL?</h3>
                <p class="faq-a">Use <code>CREATE INDEX CONCURRENTLY</code>. A plain <code>CREATE INDEX</code> takes a lock that blocks writes to the table for the build's duration. <code>CONCURRENTLY</code> builds the index in the background without blocking <code>INSERT</code>, <code>UPDATE</code>, or <code>DELETE</code>, at the cost of a longer build time and the requirement to run it outside a transaction block.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-q">How do I find unused indexes in PostgreSQL?</h3>
                <p class="faq-a">Query <code>pg_stat_user_indexes</code> and look for rows where <code>idx_scan</code> equals zero. Confirm the statistics have accumulated over a representative period first, since a freshly reset counter or a recently restarted server will show every index as unused regardless of whether it's actually needed.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/mysql-indexes">MySQL Indexes Explained — B-Tree, Composite, EXPLAIN &rarr;</a></li>
                <li><a href="/blog/postgresql-data-types">PostgreSQL Data Types Explained &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences &rarr;</a></li>
                <li><a href="/blog/sql-joins">SQL JOIN Types Explained &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization: 1NF, 2NF, and 3NF &rarr;</a></li>
                <li><a href="/blog/crowfoot-notation">Crow's Foot Notation Explained &rarr;</a></li>
                <li><a href="/blog/database-schema-examples">Database Schema Examples &rarr;</a></li>
                <li><a href="/blog/database-ddl-comparison">DDL Syntax Comparison: MySQL, PostgreSQL &amp; More &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design indexed PostgreSQL schemas visually</h2>
    <p>SQL Designer lets you mark primary and foreign keys as you draw your tables, then exports the correct PostgreSQL DDL — indexes included. Free, no installation required.</p>
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
