@extends('layouts.main')

@section('title', 'SQL DDL Comparison: MySQL, PostgreSQL, Oracle, SQL Server')

@section('head')
    <meta name="description" content="DDL syntax for MySQL, PostgreSQL, Oracle, SQL Server, and SQLite compared side by side: auto-increment, data types, constraints, and ALTER TABLE in one guide.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/database-ddl-comparison">
    <meta property="og:title" content="DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
    <meta property="og:description" content="Side-by-side DDL comparison across five major databases: CREATE TABLE, primary keys, data types, constraints, and ALTER TABLE syntax.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/database-ddl-comparison">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — visual database schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
    <meta name="twitter:description" content="Side-by-side DDL comparison across five major databases: CREATE TABLE, primary keys, data types, constraints, and ALTER TABLE syntax.">
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
            { "@type": "ListItem", "position": 3, "name": "DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite", "item": "https://sql-designer.com/blog/database-ddl-comparison" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": ["BlogPosting", "TechArticle"],
        "headline": "DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite",
        "description": "How CREATE TABLE syntax, primary keys, data types, constraints, and ALTER TABLE differ across MySQL, PostgreSQL, Oracle, Microsoft SQL Server, and SQLite — with side-by-side DDL examples.",
        "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
        "url": "https://sql-designer.com/blog/database-ddl-comparison",
        "datePublished": "2026-05-05",
        "dateModified": "2026-05-16",
            "author": { "@id": "https://sql-designer.com/about#dmitriy-snyatkov" },
            "publisher": { "@id": "https://sql-designer.com/#organization" },
        "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
        "about": [
            { "@type": "SoftwareApplication", "name": "MySQL" },
            { "@type": "SoftwareApplication", "name": "PostgreSQL" },
            { "@type": "SoftwareApplication", "name": "Oracle Database" },
            { "@type": "SoftwareApplication", "name": "Microsoft SQL Server" },
            { "@type": "SoftwareApplication", "name": "SQLite" }
        ],
        "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/database-ddl-comparison" }
    },
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "How do you create an auto-increment primary key in each database?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "MySQL uses AUTO_INCREMENT, PostgreSQL uses SERIAL or GENERATED ALWAYS AS IDENTITY, Oracle uses GENERATED ALWAYS AS IDENTITY (12c+) or a separate sequence on older versions, SQL Server uses IDENTITY(1,1), and SQLite uses INTEGER PRIMARY KEY which auto-increments implicitly by aliasing the internal rowid."
                }
            },
            {
                "@type": "Question",
                "name": "Which databases enforce CHECK constraints?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "PostgreSQL, Oracle, and SQL Server have always enforced CHECK constraints fully. MySQL ignored them before version 8.0.16 (April 2019) — schemas built on MySQL 5.7 or earlier may contain data that violates defined CHECK rules. SQLite enforces CHECK constraints since version 3.25.0 (2018)."
                }
            },
            {
                "@type": "Question",
                "name": "What is the equivalent of VARCHAR across different databases?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "MySQL and PostgreSQL both use VARCHAR(n). Oracle requires VARCHAR2(n) — using plain VARCHAR in Oracle is not recommended. SQL Server uses VARCHAR(n) for ASCII and NVARCHAR(n) for Unicode. SQLite stores strings as TEXT regardless of the declared column type."
                }
            },
            {
                "@type": "Question",
                "name": "Does Oracle have a native BOOLEAN column type?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Oracle 23c introduced a native BOOLEAN column type, the first Oracle version to support it in SQL DDL. On earlier Oracle versions, the standard workaround is NUMBER(1) CHECK (col IN (0, 1)), which enforces boolean semantics at the database level without a true boolean type."
                }
            },
            {
                "@type": "Question",
                "name": "Can you rename a column directly in SQLite?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "SQLite has supported RENAME COLUMN since version 3.25.0 (2018). Dropping columns requires SQLite 3.35.0 or later (2021). On older SQLite builds, both operations require recreating the table with the new structure and copying data across."
                }
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
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>Schema Design</span></p>
        <p class="post-eyebrow">May 2026 · <time datetime="2026-05-16">Last updated: May 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 12 min read</p>
        <h1 class="page-h1">DDL Differences: MySQL, PostgreSQL, Oracle, SQL Server, and SQLite</h1>
        <p class="page-sub">MySQL, PostgreSQL, Oracle Database, Microsoft SQL Server, and SQLite all use SQL DDL but differ in <code>CREATE TABLE</code> syntax, auto-increment mechanisms (<code>AUTO_INCREMENT</code> vs <code>SERIAL</code> vs <code>IDENTITY</code>), data type names (<code>VARCHAR</code> vs <code>VARCHAR2</code> vs <code>NVARCHAR</code>), CHECK constraint enforcement (ignored in MySQL before 8.0.16), and <code>ALTER TABLE</code> capabilities. These differences matter when you're designing portable schemas or migrating between databases.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#at-a-glance">At a Glance</a></li>
            <li><a href="#primary-keys">Primary Keys</a></li>
            <li><a href="#string-types">String Types</a></li>
            <li><a href="#numeric-types">Numeric Types</a></li>
            <li><a href="#boolean-type">Boolean Type</a></li>
            <li><a href="#date-and-time">Date and Time</a></li>
            <li><a href="#check-constraints">CHECK Constraints</a></li>
            <li><a href="#default-values">DEFAULT Values</a></li>
            <li><a href="#generated-columns">Generated Columns</a></li>
            <li><a href="#alter-table">ALTER TABLE</a></li>
            <li><a href="#summary">Summary</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>Each database uses a different keyword for auto-increment PKs: <code>AUTO_INCREMENT</code>, <code>SERIAL</code>/<code>IDENTITY</code>, <code>GENERATED AS IDENTITY</code>, <code>IDENTITY(1,1)</code>, or implicit <code>INTEGER PRIMARY KEY</code>.</li>
                <li>MySQL silently ignored <code>CHECK</code> constraints before version 8.0.16 (April 2019), so older schemas may contain invalid data.</li>
                <li>Oracle's <code>DATE</code> type stores both date and time, unlike every other database where <code>DATE</code> is date-only — a common migration gotcha.</li>
                <li>SQLite didn't support <code>DROP COLUMN</code> until version 3.35.0 (2021) and isn't suited for schemas that need frequent structural changes in production.</li>
            </ul>
        </div>

        <figure>
            <img src="https://images.pexels.com/photos/1181354/pexels-photo-1181354.jpeg?auto=compress&cs=tinysrgb&w=1600"
                 alt="A software engineer with a laptop working beside server racks in a data center"
                 width="1600" height="1067" loading="lazy">
            <figcaption>DDL differences matter most when schemas move between databases — each engine running its own rules on the same server infrastructure. (Photo: Christina Morillo / <a href="https://www.pexels.com" target="_blank" rel="noopener noreferrer">Pexels</a>)</figcaption>
        </figure>

        <h2 id="at-a-glance">At a Glance</h2>
        <p>Five major SQL databases share the same core DDL concepts but use incompatible syntax. MySQL relies on <code>AUTO_INCREMENT</code> while SQL Server uses <code>IDENTITY(1,1)</code>. Oracle stores all integers as <code>NUMBER(p,s)</code> while PostgreSQL has dedicated <code>BIGINT</code> and <code>SMALLINT</code> types. SQLite ignores declared type names entirely at the storage level. The table below summarizes the most important differences; each section below drills into one topic with side-by-side code examples.</p>
        <table>
            <tr>
                <th>Feature</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Auto-increment PK</td>
                <td><code>AUTO_INCREMENT</code></td>
                <td><code>SERIAL</code> / <code>IDENTITY</code></td>
                <td><code>GENERATED AS IDENTITY</code></td>
                <td><code>IDENTITY(1,1)</code></td>
                <td><code>INTEGER PRIMARY KEY</code></td>
            </tr>
            <tr>
                <td>Variable string</td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR2(n)</code></td>
                <td><code>NVARCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Large text</td>
                <td><code>LONGTEXT</code></td>
                <td><code>TEXT</code></td>
                <td><code>CLOB</code></td>
                <td><code>NVARCHAR(MAX)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Boolean</td>
                <td><code>TINYINT(1)</code></td>
                <td><code>BOOLEAN</code></td>
                <td><code>NUMBER(1)</code></td>
                <td><code>BIT</code></td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td>Auto timestamp</td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
                <td><code>DEFAULT NOW()</code></td>
                <td><code>DEFAULT SYSTIMESTAMP</code></td>
                <td><code>DEFAULT GETDATE()</code></td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
            </tr>
            <tr>
                <td>CHECK constraints</td>
                <td>Enforced (MySQL 8.0.16+)</td>
                <td>Always enforced</td>
                <td>Always enforced</td>
                <td>Always enforced</td>
                <td>Parsed; enforced since 3.25.0</td>
            </tr>
            <tr>
                <td>Generated columns</td>
                <td><code>GENERATED ALWAYS AS (...) STORED/VIRTUAL</code></td>
                <td><code>GENERATED ALWAYS AS (...) STORED</code></td>
                <td><code>GENERATED ALWAYS AS (...) VIRTUAL</code></td>
                <td><code>AS (...) PERSISTED/computed</code></td>
                <td><code>GENERATED ALWAYS AS (...) STORED/VIRTUAL</code></td>
            </tr>
            <tr>
                <td>Rename column (ALTER)</td>
                <td><code>RENAME COLUMN</code> (MySQL 8+)</td>
                <td><code>RENAME COLUMN</code></td>
                <td><code>RENAME COLUMN</code></td>
                <td><code>sp_rename</code> (stored procedure)</td>
                <td>Supported since SQLite 3.25.0</td>
            </tr>
            <tr>
                <td>IF NOT EXISTS</td>
                <td>Supported</td>
                <td>Supported</td>
                <td>Not supported (use exception handling)</td>
                <td><code>IF NOT EXISTS</code> (SQL Server 2022+) or <code>OBJECT_ID</code> check</td>
                <td>Supported</td>
            </tr>
        </table>

        <h2 id="primary-keys">How Does Each Database Handle Auto-Increment Primary Keys?</h2>
        <p>
            Auto-incrementing integer primary keys are the most visible DDL difference between databases. Every database uses its own keyword or mechanism, and the underlying behaviour varies in subtle ways. MySQL's <code>AUTO_INCREMENT</code> is a column-level attribute. PostgreSQL offers <code>SERIAL</code> (a shorthand that creates a backing sequence) and <code>GENERATED ALWAYS AS IDENTITY</code> (the SQL:2003 standard form, preferred for new schemas from PostgreSQL 10 onwards). SQL Server uses <code>IDENTITY(seed, increment)</code>. Oracle added native identity columns in 12c; older versions require a separate sequence object plus a trigger. SQLite takes the simplest approach: declare <code>INTEGER PRIMARY KEY</code> and the column automatically aliases the internal <code>rowid</code>.
        </p>

        <div class="db-grid">
            <div class="db-block">
                <p class="db-label">MySQL</p>
                <pre><code>CREATE TABLE users (
  id INT UNSIGNED NOT NULL
     AUTO_INCREMENT,
  PRIMARY KEY (id)
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">PostgreSQL</p>
                <pre><code>-- modern standard (PG 10+)
CREATE TABLE users (
  id INT GENERATED ALWAYS
     AS IDENTITY PRIMARY KEY
);

-- legacy shorthand
-- id SERIAL PRIMARY KEY</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">Oracle</p>
                <pre><code>-- Oracle 12c+
CREATE TABLE users (
  id NUMBER GENERATED ALWAYS
     AS IDENTITY PRIMARY KEY
);

-- Oracle 11g and older:
-- use a SEQUENCE + trigger</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQL Server</p>
                <pre><code>CREATE TABLE users (
  id INT NOT NULL
     IDENTITY(1, 1),
  CONSTRAINT PK_users
     PRIMARY KEY (id)
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQLite</p>
                <pre><code>CREATE TABLE users (
  id INTEGER PRIMARY KEY
  -- AUTOINCREMENT keyword
  -- is optional and changes
  -- the reuse behaviour
);</code></pre>
            </div>
        </div>

        <p>Key behavioural notes:</p>
        <ul>
            <li><strong>MySQL</strong> (<a href="https://dev.mysql.com/doc/refman/8.0/en/example-auto-increment.html" target="_blank" rel="noopener">MySQL docs</a>): <code>AUTO_INCREMENT</code> reuses gaps by default. Before MySQL 8.0, the counter reset on server restart when the table was empty.</li>
            <li><strong>PostgreSQL</strong> (<a href="https://www.postgresql.org/docs/current/datatype-numeric.html" target="_blank" rel="noopener">PostgreSQL docs</a>): <code>SERIAL</code> creates a backing sequence object. <code>GENERATED ALWAYS AS IDENTITY</code> is the SQL:2003 standard equivalent and is preferred for new schemas.</li>
            <li><strong>Oracle</strong> (<a href="https://docs.oracle.com/en/database/oracle/oracle-database/21/sqlrf/CREATE-TABLE.html" target="_blank" rel="noopener">Oracle docs</a>): before 12c, sequences were separate objects fed into the column via a trigger or called explicitly in the <code>INSERT</code> statement.</li>
            <li><strong>SQL Server</strong> (<a href="https://learn.microsoft.com/en-us/sql/t-sql/statements/create-table-transact-sql-identity-property" target="_blank" rel="noopener">SQL Server docs</a>): once a row with an identity value is inserted, you cannot insert an explicit value without <code>SET IDENTITY_INSERT table ON</code>.</li>
            <li><strong>SQLite</strong> (<a href="https://www.sqlite.org/autoinc.html" target="_blank" rel="noopener">SQLite docs</a>): <code>INTEGER PRIMARY KEY</code> without the <code>AUTOINCREMENT</code> keyword reuses deleted values. Adding <code>AUTOINCREMENT</code> prevents reuse at a small performance cost.</li>
        </ul>

        <h2 id="string-types">String Types</h2>
        <p>String storage is one of the most divergent areas across databases, particularly around Unicode support and maximum lengths. Oracle's requirement for <code>VARCHAR2</code> instead of <code>VARCHAR</code> trips up nearly every developer migrating from MySQL or PostgreSQL. SQL Server's distinction between <code>VARCHAR</code> (single-byte ASCII) and <code>NVARCHAR</code> (Unicode UTF-16) is equally easy to get wrong in production.</p>
        <table>
            <tr>
                <th>Use case</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Short variable string</td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR(n)</code></td>
                <td><code>VARCHAR2(n)</code></td>
                <td><code>NVARCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Fixed-length string</td>
                <td><code>CHAR(n)</code></td>
                <td><code>CHAR(n)</code></td>
                <td><code>CHAR(n)</code></td>
                <td><code>NCHAR(n)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Large text / CLOB</td>
                <td><code>LONGTEXT</code></td>
                <td><code>TEXT</code> (unlimited)</td>
                <td><code>CLOB</code></td>
                <td><code>NVARCHAR(MAX)</code></td>
                <td><code>TEXT</code></td>
            </tr>
            <tr>
                <td>Max <code>VARCHAR</code> length</td>
                <td>65,535 bytes</td>
                <td>1 GB</td>
                <td>32,767 bytes</td>
                <td>4,000 chars (<code>NVARCHAR</code>)</td>
                <td>Unlimited (stored as TEXT)</td>
            </tr>
        </table>

        <p>Important differences to be aware of:</p>
        <ul>
            <li><strong>Oracle uses <code>VARCHAR2</code>, not <code>VARCHAR</code>.</strong> Oracle's documentation (<a href="https://docs.oracle.com/en/database/oracle/oracle-database/21/sqlrf/Data-Types.html" target="_blank" rel="noopener">Oracle SQL reference</a>) reserves the <code>VARCHAR</code> keyword for potential future redefinition. Always use <code>VARCHAR2</code>.</li>
            <li><strong>SQL Server uses <code>N</code>-prefix types for Unicode.</strong> <code>NVARCHAR</code> and <code>NCHAR</code> store Unicode (UTF-16). Plain <code>VARCHAR</code> is ASCII only. For any modern application, use <code>NVARCHAR</code>.</li>
            <li><strong>PostgreSQL's <code>TEXT</code> is unlimited.</strong> <code>TEXT</code> and <code>VARCHAR(n)</code> have identical performance; the length limit is just a constraint, not a storage optimisation (<a href="https://www.postgresql.org/docs/current/datatype-character.html" target="_blank" rel="noopener">PostgreSQL character types</a>).</li>
            <li><strong>SQLite ignores type affinity at the storage level.</strong> SQLite stores any string as <code>TEXT</code> regardless of whether you declare the column as <code>VARCHAR(255)</code> or <code>CHAR(10)</code>. Type names are advisory only.</li>
        </ul>

        <div class="verdict"><p>For portable DDL, <code>VARCHAR(n)</code> works on MySQL, PostgreSQL, Oracle (write it as <code>VARCHAR2</code>), and SQL Server. Always use <code>NVARCHAR</code> on SQL Server for Unicode safety.</p></div>

        <h2 id="numeric-types">Numeric Types</h2>
        <p>Integer and decimal types are broadly consistent in naming, but gaps exist. Oracle stands out because it has no dedicated integer storage types — everything goes through the flexible <code>NUMBER(p,s)</code> format, which means integers may take more storage than the equivalent MySQL <code>INT</code> or PostgreSQL <code>INTEGER</code>. SQLite takes the opposite approach: the database decides actual storage size automatically based on the value stored, regardless of the declared type name.</p>
        <table>
            <tr>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td><code>TINYINT</code> (1 byte)</td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>NUMBER(3)</code></td>
                <td><code>TINYINT</code> (1 byte)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>NUMBER(5)</code></td>
                <td><code>SMALLINT</code> (2 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>INT</code> (4 bytes)</td>
                <td><code>INTEGER</code> (4 bytes)</td>
                <td><code>NUMBER(10)</code></td>
                <td><code>INT</code> (4 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>NUMBER(19)</code></td>
                <td><code>BIGINT</code> (8 bytes)</td>
                <td><code>INTEGER</code></td>
            </tr>
            <tr>
                <td><code>DECIMAL(p,s)</code></td>
                <td><code>NUMERIC(p,s)</code></td>
                <td><code>NUMBER(p,s)</code></td>
                <td><code>DECIMAL(p,s)</code></td>
                <td><code>NUMERIC(p,s)</code></td>
            </tr>
        </table>

        <p>Notable differences:</p>
        <ul>
            <li><strong>Oracle has no separate integer types.</strong> Everything is <code>NUMBER(p,s)</code>. <code>INTEGER</code> is an alias for <code>NUMBER(38)</code> and lacks the compact storage of a true 4-byte integer.</li>
            <li><strong>SQLite uses dynamic typing.</strong> Any integer you declare gets stored in 1 to 8 bytes depending on value, regardless of the declared type name.</li>
            <li><strong>MySQL supports <code>UNSIGNED</code>.</strong> <code>INT UNSIGNED</code> doubles the positive range to 0 through roughly 4.3 billion. No other major database supports this modifier.</li>
            <li>Use <code>DECIMAL</code> or <code>NUMERIC</code> for monetary values in all databases. Never use <code>FLOAT</code> or <code>DOUBLE</code> for money.</li>
        </ul>

        <h2 id="boolean-type">Which Databases Have a Native Boolean Type?</h2>
        <p>Boolean is one of the most inconsistent types across SQL databases. PostgreSQL is the only engine here with a true native <code>BOOLEAN</code> column that accepts <code>TRUE</code>, <code>FALSE</code>, and a range of equivalent string literals (<code>'t'</code>, <code>'yes'</code>, <code>'on'</code>, and <code>1</code>). SQL Server's <code>BIT</code> is close but is better described as a 1-bit integer. Oracle had no SQL-level boolean column at all until version 23c.</p>
        <table>
            <tr>
                <th>Database</th>
                <th>Boolean DDL</th>
                <th>True / False values</th>
            </tr>
            <tr>
                <td>MySQL</td>
                <td><code>TINYINT(1)</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
            <tr>
                <td>PostgreSQL</td>
                <td><code>BOOLEAN</code></td>
                <td><code>TRUE</code>/<code>FALSE</code>, <code>'t'</code>/<code>'f'</code>, <code>1</code>/<code>0</code></td>
            </tr>
            <tr>
                <td>Oracle</td>
                <td><code>NUMBER(1)</code> or <code>CHAR(1)</code></td>
                <td><code>1</code>/<code>0</code> or <code>'Y'</code>/<code>'N'</code> by convention</td>
            </tr>
            <tr>
                <td>SQL Server</td>
                <td><code>BIT</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
            <tr>
                <td>SQLite</td>
                <td><code>INTEGER</code></td>
                <td><code>1</code> / <code>0</code></td>
            </tr>
        </table>

        <p>
            SQL Server's <code>BIT</code> accepts <code>1</code>/<code>0</code> and <code>'true'</code>/<code>'false'</code> strings but is not a proper boolean type. Oracle's PL/SQL has a boolean type, but it cannot be used as a column type in SQL DDL prior to version 23c.
        </p>
        <div class="warn"><p>Oracle 23c introduced a native <code>BOOLEAN</code> column type (<a href="https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/Data-Types.html" target="_blank" rel="noopener">Oracle 23c data types</a>), the first Oracle version to support it in SQL DDL. On older Oracle versions, use <code>NUMBER(1) CHECK (col IN (0, 1))</code>.</p></div>

        <h2 id="date-and-time">Date and Time Types</h2>
        <p>Date and time handling is where Oracle surprises developers most. Oracle's <code>DATE</code> type stores both date and time to the nearest second, unlike every other database where <code>DATE</code> is date-only. SQL Server's <code>DATETIME2</code> (preferred over the deprecated <code>DATETIME</code>) offers 100-nanosecond precision and a wider year range. MySQL has no built-in timezone-aware timestamp type.</p>
        <table>
            <tr>
                <th>Use case</th>
                <th>MySQL</th>
                <th>PostgreSQL</th>
                <th>Oracle</th>
                <th>SQL Server</th>
                <th>SQLite</th>
            </tr>
            <tr>
                <td>Date only</td>
                <td><code>DATE</code></td>
                <td><code>DATE</code></td>
                <td><code>DATE</code> (includes time!)</td>
                <td><code>DATE</code></td>
                <td><code>TEXT</code> / <code>NUMERIC</code></td>
            </tr>
            <tr>
                <td>Date + time</td>
                <td><code>DATETIME</code></td>
                <td><code>TIMESTAMP</code></td>
                <td><code>TIMESTAMP</code></td>
                <td><code>DATETIME2</code></td>
                <td><code>TEXT</code> (ISO 8601)</td>
            </tr>
            <tr>
                <td>Date + time + timezone</td>
                <td>Not natively supported</td>
                <td><code>TIMESTAMPTZ</code></td>
                <td><code>TIMESTAMP WITH TIME ZONE</code></td>
                <td><code>DATETIMEOFFSET</code></td>
                <td>Not supported</td>
            </tr>
            <tr>
                <td>Auto-set on insert</td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
                <td><code>DEFAULT NOW()</code></td>
                <td><code>DEFAULT SYSTIMESTAMP</code></td>
                <td><code>DEFAULT GETDATE()</code></td>
                <td><code>DEFAULT CURRENT_TIMESTAMP</code></td>
            </tr>
        </table>

        <p>Critical Oracle gotcha: Oracle's <code>DATE</code> stores both date <em>and</em> time (to the nearest second). This is unlike every other database where <code>DATE</code> is date-only. If you query <code>WHERE event_date = DATE '2026-05-01'</code> in Oracle and the column has a time component, you'll get no results. Use <code>TRUNC(event_date)</code> to strip the time, or use <code>TIMESTAMP</code> columns from the start.</p>

        <p>SQL Server deprecated the older <code>DATETIME</code> type in favour of <code>DATETIME2</code>, which has higher precision (100ns vs 3ms) and a wider date range. Use <code>DATETIME2</code> in all new SQL Server schemas.</p>

        <h2 id="check-constraints">Which Databases Enforce CHECK Constraints?</h2>
        <p><code>CHECK</code> constraints enforce rules at the database level — for example, limiting a status column to a fixed set of values. PostgreSQL, Oracle, and SQL Server have always enforced them fully. MySQL silently ignored CHECK constraints before version 8.0.16, released in April 2019 (<a href="https://dev.mysql.com/doc/refman/8.0/en/create-table-check-constraints.html" target="_blank" rel="noopener">MySQL CHECK constraint docs</a>). Any schema built on MySQL 5.7 or earlier that relied on CHECK for data integrity may contain dirty data.</p>
        <pre><code>-- Works the same way in MySQL 8.0.16+, PostgreSQL, Oracle, and SQL Server
CREATE TABLE orders (
    id          INT PRIMARY KEY,
    status      VARCHAR(20) NOT NULL,
    total_cents INT NOT NULL,
    CONSTRAINT chk_status  CHECK (status IN ('pending', 'paid', 'cancelled')),
    CONSTRAINT chk_total   CHECK (total_cents >= 0)
);</code></pre>

        <p>The SQL above is portable across all five databases with minor syntax adjustments. The key differences are in enforcement history:</p>
        <ul>
            <li><strong>MySQL</strong>: <code>CHECK</code> constraints were parsed but silently ignored before MySQL 8.0.16. Any existing schema built on MySQL 5.7 or earlier that uses <code>CHECK</code> for data integrity may have dirty data.</li>
            <li><strong>PostgreSQL, Oracle, SQL Server</strong>: always enforced <code>CHECK</code> constraints fully.</li>
            <li><strong>SQLite</strong>: enforced <code>CHECK</code> constraints from version 3.25.0, released 2018 (<a href="https://www.sqlite.org/releaselog/3_25_0.html" target="_blank" rel="noopener">SQLite 3.25.0 release notes</a>). Very old SQLite builds may ignore them.</li>
        </ul>

        <div class="verdict"><p>For maximum portability, always name your CHECK constraints: <code>CONSTRAINT chk_name CHECK (...)</code>. Named constraints can be dropped by name later if the schema evolves.</p></div>

        <h2 id="default-values">DEFAULT Values</h2>
        <p>Specifying default values is mostly consistent across databases, but the function names for the current timestamp differ in every dialect. MySQL also has a unique extension that automatically refreshes a timestamp column on any UPDATE — no other database supports this without a trigger.</p>
        <pre><code>-- MySQL
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- PostgreSQL
created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()  -- triggers or rules needed for auto-update

-- Oracle
created_at TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
updated_at TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL

-- SQL Server
created_at DATETIME2 NOT NULL DEFAULT GETDATE(),
updated_at DATETIME2 NOT NULL DEFAULT GETDATE()

-- SQLite
created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP</code></pre>

        <p>
            MySQL's <code>ON UPDATE CURRENT_TIMESTAMP</code> is a MySQL-only extension that automatically refreshes the column on any <code>UPDATE</code>. PostgreSQL, Oracle, and SQL Server all require a trigger to replicate this behaviour. SQLite has no trigger-free equivalent.
        </p>

        <h2 id="generated-columns">Generated (Computed) Columns</h2>
        <p>Generated columns derive their value from an expression evaluated automatically by the database. They're useful for storing pre-computed values like full names, order totals, or slugs without duplicating logic in application code. The syntax is similar across databases, but the STORED vs VIRTUAL distinction behaves differently depending on the engine.</p>

        <div class="db-grid">
            <div class="db-block">
                <p class="db-label">MySQL</p>
                <pre><code>ALTER TABLE products ADD COLUMN
  total_price DECIMAL(10,2)
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">PostgreSQL</p>
                <pre><code>ALTER TABLE products ADD COLUMN
  total_price DECIMAL(10,2)
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;
-- VIRTUAL not yet supported</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">Oracle</p>
                <pre><code>ALTER TABLE products ADD (
  total_price NUMBER
  GENERATED ALWAYS AS
    (quantity * unit_price)
  VIRTUAL
);</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQL Server</p>
                <pre><code>ALTER TABLE products ADD
  total_price AS
    (quantity * unit_price)
  PERSISTED;</code></pre>
            </div>
            <div class="db-block">
                <p class="db-label">SQLite</p>
                <pre><code>-- Must be in CREATE TABLE
total_price REAL
  GENERATED ALWAYS AS
    (quantity * unit_price)
  STORED;</code></pre>
            </div>
        </div>

        <p>
            <strong>STORED vs VIRTUAL:</strong> a stored (persisted) generated column writes the computed value to disk, making reads fast but writes slightly slower. A virtual column recomputes on every read. PostgreSQL only supports <code>STORED</code>. Oracle defaults to <code>VIRTUAL</code>. MySQL supports both.
        </p>

        <h2 id="alter-table">ALTER TABLE Differences</h2>
        <p>Modifying an existing table is where dialects diverge most sharply. SQLite has historically been the most restrictive — some operations still require recreating the table on older SQLite builds. Here are the most common operations across all five databases:</p>

        <figure aria-label="ALTER TABLE compatibility matrix across MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 760 320" role="img" aria-label="ALTER TABLE operation support across MySQL, PostgreSQL, Oracle, SQL Server, and SQLite">
                <title>ALTER TABLE Compatibility Matrix</title>
                <rect width="760" height="320" fill="#111827" rx="10"/>
                <text x="380" y="26" text-anchor="middle" fill="#f1f5f9" font-family="system-ui,-apple-system,sans-serif" font-size="14" font-weight="700">ALTER TABLE: Which Operations Are Supported?</text>
                <rect x="0" y="38" width="760" height="44" fill="#1e293b"/>
                <text x="85" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">OPERATION</text>
                <text x="233" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">MYSQL</text>
                <text x="353" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">POSTGRESQL</text>
                <text x="469" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">ORACLE</text>
                <text x="586" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">SQL SERVER</text>
                <text x="702" y="64" text-anchor="middle" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10" font-weight="700">SQLITE</text>
                <line x1="170" y1="38" x2="170" y2="298" stroke="#2d3748" stroke-width="1"/>
                <line x1="296" y1="38" x2="296" y2="298" stroke="#2d3748" stroke-width="1"/>
                <line x1="410" y1="38" x2="410" y2="298" stroke="#2d3748" stroke-width="1"/>
                <line x1="528" y1="38" x2="528" y2="298" stroke="#2d3748" stroke-width="1"/>
                <line x1="644" y1="38" x2="644" y2="298" stroke="#2d3748" stroke-width="1"/>
                <!-- Row 1: Add Column -->
                <rect x="0" y="82" width="760" height="54" fill="#111827"/>
                <text x="85" y="104" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Add</text>
                <text x="85" y="120" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Column</text>
                <rect x="175" y="89" width="116" height="40" fill="#052e16" rx="4"/><text x="233" y="114" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="301" y="89" width="104" height="40" fill="#052e16" rx="4"/><text x="353" y="114" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="415" y="89" width="108" height="40" fill="#052e16" rx="4"/><text x="469" y="114" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="533" y="89" width="106" height="40" fill="#052e16" rx="4"/><text x="586" y="114" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="649" y="89" width="106" height="40" fill="#052e16" rx="4"/><text x="702" y="114" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <!-- Row 2: Rename Column -->
                <line x1="0" y1="136" x2="760" y2="136" stroke="#1e293b" stroke-width="1"/>
                <rect x="0" y="136" width="760" height="54" fill="#0f172a"/>
                <text x="85" y="158" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Rename</text>
                <text x="85" y="174" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Column</text>
                <rect x="175" y="143" width="116" height="40" fill="#1c1206" rx="4"/><text x="233" y="158" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="11" font-weight="600">~ Partial</text><text x="233" y="174" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="10">MySQL 8+</text>
                <rect x="301" y="143" width="104" height="40" fill="#052e16" rx="4"/><text x="353" y="168" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="415" y="143" width="108" height="40" fill="#1c1206" rx="4"/><text x="469" y="158" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="11" font-weight="600">~ Partial</text><text x="469" y="174" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="10">12c R2+</text>
                <rect x="533" y="143" width="106" height="40" fill="#1c1206" rx="4"/><text x="586" y="158" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="11" font-weight="600">~ Partial</text><text x="586" y="174" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="10">sp_rename</text>
                <rect x="649" y="143" width="106" height="40" fill="#1c1206" rx="4"/><text x="702" y="158" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="11" font-weight="600">~ Partial</text><text x="702" y="174" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="10">v3.25+</text>
                <!-- Row 3: Change Data Type -->
                <line x1="0" y1="190" x2="760" y2="190" stroke="#1e293b" stroke-width="1"/>
                <rect x="0" y="190" width="760" height="54" fill="#111827"/>
                <text x="85" y="212" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Change</text>
                <text x="85" y="228" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Data Type</text>
                <rect x="175" y="197" width="116" height="40" fill="#052e16" rx="4"/><text x="233" y="222" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="301" y="197" width="104" height="40" fill="#052e16" rx="4"/><text x="353" y="222" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="415" y="197" width="108" height="40" fill="#052e16" rx="4"/><text x="469" y="222" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="533" y="197" width="106" height="40" fill="#052e16" rx="4"/><text x="586" y="222" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="649" y="197" width="106" height="40" fill="#1f0606" rx="4"/><text x="702" y="222" text-anchor="middle" fill="#ef4444" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✗ None</text>
                <!-- Row 4: Drop Column -->
                <line x1="0" y1="244" x2="760" y2="244" stroke="#1e293b" stroke-width="1"/>
                <rect x="0" y="244" width="760" height="54" fill="#0f172a"/>
                <text x="85" y="266" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Drop</text>
                <text x="85" y="282" text-anchor="middle" fill="#cbd5e1" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">Column</text>
                <rect x="175" y="251" width="116" height="40" fill="#052e16" rx="4"/><text x="233" y="276" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="301" y="251" width="104" height="40" fill="#052e16" rx="4"/><text x="353" y="276" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="415" y="251" width="108" height="40" fill="#052e16" rx="4"/><text x="469" y="276" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="533" y="251" width="106" height="40" fill="#052e16" rx="4"/><text x="586" y="276" text-anchor="middle" fill="#22c55e" font-family="system-ui,-apple-system,sans-serif" font-size="12" font-weight="600">✓ Full</text>
                <rect x="649" y="251" width="106" height="40" fill="#1c1206" rx="4"/><text x="702" y="265" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="11" font-weight="600">~ Partial</text><text x="702" y="281" text-anchor="middle" fill="#f59e0b" font-family="system-ui,-apple-system,sans-serif" font-size="10">v3.35+</text>
                <!-- Legend -->
                <rect x="0" y="298" width="760" height="22" fill="#0f172a"/>
                <circle cx="148" cy="309" r="5" fill="#22c55e"/>
                <text x="157" y="313" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10.5">Full support</text>
                <circle cx="295" cy="309" r="5" fill="#f59e0b"/>
                <text x="304" y="313" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10.5">Partial / version-limited</text>
                <circle cx="510" cy="309" r="5" fill="#ef4444"/>
                <text x="519" y="313" fill="#94a3b8" font-family="system-ui,-apple-system,sans-serif" font-size="10.5">Not supported</text>
            </svg>
            <figcaption>ALTER TABLE support across five databases. SQLite requires v3.25+ to rename columns and v3.35+ to drop them; changing a column's data type is not supported and requires recreating the table.</figcaption>
        </figure>

        <h3>Add a column</h3>
        <pre><code>-- MySQL, PostgreSQL, SQLite
ALTER TABLE users ADD COLUMN phone VARCHAR(20);

-- Oracle
ALTER TABLE users ADD (phone VARCHAR2(20));

-- SQL Server
ALTER TABLE users ADD phone NVARCHAR(20);</code></pre>

        <h3>Rename a column</h3>
        <pre><code>-- MySQL 8+, PostgreSQL
ALTER TABLE users RENAME COLUMN phone TO phone_number;

-- Oracle 12c R2+
ALTER TABLE users RENAME COLUMN phone TO phone_number;

-- SQL Server (uses a system stored procedure, not standard SQL)
EXEC sp_rename 'users.phone', 'phone_number', 'COLUMN';

-- SQLite 3.25.0+
ALTER TABLE users RENAME COLUMN phone TO phone_number;</code></pre>

        <h3>Change a column's data type</h3>
        <pre><code>-- MySQL
ALTER TABLE users MODIFY COLUMN age SMALLINT;

-- PostgreSQL
ALTER TABLE users ALTER COLUMN age TYPE SMALLINT;

-- Oracle
ALTER TABLE users MODIFY (age NUMBER(5));

-- SQL Server
ALTER TABLE users ALTER COLUMN age SMALLINT;

-- SQLite — not supported. Recreate the table.</code></pre>

        <h3>Drop a column</h3>
        <pre><code>-- MySQL, PostgreSQL, Oracle, SQL Server
ALTER TABLE users DROP COLUMN phone;

-- SQLite 3.35.0+ (March 2021)
ALTER TABLE users DROP COLUMN phone;

-- Older SQLite — not supported. Recreate the table.</code></pre>

        <div class="warn"><p>SQLite's <code>ALTER TABLE</code> support has expanded significantly but remains limited compared to other databases. Renaming columns requires SQLite 3.25.0+ and dropping columns requires SQLite 3.35.0+. Always check your SQLite version before relying on these in production (<a href="https://www.sqlite.org/lang_altertable.html" target="_blank" rel="noopener">SQLite ALTER TABLE docs</a>).</p></div>

        <h2 id="summary">Summary: Which Differences Matter Most in Practice</h2>
        <p>If you're designing a schema from scratch, focus your attention here:</p>
        <ul>
            <li><strong>Primary keys</strong>: the biggest immediate syntax difference. Know your database's keyword before writing your first <code>CREATE TABLE</code>.</li>
            <li><strong>String types</strong>: use <code>VARCHAR2</code> on Oracle, <code>NVARCHAR</code> on SQL Server, and <code>VARCHAR</code> everywhere else. SQLite accepts anything and stores it as <code>TEXT</code>.</li>
            <li><strong>Booleans</strong>: only PostgreSQL has a true native <code>BOOLEAN</code>. Use <code>TINYINT(1)</code>, <code>BIT</code>, or <code>NUMBER(1)</code> with a <code>CHECK</code> constraint elsewhere.</li>
            <li><strong>Oracle's <code>DATE</code> includes time</strong>: this surprises almost everyone coming from MySQL or PostgreSQL. Use <code>TIMESTAMP</code> in Oracle when you only want a date.</li>
            <li><strong>CHECK enforcement history</strong>: if you're working with a MySQL 5.7 or earlier schema, <code>CHECK</code> constraints were ignored. Verify data integrity before migrating.</li>
            <li><strong>SQLite ALTER TABLE limitations</strong>: SQLite is not suited for schemas that need frequent structural changes in production. Column drops and renames require a recent SQLite version.</li>
            <li><strong>Timestamp defaults</strong>: <code>CURRENT_TIMESTAMP</code> works in MySQL and SQLite; PostgreSQL prefers <code>NOW()</code>; Oracle uses <code>SYSTIMESTAMP</code>; SQL Server uses <code>GETDATE()</code> or <code>SYSDATETIME()</code>.</li>
        </ul>
        <p>
            Whichever database you're targeting, modelling your schema visually before writing DDL makes it easier to catch type mismatches and missing constraints early. Index syntax and capabilities also differ by engine, so use the <a href="/blog/mysql-indexes">MySQL composite-index guide</a> or <a href="/blog/postgresql-indexes">PostgreSQL index-types guide</a> after choosing a dialect. SQL Designer supports MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and Microsoft Access dialects and exports ready-to-run <code>CREATE TABLE</code> scripts — <a href="/demo">open the interactive database designer</a> with your own schema.
        </p>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>

            <div class="faq-item">
                <h3 class="faq-q">How do you create an auto-increment primary key in each database?</h3>
                <p class="faq-a">MySQL uses <code>AUTO_INCREMENT</code>, PostgreSQL uses <code>SERIAL</code> or <code>GENERATED ALWAYS AS IDENTITY</code>, Oracle uses <code>GENERATED ALWAYS AS IDENTITY</code> (12c+) or a separate sequence on older versions, SQL Server uses <code>IDENTITY(1,1)</code>, and SQLite uses <code>INTEGER PRIMARY KEY</code>, which auto-increments implicitly by aliasing the internal rowid.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">Which databases enforce CHECK constraints?</h3>
                <p class="faq-a">PostgreSQL, Oracle, and SQL Server have always enforced CHECK constraints fully. MySQL ignored them before version 8.0.16 (released April 2019), so schemas built on MySQL 5.7 or earlier may contain data that violates defined CHECK rules. SQLite has enforced CHECK constraints since version 3.25.0 (2018).</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What is the equivalent of VARCHAR across different databases?</h3>
                <p class="faq-a">MySQL and PostgreSQL both use <code>VARCHAR(n)</code>. Oracle requires <code>VARCHAR2(n)</code> — using plain <code>VARCHAR</code> in Oracle is not recommended. SQL Server uses <code>VARCHAR(n)</code> for ASCII and <code>NVARCHAR(n)</code> for Unicode. SQLite stores strings as <code>TEXT</code> regardless of the declared column type.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">Does Oracle have a native BOOLEAN column type?</h3>
                <p class="faq-a">Oracle 23c introduced a native <code>BOOLEAN</code> column type, the first Oracle version to support it in SQL DDL. On earlier Oracle versions, the standard workaround is <code>NUMBER(1) CHECK (col IN (0, 1))</code>, which enforces boolean semantics at the database level without a true boolean type.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">Can you rename a column directly in SQLite?</h3>
                <p class="faq-a">Yes, but only on SQLite 3.25.0 or later (2018). Dropping columns requires SQLite 3.35.0 or later (2021). On older SQLite builds, both operations require recreating the table: create a new table with the new structure, copy data across, drop the original, then rename the new table.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/features">SQL Designer Features — multi-dialect DDL export &rarr;</a></li>
                <li><a href="/blog/mysql-vs-postgresql">MySQL vs PostgreSQL — Key Differences for Schema Design &rarr;</a></li>
                <li><a href="/blog/mysql-data-types">MySQL Data Types Explained &rarr;</a></li>
                <li><a href="/blog/mysql-indexes">MySQL Composite Indexes and the Leftmost-Prefix Rule &rarr;</a></li>
                <li><a href="/blog/postgresql-indexes">PostgreSQL Index Types: B-Tree, GIN, GiST, and BRIN &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization Explained &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax and Examples &rarr;</a></li>
                <li><a href="/blog/best-free-erd-tools">Best Free ERD Tools — 10 Tested in 2026 &rarr;</a></li>
                <li><a href="/blog/postgresql-data-types">PostgreSQL Data Types — NUMERIC, TIMESTAMPTZ, JSONB, UUID &rarr;</a></li>
                <li><a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">SQL-Aware vs. Generic ER Diagram Tools &rarr;</a></li>
                <li><a href="/blog/database-schema-examples">Database Schema Examples &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Design your database schema visually</h2>
    <p>SQL Designer lets you model tables, relationships, and constraints visually and export a CREATE TABLE script for MySQL, PostgreSQL, Oracle, SQL Server, or SQLite. Free, browser-based, no installation required.</p>
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
