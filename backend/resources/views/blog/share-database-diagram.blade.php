@extends('layouts.main')

@section('title', 'How to Share a Database Diagram Online — Shareable Links & Embeds')

@section('head')
    <meta name="description"
          content="Share your database schema online with a shareable link or embed it as an interactive iframe. Control access with approval-based permissions.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/blog/share-database-diagram">
    <meta property="og:title" content="How to Share a Database Diagram Online — Shareable Links & Embeds">
    <meta property="og:description"
          content="Share your database schema with a link or embed it as an interactive diagram. Read-only, editable, or approval-based access — all free on SQL Designer.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sql-designer.com/blog/share-database-diagram">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.png">
    <meta property="og:image:width" content="2556">
    <meta property="og:image:height" content="1271">
    <meta property="og:image:alt" content="SQL Designer — visual MySQL and PostgreSQL schema editor">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="How to Share a Database Diagram Online — Shareable Links & Embeds">
    <meta name="twitter:description" content="Share a database schema online with a shareable link or interactive embed. Control who can view or edit — all free on SQL Designer.">
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
                    { "@type": "ListItem", "position": 3, "name": "How to Share a Database Diagram Online", "item": "https://sql-designer.com/blog/share-database-diagram" }
                ]
            },
            {
                "@context": "https://schema.org",
                "@type": "TechArticle",
                "headline": "How to Share a Database Diagram Online — Shareable Links & Embeds",
                "description": "How to share a database schema diagram online using shareable links and embeddable iframes, with read-only, editable, or approval-based access control.",
                "image": "https://sql-designer.com/images/designer_screenshot.png",
                "url": "https://sql-designer.com/blog/share-database-diagram",
                "datePublished": "2026-04-11",
                "dateModified": "2026-04-11",
                "author": { "@type": "Organization", "name": "SQL Designer" },
                "publisher": { "@type": "Organization", "name": "SQL Designer", "url": "https://sql-designer.com", "logo": { "@type": "ImageObject", "url": "https://sql-designer.com/favicon-192x192.png" } }
            },
            {
                "@context": "https://schema.org",
                "@type": "HowTo",
                "name": "How to Share a Database Diagram Online",
                "description": "Steps to share a database schema diagram using a shareable link or embeddable iframe in SQL Designer.",
                "step": [
                    {
                        "@type": "HowToStep",
                        "position": 1,
                        "name": "Open your diagram",
                        "text": "Log in to SQL Designer and open the diagram you want to share."
                    },
                    {
                        "@type": "HowToStep",
                        "position": 2,
                        "name": "Open sharing settings",
                        "text": "Click the Share button in the toolbar to open the sharing panel."
                    },
                    {
                        "@type": "HowToStep",
                        "position": 3,
                        "name": "Choose an access mode",
                        "text": "Select read-only (anyone with the link can view), editable (anyone can edit), or approval-based (you approve each visitor)."
                    },
                    {
                        "@type": "HowToStep",
                        "position": 4,
                        "name": "Copy the link or embed code",
                        "text": "Copy the shareable URL to send to teammates, or copy the iframe embed code to paste into your documentation, wiki, or website."
                    }
                ]
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

        .blog-post ol {
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

        .blog-post code {
            background: var(--bg-elevated);
            padding: 0.1em 0.4em;
            border-radius: 3px;
            font-size: 0.85em;
            color: var(--text-primary);
        }

        .blog-post pre {
            background: var(--bg-elevated);
            border-radius: 6px;
            padding: 1rem 1.2rem;
            overflow-x: auto;
            margin: 0 0 1.5rem;
        }

        .blog-post pre code {
            background: none;
            padding: 0;
            font-size: 0.82rem;
            line-height: 1.7;
        }

        .blog-post .access-mode-card {
            background: var(--bg-surface);
            border-radius: 6px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            margin-bottom: 1rem;
            border-left: 3px solid var(--color-primary);
        }

        .blog-post .access-mode-card h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-primary);
            background-color: transparent;
            margin: 0 0 0.4rem;
        }

        .blog-post .access-mode-card p {
            margin: 0;
            font-size: 0.85rem;
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
        <p class="breadcrumb"><a href="/blog">Blog</a> &rsaquo; Guides</p>
        <p class="post-meta"><time datetime="2026-04-11">April 2026</time> &mdash; 5 min read</p>
        <h1>How to Share a Database Diagram Online — Shareable Links &amp; Embeds</h1>

        <p class="intro">
            Sending a screenshot of a database schema works once — then it goes stale the moment you make a change.
            Shareable diagram links solve this: teammates, reviewers, and stakeholders always see the live, current
            version, and you control exactly what they can do with it. Here's how sharing and embedding work in
            SQL Designer, and when to use each mode.
        </p>

        <h2>The Problem with Screenshots and Exported Images</h2>
        <p>
            The standard way to share a database diagram is to export it as an image — a PNG or PDF — and attach it
            to a message or document. This approach has two fundamental problems:
        </p>
        <ul>
            <li><strong>Snapshots go stale.</strong> As soon as you update the schema, any previously shared image
                is out of date. There's no way for the recipient to know the diagram has changed.</li>
            <li><strong>No interaction.</strong> An image can't be panned, zoomed, or inspected table by table.
                Large schemas become unreadable at any single zoom level.</li>
        </ul>
        <p>
            A shareable link solves both problems: the recipient always sees the current diagram, and they can
            interact with it in the browser.
        </p>

        <h2>Three Ways to Share a Database Diagram in SQL Designer</h2>

        <div class="access-mode-card">
            <h3>Read-Only Link</h3>
            <p>Anyone with the link can view and interact with the diagram — pan, zoom, inspect tables and columns —
                but cannot make changes. Use this when you're sharing a schema for review, documentation, or
                approval. The link works without login, so recipients don't need an account.</p>
        </div>

        <div class="access-mode-card">
            <h3>Editable Link</h3>
            <p>Anyone with the link can view and edit the diagram in real time. Useful when you're working with a
                trusted collaborator who doesn't have an account on the tool, or when you want to let a contractor
                or consultant work on the schema without a formal invite process.</p>
        </div>

        <div class="access-mode-card">
            <h3>Approval-Based Access</h3>
            <p>Each visitor who clicks the link is placed in a pending queue. You see who's waiting and approve or
                deny each one individually. Approved visitors are added to a visitor list and can view or edit
                (depending on the permission you grant them). Use this for sensitive schemas where you want explicit
                control over who sees the diagram.</p>
        </div>

        <h2>How to Generate a Shareable Link</h2>
        <ol>
            <li>Log in to SQL Designer and open the diagram you want to share.</li>
            <li>Click the <strong>Share</strong> button in the toolbar.</li>
            <li>Choose an access mode: read-only, editable, or approval-based.</li>
            <li>Copy the generated link and send it to your recipients.</li>
        </ol>
        <p>
            The link points to the live diagram. Every time a recipient opens it, they see the latest version —
            no re-sharing required when you make updates.
        </p>

        <h2>Embedding a Database Diagram as an iframe</h2>
        <p>
            If you want to embed a database diagram directly in a webpage, wiki, or documentation site, SQL Designer
            generates an iframe embed code alongside the shareable link. The embedded diagram is fully interactive —
            visitors can pan, zoom, and inspect tables — and it stays in sync with the live diagram automatically.
        </p>
        <p>
            A typical embed looks like this:
        </p>
        <pre><code>&lt;iframe
  src="https://sql-designer.com/share/your-token"
  width="100%"
  height="500"
  frameborder="0"
  allowfullscreen&gt;
&lt;/iframe&gt;</code></pre>
        <p>
            Paste it into any HTML page, Notion embed block, Confluence "HTML macro", or GitHub README (where
            iframes are supported). The diagram renders inline, scrollable and zoomable, without requiring the
            viewer to navigate away from your documentation.
        </p>

        <h2>Use Cases for Shared and Embedded Diagrams</h2>
        <ul>
            <li><strong>Code review.</strong> Link directly to the schema diagram in a pull request description so
                reviewers can see the data model alongside the code change.</li>
            <li><strong>Client or stakeholder review.</strong> Share a read-only link with non-technical
                stakeholders before development begins. They see the full schema in the browser without needing
                to install anything.</li>
            <li><strong>Team documentation.</strong> Embed the live diagram in your project wiki or internal
                docs. As the schema evolves, the embedded diagram updates automatically — no manual maintenance
                required.</li>
            <li><strong>Onboarding.</strong> New engineers joining a project can open the schema diagram in the
                browser and explore the data model interactively, without needing database access or a local
                tool installed.</li>
            <li><strong>Open-source projects.</strong> Embed the database diagram in a project README or
                documentation site so contributors can understand the data model immediately.</li>
        </ul>

        <h2>Managing Access After Sharing</h2>
        <p>
            Sharing isn't permanent or one-way. In SQL Designer you can:
        </p>
        <ul>
            <li><strong>Revoke access at any time</strong> — disabling the shared link immediately prevents
                anyone from viewing the diagram via that URL.</li>
            <li><strong>Change the access mode</strong> — switch between read-only, editable, and
                approval-based without generating a new link.</li>
            <li><strong>Manage individual visitors</strong> — in approval-based mode, see a list of who has
                been granted access and remove specific people.</li>
        </ul>

        <h2>How SQL Designer Compares on Sharing</h2>
        <p>
            Most ERD tools make sharing expensive or awkward:
        </p>
        <ul>
            <li><strong>dbdiagram.io:</strong> diagrams are public by default on the free tier; private diagrams
                and real sharing controls require a paid plan.</li>
            <li><strong>draw.io:</strong> no live shareable links — sharing means exporting a file and sending it
                separately.</li>
            <li><strong>Lucidchart:</strong> shareable links are available but require a paid plan for meaningful
                access control.</li>
            <li><strong>MySQL Workbench:</strong> a desktop application with no built-in sharing — you export an
                image or an SQL file.</li>
        </ul>
        <p>
            SQL Designer includes shareable links, embeddable iframes, and per-visitor access control on the free
            plan — no upgrade required.
        </p>

        <h2>Summary</h2>
        <ul>
            <li>Sharing a diagram as an image goes stale; a shareable link always shows the current version.</li>
            <li>SQL Designer supports three sharing modes: read-only, editable, and approval-based.</li>
            <li>Diagrams can be embedded as interactive iframes in wikis, docs, and websites.</li>
            <li>Access can be revoked or changed at any time without generating a new link.</li>
            <li>All sharing features are free — no paid plan required.</li>
        </ul>

        <nav aria-label="Related articles" style="margin-top:3rem; padding-top:2rem; border-top:1px solid var(--border-color);">
            <p style="font-size:0.875rem; text-transform:uppercase; letter-spacing:0.06em; color:#767676; margin:0 0 0.8rem;">
                Related Articles</p>
            <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:0.5rem;">
                <li><a href="/blog/best-erd-tools"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Best Free ERD Tools Online — Compared &rarr;</a></li>
                <li><a href="/blog/database-designer"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">Free Online Database Designer — Visual Schema Builder &rarr;</a></li>
                <li><a href="/blog/dbdiagram-alternative"
                       style="color:var(--color-primary); font-size:0.88rem; text-decoration:none;">dbdiagram.io Alternative — Free Visual Schema Designer &rarr;</a></li>
            </ul>
        </nav>

        <div class="cta-box">
            <h3>Share your database schema online — free</h3>
            <p>Design your schema visually, then share it with a link or embed it in your docs. Read-only,
                editable, or approval-based access — all included at no cost.</p>
            <a class="btn-cta" href="/register">Create a Free Account</a>
        </div>
    </article>
@endsection
