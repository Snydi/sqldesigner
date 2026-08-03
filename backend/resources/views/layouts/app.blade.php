<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if (request()->is('demo'))
        <title>Free ER Diagram Maker Online — Design and Export SQL</title>
        <meta name="description" content="Create ER diagrams online for MySQL, PostgreSQL, SQLite, Oracle, SQL Server and MS Access. Draw tables and relationships, then export SQL in your browser.">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="https://sql-designer.com/demo">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="SQL Designer">
        <meta property="og:title" content="Free ER Diagram Maker Online — Design and Export SQL">
        <meta property="og:description" content="Draw tables and relationships in a working browser-based ER diagram editor, then export SQL for six database dialects.">
        <meta property="og:url" content="https://sql-designer.com/demo">
        <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Free ER Diagram Maker Online — Design and Export SQL">
        <meta name="twitter:description" content="Draw tables and relationships in a working browser-based ER diagram editor, then export SQL for six database dialects.">
        <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
        <style>
            body { background: var(--bg-page); }
            .demo-seo-content {
                max-width: 960px;
                margin: 0 auto;
                padding: 4rem clamp(1.25rem, 5vw, 4rem);
                color: var(--text-primary);
                font-size: 16px;
                line-height: 1.7;
            }
            .demo-seo-content h1,
            .demo-seo-content h2 { line-height: 1.2; }
            .demo-seo-content h1 { margin: 0 0 1.5rem; font-size: clamp(2rem, 5vw, 3.5rem); }
            .demo-seo-content h2 { margin: 2.5rem 0 1rem; font-size: clamp(1.5rem, 3vw, 2rem); }
            .demo-seo-content p { margin: 0 0 1rem; font-size: 16px; color: var(--text-secondary); }
            .demo-seo-content a { color: var(--color-primary-text); text-decoration: underline; }
            .demo-seo-content code { font-size: 16px; }
        </style>
        <script type="application/ld+json">
        @verbatim
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "@id": "https://sql-designer.com/#app",
            "name": "SQL Designer",
            "url": "https://sql-designer.com/demo",
            "description": "Browser-based ER diagram maker for designing tables and relationships and exporting SQL for six database dialects.",
            "applicationCategory": "DeveloperApplication",
            "operatingSystem": "Any",
            "browserRequirements": "Requires a modern browser with JavaScript enabled",
            "sameAs": [
                "https://github.com/Snydi/sqldesigner",
                "https://gitlab.com/Snydi/sql-designer",
                "https://discord.gg/vFwgX7qKqA"
            ],
            "isAccessibleForFree": true,
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "USD"
            },
            "provider": { "@id": "https://sql-designer.com/#organization" },
            "featureList": [
                "Interactive entity-relationship diagram canvas",
                "Foreign key relationship modeling",
                "MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access support",
                "SQL, JSON, Laravel migration, and PNG export"
            ]
        }
        @endverbatim
        </script>
    @else
        <title>SQL Designer</title>
        <meta name="description" content="Visually design MySQL and PostgreSQL schemas with drag-and-drop. Create tables, define relationships, and export SQL scripts — free and browser-based.">
        <meta name="robots" content="noindex, follow">
    @endif
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="manifest" href="/manifest.json">
    <link rel="license" href="https://github.com/Snydi/sqldesigner/blob/master/LICENSE">
    @vite(['src/css/app.css', 'src/main.js'])
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        window.gtag = gtag;
        gtag('js', new Date());
        gtag('config', 'G-4L116MPX4C');
        function loadGtag() {
            if (window._gtagLoaded) return;
            window._gtagLoaded = true;
            const s = document.createElement('script');
            s.async = true;
            s.src = 'https://www.googletagmanager.com/gtag/js?id=G-4L116MPX4C';
            document.head.appendChild(s);
        }
        ['click','scroll','keydown','touchstart'].forEach(function(e) {
            document.addEventListener(e, loadGtag, {once: true, passive: true});
        });
        setTimeout(loadGtag, 8000);
    </script>
</head>
<body>
<div id="app"></div>
@if (request()->is('demo'))
<main class="demo-seo-content">
    <h1>Free Online ER Diagram Maker with SQL Export</h1>
    <p>Design an entity-relationship diagram directly in the editor above. Add tables and columns, connect foreign keys, choose crow&rsquo;s foot cardinality, and keep the visual model aligned with executable database definitions.</p>
    <p>SQL Designer supports MySQL, PostgreSQL, SQLite, Oracle, SQL Server, and MS Access. You can start without an account, import an existing <code>CREATE TABLE</code> script, and export the finished design as SQL, JSON, a Laravel migration, or PNG.</p>
    <h2>From diagram to database schema in one browser tab</h2>
    <p>The editor is the primary ER diagram maker for sql-designer.com: it opens with a sample schema and is ready to edit immediately. For guidance rather than tool use, read the <a href="/blog/er-diagram-maker-online">ER diagram tools guide</a>, follow the <a href="/blog/create-database-schema-online">step-by-step schema tutorial</a>, or compare <a href="/blog/database-designer">database design software features</a>.</p>
</main>
@endif
</body>
</html>
