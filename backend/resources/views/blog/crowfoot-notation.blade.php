@extends('layouts.main')

@section('title', "Crow's Foot Notation — ER Diagram Cardinality Explained")

@section('head')
    <meta name="description"
          content="Crow's foot notation is the ER diagram standard since Gordon Everest's 1976 paper. Learn the six cardinality symbols with mandatory, optional, and SQL.">
    <meta name="author" content="Dmitriy Snyatkov">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/crowfoot-notation">
    <meta property="og:title" content="Crow's Foot Notation — ER Diagram Cardinality Explained">
    <meta property="og:description"
          content="Learn crow's foot notation for ER diagrams — the symbols for cardinality (one, many, zero-or-one) with examples for MySQL and PostgreSQL schema design.">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:url" content="https://sql-designer.com/blog/crowfoot-notation">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer — ER diagram with crow's foot notation">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Crow's Foot Notation — ER Diagram Cardinality Explained">
    <meta name="twitter:description" content="Crow's foot notation explained — learn the cardinality symbols for ER diagrams with practical database design examples.">
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
                { "@type": "ListItem", "position": 3, "name": "Crow's Foot Notation", "item": "https://sql-designer.com/blog/crowfoot-notation" }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "TechArticle",
            "headline": "Crow's Foot Notation — ER Diagram Cardinality Explained",
            "description": "Crow's foot notation explained — the symbols for one-to-one, one-to-many, and many-to-many relationships in ER diagrams, with examples for MySQL and PostgreSQL schemas.",
            "image": { "@type": "ImageObject", "url": "https://sql-designer.com/images/designer_screenshot.webp", "width": 2240, "height": 1111 },
            "url": "https://sql-designer.com/blog/crowfoot-notation",
            "datePublished": "2026-04-16",
            "dateModified": "2026-05-16",
            "author": { "@type": "Person", "name": "Dmitriy Snyatkov", "url": "https://sql-designer.com/about", "sameAs": "https://github.com/Snydi", "worksFor": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com" } },
            "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "sameAs": "https://github.com/Snydi/sqldesigner", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } },
            "speakable": { "@type": "SpeakableSpecification", "cssSelector": [".page-sub"] },
            "mainEntityOfPage": { "@type": "WebPage", "@id": "https://sql-designer.com/blog/crowfoot-notation" }
        },
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "What is crow's foot notation?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Crow's foot notation is a graphical convention for representing cardinality and optionality in ER diagrams. It uses symbols at the end of relationship lines — a single bar for 'one', a circle for 'zero', and a three-pronged crow's foot for 'many' — to show how many records on each side of a relationship can exist." }
                },
                {
                    "@type": "Question",
                    "name": "What does the crow's foot symbol mean in an ER diagram?",
                    "acceptedAnswer": { "@type": "Answer", "text": "The crow's foot symbol (three diverging lines at the end of a relationship line) represents the 'many' side of a relationship. It indicates that multiple records in that table can relate to a single record in the connected table." }
                },
                {
                    "@type": "Question",
                    "name": "How do you represent a one-to-many relationship in crow's foot notation?",
                    "acceptedAnswer": { "@type": "Answer", "text": "A one-to-many relationship uses a single vertical bar on the 'one' side and a crow's foot on the 'many' side. For example, one user can have many orders: the users end of the line has a single bar, and the orders end has a crow's foot." }
                },
                {
                    "@type": "Question",
                    "name": "What is the difference between mandatory and optional in crow's foot notation?",
                    "acceptedAnswer": { "@type": "Answer", "text": "Mandatory (shown by a vertical bar next to the entity) means a related record must exist — in SQL this corresponds to a NOT NULL foreign key. Optional (shown by a circle) means the relationship is not required — the foreign key column allows NULL." }
                },
                {
                    "@type": "Question",
                    "name": "How are many-to-many relationships modelled in crow's foot notation?",
                    "acceptedAnswer": { "@type": "Answer", "text": "A many-to-many relationship shows a crow's foot at both ends of the line. In a relational database this cannot be implemented with a single foreign key — it requires a junction table (e.g., product_tags) containing foreign keys to both related tables." }
                }
            ]
        },
        {
            "@context": "https://schema.org",
            "@type": "DefinedTerm",
            "name": "Crow's Foot Notation",
            "description": "Crow's foot notation is a graphical convention for representing cardinality and optionality in entity-relationship (ER) diagrams. It uses symbols at the ends of relationship lines: a single vertical bar for 'exactly one', a circle for 'zero', and three diverging lines (the crow's foot) for 'many'. Combined, these produce six cardinality markers — one-to-one, one-to-many, many-to-many, and their optional variants — that translate directly to foreign key definitions in a relational database.",
            "inDefinedTermSet": { "@type": "DefinedTermSet", "name": "Database Design Glossary", "url": "https://sql-designer.com/blog" },
            "url": "https://sql-designer.com/blog/crowfoot-notation"
        },
        {
            "@context": "https://schema.org",
            "@type": "VideoObject",
            "name": "Crow's Foot Symbols with Cardinalities",
            "description": "An academic walkthrough of all crow's foot cardinality symbols, covering one-to-many, many-to-many, and one-to-one relationships in ER diagrams.",
            "thumbnailUrl": "https://img.youtube.com/vi/Oxda-LTLTOc/hqdefault.jpg",
            "uploadDate": "2023-02-03",
            "embedUrl": "https://www.youtube.com/embed/Oxda-LTLTOc",
            "url": "https://www.youtube.com/watch?v=Oxda-LTLTOc",
            "author": { "@type": "Person", "name": "Daniel Soper" }
        }
        ]
        @endverbatim
    </script>
@endsection

@section('content')

<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>ER Diagrams</span></p>
        <p class="post-eyebrow">April 2026 · <time datetime="2026-05-16">Last updated: May 2026</time> · by <a href="/about" style="color:var(--color-primary-text);">Dmitriy Snyatkov</a>, database tool developer · 8 min read</p>
        <h1 class="page-h1">Crow&rsquo;s Foot Notation &mdash; ER Diagram Cardinality Explained</h1>
        <p class="page-sub">Crow&rsquo;s foot notation is a graphical symbol system for representing cardinality in entity-relationship (ER) diagrams. Each relationship line carries two symbols at both ends: the outer shows maximum cardinality (one or many), the inner shows whether participation is mandatory or optional. Those symbols map directly to foreign key constraints and <code>NOT NULL</code> decisions in SQL. Developed by Gordon Everest in 1976, it&rsquo;s now the industry standard in tools, textbooks, and documentation worldwide.</p>
    </div>
</section>

<div class="article-layout">
    <aside class="article-sidebar" aria-label="Article navigation">
        <p class="sidebar-label">On this page</p>
        <ul class="sidebar-nav">
            <li><a href="#what-is">What Is It?</a></li>
            <li><a href="#symbols">The Symbols</a></li>
            <li><a href="#relationship-types">Relationship Types</a></li>
            <li><a href="#optionality">Optionality</a></li>
            <li><a href="#how-to-read">How to Read</a></li>
            <li><a href="#sql-designer">SQL Designer</a></li>
            <li><a href="#faq">FAQ</a></li>
        </ul>
    </aside>

    <article class="article-body">

        <div class="key-takeaways">
            <p class="kt-label">Key Takeaways</p>
            <ul>
                <li>Crow&rsquo;s foot notation was introduced by Gordon Everest in his 1976 IEEE paper and is now the de facto standard for ER diagram cardinality.</li>
                <li>Each line end carries two symbols: the outer shows maximum cardinality (one or many), the inner shows minimum cardinality (mandatory or optional).</li>
                <li>The foreign key column always belongs to the &ldquo;many&rdquo; side, the table with the crow&rsquo;s foot.</li>
                <li>Mandatory relationships map to <code>NOT NULL</code>; optional relationships allow <code>NULL</code> in the foreign key column.</li>
            </ul>
        </div>

        <h2 id="what-is">What Is Crow&rsquo;s Foot Notation?</h2>
        <p>
            Crow&rsquo;s foot notation is the graphical standard for showing how many records on each side of a relationship can exist in a relational database. Gordon Everest introduced it in his 1976 IEEE paper &ldquo;Basic Data Structure Models Explained with a Common Example,&rdquo; presented at the Fifth Texas Conference on Computing Systems in Austin, Texas (<a href="https://www.red-gate.com/blog/crow-s-foot-notation/" target="_blank" rel="noopener">Redgate</a>). Everest originally called the symbol an &ldquo;inverted arrow&rdquo; or &ldquo;fork.&rdquo; The term &ldquo;crow&rsquo;s foot&rdquo; emerged through academic and industry use over the following decade.
        </p>
        <p>
            The notation spread through ICL, CACI consultancy, and later Oracle UK before becoming the default in most modern database design tools. Today, the relational database market is valued at $74 billion (2024) and projected to reach $84 billion in 2025 at a 13.3% annual growth rate (<a href="https://www.thebusinessresearchcompany.com/report/relational-database-global-market-report" target="_blank" rel="noopener">The Business Research Company, 2025</a>). In that context, reading a crow&rsquo;s foot diagram is a baseline skill for database professionals, not an advanced one.
        </p>
        <p>
            You&rsquo;ll also hear it called &ldquo;chicken foot notation&rdquo; or &ldquo;IE notation&rdquo; (after the Information Engineering methodology). James Martin and Clive Finkelstein popularized it through their information engineering frameworks in the 1980s, which helped cement it as the industry default.
        </p>

        <h2 id="symbols">The Crow&rsquo;s Foot Symbols</h2>
        <p>
            Three primitive symbols make up the entire crow&rsquo;s foot vocabulary. The outer symbol (furthest from the entity box) indicates maximum cardinality. The inner symbol indicates minimum cardinality, meaning whether at least one record must exist. You read both together to get the full picture of what a relationship allows.
        </p>
        <table>
            <thead>
                <tr><th>Symbol at line end</th><th>Meaning</th></tr>
            </thead>
            <tbody>
                <tr><td>Single vertical bar ( | )</td><td>Exactly one (mandatory)</td></tr>
                <tr><td>Circle ( &#9675; )</td><td>Zero (optional)</td></tr>
                <tr><td>Crow&rsquo;s foot ( &#8767; )</td><td>Many</td></tr>
            </tbody>
        </table>
        <p>
            These combine in pairs to produce four cardinality labels. The inner symbol is closest to the entity; the outer symbol is next:
        </p>
        <table>
            <thead>
                <tr><th>Combined symbol</th><th>Reads as</th><th>SQL mapping</th></tr>
            </thead>
            <tbody>
                <tr><td>One and only one ( | | )</td><td>Exactly one, mandatory</td><td><code>NOT NULL</code> FK + <code>UNIQUE</code></td></tr>
                <tr><td>Zero or one ( &#9675; | )</td><td>Optional, at most one</td><td>Nullable FK + <code>UNIQUE</code></td></tr>
                <tr><td>One or many ( | &#8767; )</td><td>At least one, mandatory many</td><td><code>NOT NULL</code> FK</td></tr>
                <tr><td>Zero or many ( &#9675; &#8767; )</td><td>Optional many, zero or more</td><td>Nullable FK</td></tr>
            </tbody>
        </table>
        <p>
            The two combinations you&rsquo;ll see most often day-to-day are &ldquo;zero or many&rdquo; and &ldquo;one and only one.&rdquo; Together they describe a standard optional one-to-many relationship, the backbone of most relational schemas. Worth knowing those two cold before the others.
        </p>

        <figure>
            <img src="https://images.pexels.com/photos/1148820/pexels-photo-1148820.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="Server racks in a modern data center representing the relational database infrastructure that crow's foot notation helps document"
                 loading="lazy" width="1260" height="750">
            <figcaption>The relational database market is projected to reach $84 billion in 2025. Crow&rsquo;s foot notation is how teams document the schemas behind that infrastructure. (Photo: Pexels / panumas nikhomkhai)</figcaption>
        </figure>

        <h2 id="relationship-types">Relationship Types in Crow&rsquo;s Foot Notation</h2>
        <p>
            Three relationship types cover every scenario in a relational schema. One-to-many is the most common by far, appearing in patterns like customers to orders, posts to comments, and users to sessions. Many-to-many relationships can&rsquo;t be modeled with a single foreign key — they require a junction table to resolve into two one-to-many relationships. One-to-one comes up less often, but it has clear use cases in schema decomposition.
        </p>

        <h3>One-to-One (1:1)</h3>
        <p>
            A single bar on both ends of the line. Each record in table A relates to exactly one record in table B, and vice versa.
        </p>
        <p>
            <strong>Example:</strong> A <code>users</code> table and a <code>user_profiles</code> table, where each user has exactly one profile and each profile belongs to exactly one user. The foreign key (<code>user_profiles.user_id</code>) references <code>users.id</code> with a <code>UNIQUE</code> constraint to enforce the one-to-one cardinality. This pattern is useful when you want to keep large or rarely-accessed columns in a separate table without polluting the main entity.
        </p>

        <h3>One-to-Many (1:N)</h3>
        <p>
            A single bar on one end, a crow&rsquo;s foot on the other. One record in table A relates to many records in table B, but each record in B relates to exactly one record in A.
        </p>
        <p>
            <strong>Example:</strong> A <code>users</code> table and an <code>orders</code> table. One user can place many orders, but each order belongs to exactly one user. The foreign key (<code>orders.user_id</code>) references <code>users.id</code>. This is the most common relationship type in relational databases, and the one you&rsquo;ll design most frequently.
        </p>

        <h3>Many-to-Many (N:M)</h3>
        <p>
            A crow&rsquo;s foot on both ends. Many records in table A relate to many records in table B.
        </p>
        <p>
            <strong>Example:</strong> A <code>products</code> table and a <code>tags</code> table. One product can have many tags, and one tag can apply to many products. Many-to-many relationships can&rsquo;t be represented by a single foreign key, so they require a junction table (also called a join table or associative entity), such as <code>product_tags</code>, with foreign keys to both <code>products</code> and <code>tags</code>.
        </p>

        <figure aria-label="Relational database market size chart">
            <svg viewBox="0 0 480 210" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart: relational database market grew from $74.09 billion in 2024 to a projected $83.98 billion in 2025" style="width:100%;border-radius:6px;">
                <title>Relational Database Market Size 2024 vs 2025</title>
                <rect width="480" height="210" fill="#12121e" rx="8"/>
                <text x="240" y="22" text-anchor="middle" font-family="system-ui,-apple-system,sans-serif" font-size="10.5" fill="#8080a0" letter-spacing="0.5">RELATIONAL DATABASE MARKET SIZE (USD BILLIONS)</text>
                <line x1="55" y1="155" x2="430" y2="155" stroke="#2e2e48" stroke-width="1"/>
                <line x1="55" y1="122" x2="430" y2="122" stroke="#22223a" stroke-width="1" stroke-dasharray="4,3"/>
                <line x1="55" y1="89" x2="430" y2="89" stroke="#22223a" stroke-width="1" stroke-dasharray="4,3"/>
                <line x1="55" y1="56" x2="430" y2="56" stroke="#22223a" stroke-width="1" stroke-dasharray="4,3"/>
                <text x="50" y="158" text-anchor="end" font-family="system-ui,sans-serif" font-size="9.5" fill="#555570">$0</text>
                <text x="50" y="125" text-anchor="end" font-family="system-ui,sans-serif" font-size="9.5" fill="#555570">$30B</text>
                <text x="50" y="92" text-anchor="end" font-family="system-ui,sans-serif" font-size="9.5" fill="#555570">$60B</text>
                <text x="50" y="59" text-anchor="end" font-family="system-ui,sans-serif" font-size="9.5" fill="#555570">$90B</text>
                <rect x="120" y="73" width="90" height="82" fill="#4e6ee8" rx="4"/>
                <text x="165" y="67" text-anchor="middle" font-family="system-ui,sans-serif" font-size="11.5" fill="#d0d0f0" font-weight="600">$74.09B</text>
                <text x="165" y="172" text-anchor="middle" font-family="system-ui,sans-serif" font-size="10.5" fill="#8080a0">2024</text>
                <rect x="270" y="63" width="90" height="92" fill="#6484ff" rx="4"/>
                <text x="315" y="57" text-anchor="middle" font-family="system-ui,sans-serif" font-size="11.5" fill="#d0d0f0" font-weight="600">$83.98B</text>
                <text x="315" y="172" text-anchor="middle" font-family="system-ui,sans-serif" font-size="10.5" fill="#8080a0">2025 (projected)</text>
                <text x="315" y="46" text-anchor="middle" font-family="system-ui,sans-serif" font-size="9" fill="#50e080" font-weight="600">+13.3% CAGR</text>
                <text x="240" y="200" text-anchor="middle" font-family="system-ui,sans-serif" font-size="8.5" fill="#44445a">Source: The Business Research Company, 2025</text>
            </svg>
            <figcaption>The relational database market is growing at 13.3% annually, reaching a projected $83.98 billion in 2025. (Source: The Business Research Company, 2025)</figcaption>
        </figure>

        <h2 id="optionality">Optionality: Mandatory vs. Optional</h2>
        <p>
            The inner symbol at each line end determines whether the relationship is required. Get this right and your schema rejects invalid data at the database level. Get it wrong and you&rsquo;ll find orphaned records and broken joins in production.
        </p>
        <ul>
            <li><strong>Mandatory ( | )</strong> — a record must exist on that side. In SQL, this is a <code>NOT NULL</code> constraint on the foreign key column.</li>
            <li><strong>Optional ( &#9675; )</strong> — a record may or may not exist. The foreign key column allows <code>NULL</code>, so the relationship is optional.</li>
        </ul>
        <p>
            Here&rsquo;s a concrete example. An <code>orders</code> table might have an optional <code>coupon_id</code> foreign key. An order can exist without a coupon, so <code>coupon_id</code> is nullable. In crow&rsquo;s foot notation, the <code>coupons</code> end of the line shows a circle (zero-or-one) rather than a bar (exactly one).
        </p>
        <p>
            Compare that to <code>orders.user_id</code>, which is mandatory: every order must belong to a user. That end gets a bar, and the column gets <code>NOT NULL</code>. Same relationship line, but the inner symbols at each end tell completely different stories about what the schema will enforce.
        </p>

        <figure aria-label="Video: Crow's foot notation academic walkthrough">
            <div class="video-wrap">
                <iframe
                    src="https://www.youtube-nocookie.com/embed/Oxda-LTLTOc"
                    title="Crow's Foot Symbols with Cardinalities — Dr. Daniel Soper"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    aria-label="Video: Crow's Foot Symbols with Cardinalities by Dr. Daniel Soper">
                </iframe>
            </div>
            <figcaption>Crow&rsquo;s Foot Symbols with Cardinalities &mdash; Dr. Daniel Soper (academic database curriculum, 2023). A structured walkthrough of all six cardinality symbol combinations.</figcaption>
            <noscript><a href="https://www.youtube.com/watch?v=Oxda-LTLTOc" target="_blank" rel="noopener">Watch: Crow&rsquo;s Foot Symbols with Cardinalities &mdash; Dr. Daniel Soper</a></noscript>
        </figure>

        <h2 id="how-to-read">How to Read Crow&rsquo;s Foot Notation Step by Step</h2>
        <p>
            Reading a crow&rsquo;s foot diagram becomes automatic after a few practice runs. Here&rsquo;s the five-step process that works for any ER diagram, regardless of the tool or schema complexity.
        </p>
        <ol>
            <li><strong>Look at both ends of the line.</strong> Each end carries two symbols. The outer symbol shows maximum cardinality; the inner shows minimum cardinality (optionality).</li>
            <li><strong>Read the maximum cardinality.</strong> A single vertical line means &ldquo;at most one.&rdquo; Three diverging lines (the crow&rsquo;s foot) means &ldquo;many.&rdquo; A crow&rsquo;s foot at the <code>orders</code> end means one customer can have many orders.</li>
            <li><strong>Read the minimum cardinality.</strong> A bar next to the entity means mandatory (NOT NULL foreign key). A circle means optional (nullable foreign key).</li>
            <li><strong>Combine both symbols for the full label.</strong> Bar + crow&rsquo;s foot = one or more. Circle + crow&rsquo;s foot = zero or more. Bar + bar = exactly one. Circle + bar = zero or one.</li>
            <li><strong>Find the foreign key direction.</strong> The FK column always lives on the &ldquo;many&rdquo; side — the table with the crow&rsquo;s foot. That table has a foreign key column referencing the primary key of the &ldquo;one&rdquo; side.</li>
        </ol>

        <figure>
            <img src="https://images.pexels.com/photos/3803517/pexels-photo-3803517.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                 alt="A developer reviewing data output on a monitor, representing practical schema analysis using crow's foot notation"
                 loading="lazy" width="1260" height="750">
            <figcaption>Reading crow&rsquo;s foot notation directly from a schema diagram is a practical skill that speeds up code review, onboarding, and documentation. (Photo: Pexels)</figcaption>
        </figure>

        <h2 id="sql-designer">How SQL Designer Uses Crow&rsquo;s Foot Notation</h2>
        <p>
            When you draw a relationship line in SQL Designer between a foreign key column and the primary key it references, crow&rsquo;s foot notation is applied automatically based on the column constraints:
        </p>
        <ul>
            <li>The referenced (parent) side always shows a single bar — one record is referenced</li>
            <li>The referencing (child) side shows a crow&rsquo;s foot — many records can hold the same FK value</li>
            <li>If the FK column is <code>NOT NULL</code>, the child end shows a mandatory crow&rsquo;s foot (one-or-many)</li>
            <li>If the FK column allows <code>NULL</code>, the child end shows an optional crow&rsquo;s foot (zero-or-many)</li>
        </ul>
        <p>
            The result is a complete, readable ER diagram using standard crow&rsquo;s foot notation, ready to share with your team or embed in technical documentation. SQL Designer is a free <a href="/blog/er-diagram-maker-online">online ER diagram maker</a> — open the canvas and start drawing relationships immediately, no install required.
        </p>

        <section class="faq-section" aria-label="Frequently asked questions">
            <h2 id="faq">Frequently Asked Questions</h2>

            <div class="faq-item">
                <h3 class="faq-q">What is crow&rsquo;s foot notation?</h3>
                <p class="faq-a">Crow&rsquo;s foot notation is a graphical convention for representing cardinality and optionality in ER diagrams. It uses symbols at the end of relationship lines — a single bar for &ldquo;one,&rdquo; a circle for &ldquo;zero,&rdquo; and a three-pronged crow&rsquo;s foot for &ldquo;many&rdquo; — to show how many records on each side of a relationship can exist.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What does the crow&rsquo;s foot symbol mean in an ER diagram?</h3>
                <p class="faq-a">The crow&rsquo;s foot symbol (three diverging lines at the end of a relationship line) represents the &ldquo;many&rdquo; side of a relationship. It means multiple records in that table can relate to a single record in the connected table.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">How do you represent a one-to-many relationship in crow&rsquo;s foot notation?</h3>
                <p class="faq-a">A one-to-many relationship uses a single vertical bar on the &ldquo;one&rdquo; side and a crow&rsquo;s foot on the &ldquo;many&rdquo; side. For example, one user can have many orders: the <code>users</code> end has a single bar, and the <code>orders</code> end has a crow&rsquo;s foot.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">What is the difference between mandatory and optional in crow&rsquo;s foot notation?</h3>
                <p class="faq-a">Mandatory (shown by a vertical bar next to the entity) means a related record must exist — in SQL this maps to a <code>NOT NULL</code> foreign key. Optional (shown by a circle) means the relationship isn&rsquo;t required, and the foreign key column allows <code>NULL</code>.</p>
            </div>

            <div class="faq-item">
                <h3 class="faq-q">How are many-to-many relationships modelled in crow&rsquo;s foot notation?</h3>
                <p class="faq-a">A many-to-many relationship shows a crow&rsquo;s foot at both ends of the line. In a relational database this can&rsquo;t be implemented with a single foreign key — it requires a junction table (e.g., <code>product_tags</code>) containing foreign keys to both related tables.</p>
            </div>
        </section>

        <nav class="related-nav" aria-label="Related articles">
            <p class="related-label">Related Articles</p>
            <ul>
                <li><a href="/blog/database-schema-examples">Database Schema Examples — Common Patterns for MySQL and PostgreSQL &rarr;</a></li>
                <li><a href="/blog/mysql-foreign-key">MySQL Foreign Key — Syntax, Examples, and Best Practices &rarr;</a></li>
                <li><a href="/blog/database-normalization">Database Normalization — 1NF, 2NF, 3NF Explained &rarr;</a></li>
                <li><a href="/blog/best-free-erd-tools">10 Best Free ERD Tools in 2026 &rarr;</a></li>
                <li><a href="/blog/database-designer">Free Online Database Designer — Visual Schema Editor &rarr;</a></li>
                <li><a href="/blog/er-diagram-maker-online">Free Online ER Diagram Maker — Draw Tables, Export SQL &rarr;</a></li>
                <li><a href="/blog/create-database-schema-online">How to Create a Database Schema Online — Step-by-Step &rarr;</a></li>
            </ul>
        </nav>
    </article>
</div>

<section class="docs-cta">
    <h2>Draw ER diagrams with crow&rsquo;s foot notation &mdash; free</h2>
    <p>SQL Designer applies crow&rsquo;s foot notation automatically when you draw relationships between tables. No install, no subscription &mdash; just open the canvas and start designing.</p>
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
