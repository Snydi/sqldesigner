@extends('layouts.main')

@section('title', 'SQL Designer Pricing — Free and Pro Plans Compared')

@section('head')
    <meta name="description" content="SQL Designer is free with a 1-diagram limit and 3 exports daily. Pro costs $10 USD/month for unlimited diagrams and exports.">
    <meta name="author" content="SQL Designer">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/pricing">
    <meta property="og:title" content="SQL Designer Pricing — Free and Pro Plans Compared">
    <meta property="og:description" content="SQL Designer is free with a 1-diagram limit and 3 exports daily. Pro costs $10 USD/month for unlimited diagrams and exports.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="SQL Designer">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="https://sql-designer.com/pricing">
    <meta property="og:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta property="og:image:width" content="2240">
    <meta property="og:image:height" content="1111">
    <meta property="og:image:alt" content="SQL Designer pricing — free and Pro plans for the visual database designer">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="SQL Designer Pricing — Free and Pro Plans Compared">
    <meta name="twitter:description" content="SQL Designer is free with a 1-diagram limit and 3 exports daily. Pro costs $10 USD/month for unlimited diagrams and exports.">
    <meta name="twitter:image" content="https://sql-designer.com/images/designer_screenshot.webp">
    <meta name="keywords" content="sql designer pricing, database diagram tool pricing, erd tool subscription, free database designer, pro database diagram plan, sql designer pro">
    <script type="application/ld+json">
    @verbatim
    [
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home",    "item": "https://sql-designer.com/" },
            { "@type": "ListItem", "position": 2, "name": "Pricing", "item": "https://sql-designer.com/pricing" }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "@id": "https://sql-designer.com/#app",
        "name": "SQL Designer",
        "url": "https://sql-designer.com",
        "sameAs": ["https://github.com/Snydi/sqldesigner", "https://gitlab.com/Snydi/sql-designer", "https://discord.gg/vFwgX7qKqA"],
        "brand": { "@id": "https://sql-designer.com/#organization" },
        "description": "Visual SQL schema builder and online database modeler with a free plan and a Pro subscription.",
        "offers": [
            {
                "@type": "Offer",
                "name": "Free",
                "price": "0",
                "priceCurrency": "USD",
                "description": "1 diagram, 3 exports per day."
            },
            {
                "@type": "Offer",
                "name": "Pro",
                "price": "10.00",
                "priceCurrency": "USD",
                "priceSpecification": {
                    "@type": "UnitPriceSpecification",
                    "price": "10.00",
                    "priceCurrency": "USD",
                    "billingDuration": "P1M"
                },
                "description": "Unlimited diagrams and unlimited exports."
            }
        ]
    },
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Pricing — SQL Designer",
        "url": "https://sql-designer.com/pricing",
        "datePublished": "2026-07-01",
        "dateModified": "2026-07-21",
        "description": "SQL Designer pricing: a free plan with a 1-diagram limit and 3 exports per day, and a $10 USD/month Pro plan with no limits.",
        "isPartOf": { "@id": "https://sql-designer.com/#website" },
        "about": { "@id": "https://sql-designer.com/#app" }
    }
    ]
    @endverbatim
    </script>
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "Is SQL Designer free to use?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. The free plan lets you create 1 diagram and export it up to 3 times a day — no credit card required."
                }
            },
            {
                "@type": "Question",
                "name": "What happens to my diagrams if I'm over the free limit?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Nothing is deleted. If you already have more than 1 diagram, you keep all of them. You just won't be able to create a new diagram until you're back at or under the 1-diagram limit, or until you upgrade to Pro."
                }
            },
            {
                "@type": "Question",
                "name": "What does the Pro plan cost?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "SQL Designer Pro costs $10 USD per month and removes the diagram and export limits entirely."
                }
            },
            {
                "@type": "Question",
                "name": "How many exports do I get on the free plan?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "3 exports per day on the free plan, combined across SQL, JSON, migration, and PNG exports. Pro has no daily export limit."
                }
            },
            {
                "@type": "Question",
                "name": "Can I cancel my Pro subscription anytime?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Pro renews automatically each month until you cancel. You can cancel at any time and request a refund under our Refund Policy."
                }
            }
        ]
    }
    @endverbatim
    </script>
    <style>
        body { overflow-y: auto; margin: 0; }

        .page-intro {
            padding: clamp(2.5rem, 5vw, 4rem) var(--gutter, 2rem) clamp(1.5rem, 3vw, 2.5rem);
            border-bottom: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }
        .page-intro::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(var(--border-light) 1px, transparent 1px),
                linear-gradient(90deg, var(--border-light) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: linear-gradient(to bottom, black 0%, transparent 75%);
            opacity: 0.45;
            pointer-events: none;
        }
        .intro-inner { max-width: 1120px; margin: 0 auto; position: relative; text-align: center; }
        .breadcrumb {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.06em;
            margin: 0 0 1rem;
            text-align: left;
        }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--color-primary-text); }
        .breadcrumb .sep { margin: 0 0.4rem; color: var(--border-strong); }
        h1.page-h1 {
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            line-height: 1.1;
            letter-spacing: -0.025em;
            font-weight: 600;
            margin: 0 auto 0.8rem;
            text-wrap: balance;
            max-width: 26ch;
        }
        h1.page-h1 em { font-style: normal; color: var(--color-primary-text); }
        .page-sub {
            font-size: 1.02rem;
            color: var(--text-secondary);
            margin: 0 auto;
            max-width: 56ch;
            text-wrap: pretty;
        }

        .plans-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: clamp(2.5rem, 5vw, 3.5rem) var(--gutter, 2rem) clamp(1rem, 3vw, 2rem);
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }
        @media (max-width: 720px) {
            .plans-grid { grid-template-columns: 1fr; }
        }
        .plan-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-surface);
            padding: clamp(1.6rem, 3vw, 2.2rem);
            display: flex;
            flex-direction: column;
        }
        .plan-card.pro {
            border-color: var(--color-primary-text);
            box-shadow: 0 0 0 1px var(--color-primary-text);
        }
        .plan-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--color-primary-text);
            margin: 0 0 0.6rem;
        }
        .plan-name {
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin: 0 0 0.3rem;
            text-transform: none;
        }
        .plan-price { margin: 0 0 1.4rem; display: flex; align-items: baseline; gap: 0.3rem; }
        .plan-price .amount { font-size: 2.4rem; font-weight: 600; letter-spacing: -0.02em; }
        .plan-price .period { font-size: 1rem; color: var(--text-muted); }
        .plan-features {
            list-style: none;
            margin: 0 0 1.6rem;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            flex: 1;
            min-height: 11rem;
        }
        .plan-features li {
            font-size: 1rem;
            color: var(--text-secondary);
            display: flex;
            gap: 0.6rem;
            align-items: baseline;
            text-transform: none;
        }
        .plan-features li .mark { color: var(--color-primary-text); flex-shrink: 0; }
        .plan-features li.limit .mark { color: var(--text-muted); }
        .plan-cta { text-align: center; margin-top: auto; }

        .launch-banner-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: clamp(1.2rem, 3vw, 1.8rem) var(--gutter, 2rem) 0;
        }
        .launch-banner {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border: 1px solid var(--color-primary-text);
            background: var(--bg-elevated);
            border-radius: 10px;
            padding: 1rem 1.3rem;
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-primary);
            text-align: left;
        }
        .launch-banner-icon { font-size: 1.2rem; line-height: 1; flex-shrink: 0; }
        .checkout-message { display:none; margin-top:.7rem; color:#f0b6b6; font-size:1rem; line-height:1.45; }
        .checkout-message.show { display:block; }

        .compare-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: clamp(1rem, 3vw, 2rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
        }
        h2.section-h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            font-weight: 600;
            margin: 0 0 1.4rem;
            padding-bottom: 0.7rem;
            border-bottom: 1px solid var(--border-color);
            text-transform: none;
        }
        table.compare {
            width: 100%;
            border-collapse: collapse;
            font-size: 1rem;
        }
        table.compare th, table.compare td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-light);
            text-align: center;
        }
        table.compare th:first-child, table.compare td:first-child {
            text-align: left;
            color: var(--text-primary);
        }
        table.compare thead th {
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 500;
        }
        table.compare td { color: var(--text-secondary); }
        table.compare tbody tr:last-child td { border-bottom: none; }

        .faq-wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
        }
        .faq-item { border-bottom: 1px solid var(--border-light); padding: 1.2rem 0; }
        .faq-item:last-child { border-bottom: none; }
        .faq-item h3 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.4rem;
            text-transform: none;
        }
        .faq-item p {
            font-size: 1rem;
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
            max-width: 68ch;
        }

        .docs-cta {
            margin: 0 auto;
            max-width: 1120px;
            padding: clamp(2rem, 4vw, 2.8rem) var(--gutter, 2rem) clamp(3rem, 6vw, 5rem);
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        .docs-cta h2 {
            font-size: clamp(1.3rem, 2.4vw, 1.7rem);
            letter-spacing: -0.02em;
            margin: 0 0 0.6rem;
            text-transform: none;
            color: var(--text-primary);
        }
        .docs-cta p { color: var(--text-secondary); margin: 0 auto 1.2rem; max-width: 50ch; }
        .docs-cta .actions { display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; }
    </style>
@endsection

@section('content')

{{-- Page intro --}}
<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><span>Pricing</span></p>
        <h1 class="page-h1">Simple pricing. Start <em>free</em>, upgrade when you outgrow it.</h1>
        <p class="page-sub">Free covers 1 diagram and 3 exports a day. Pro removes both limits for $10 USD/month.</p>
    </div>
</section>

{{-- Launch notice --}}
<div class="launch-banner-wrap">
    <div class="launch-banner" role="status">
        <span class="launch-banner-icon" aria-hidden="true">✓</span>
        <span>
            $10 USD/month, billed automatically. Cancel anytime; Pro stays active until the end of the current billing period.
        </span>
    </div>
</div>

{{-- Plans --}}
<div class="plans-wrap">
    <div class="plans-grid">

        <div class="plan-card">
            <h2 class="plan-name">Free</h2>
            <p class="plan-price"><span class="amount">$0 USD</span><span class="period">/ forever</span></p>
            <ul class="plan-features">
                <li class="limit"><span class="mark">–</span> 1 diagram</li>
                <li class="limit"><span class="mark">–</span> 3 daily exports: SQL, JSON, migration, or PNG</li>
                <li><span class="mark">✓</span> Drag-and-drop ERD canvas</li>
                <li><span class="mark">✓</span> SQL export for 6 dialects</li>
                <li><span class="mark">✓</span> Multiplayer collaboration</li>
            </ul>
            <div class="plan-cta">
                <a class="btn btn-outline btn-lg" href="/register">Sign up free</a>
            </div>
        </div>

        <div class="plan-card pro">
            <h2 class="plan-name">Pro</h2>
            <p class="plan-price"><span class="amount">$10 USD</span><span class="period">/ month</span></p>
            <ul class="plan-features">
                <li><span class="mark">✓</span> Unlimited diagrams</li>
                <li><span class="mark">✓</span> Unlimited exports</li>
                <li><span class="mark">✓</span> Drag-and-drop ERD canvas</li>
                <li><span class="mark">✓</span> SQL export for 6 dialects</li>
                <li><span class="mark">✓</span> Multiplayer collaboration</li>
            </ul>
            <div class="plan-cta">
                <a id="pro-checkout" class="btn btn-solid btn-lg" href="/login?redirect=/billing">Get Pro — $10 USD/month</a>
                <div id="checkout-message" class="checkout-message" role="alert"></div>
            </div>
        </div>

    </div>
</div>

{{-- Comparison table --}}
<div class="compare-wrap">
    <h2 class="section-h2">Compare plans</h2>
    <table class="compare">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Free</th>
                <th>Pro</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Diagrams</td>
                <td>1</td>
                <td>Unlimited</td>
            </tr>
            <tr>
                <td>Combined SQL, JSON, migration, and PNG exports per day</td>
                <td>3</td>
                <td>Unlimited</td>
            </tr>
            <tr>
                <td>SQL export (MySQL, PostgreSQL, SQLite, Oracle, SQL Server, MS Access)</td>
                <td>✓</td>
                <td>✓</td>
            </tr>
            <tr>
                <td>Multiplayer collaboration</td>
                <td>✓</td>
                <td>✓</td>
            </tr>
            <tr>
                <td>Price</td>
                <td>$0 USD</td>
                <td>$10 USD / month</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- FAQ --}}
<div class="faq-wrap">
    <h2 class="section-h2">Pricing FAQ</h2>

    <div class="faq-item">
        <h3>Is SQL Designer free to use?</h3>
        <p>Yes. The free plan lets you create 1 diagram and export it up to 3 times a day — no credit card required.</p>
    </div>
    <div class="faq-item">
        <h3>What happens to my diagrams if I'm over the free limit?</h3>
        <p>Nothing is deleted. If you already have more than 1 diagram, you keep all of them. You just won't be able to create a new diagram until you're back at or under the 1-diagram limit, or until you upgrade to Pro.</p>
    </div>
    <div class="faq-item">
        <h3>What does the Pro plan cost?</h3>
        <p>SQL Designer Pro costs $10 USD per month and removes the diagram and export limits entirely.</p>
    </div>
    <div class="faq-item">
        <h3>How many exports do I get on the free plan?</h3>
        <p>3 exports per day on the free plan, combined across SQL, JSON, migration, and PNG exports. Pro has no daily export limit.</p>
    </div>
    <div class="faq-item">
        <h3>Can I cancel my Pro subscription anytime?</h3>
        <p>Yes. Pro renews automatically each month until you cancel. You can cancel at any time and request a refund under our <a href="/refund-policy">Refund Policy</a>.</p>
    </div>
</div>

{{-- CTA --}}
<section class="docs-cta">
    <h2>Ready to draw a schema?</h2>
    <p>Open the demo and try it on a real schema — no account required.</p>
    <div class="actions">
        <a class="btn btn-solid btn-lg" href="/demo">Open the demo</a>
        <a class="btn btn-outline btn-lg" href="/register">Sign up to save</a>
    </div>
</section>

<script>
    (function () {
        const button = document.getElementById('pro-checkout');
        if (!button) return;

        button.addEventListener('click', async function (event) {
            if (button.getAttribute('aria-disabled') === 'true') {
                event.preventDefault();
                return;
            }
            const token = localStorage.getItem('auth_token');
            if (!token) {
                sessionStorage.setItem('post_auth_route', 'billing');
                return;
            }

            event.preventDefault();
            window.location.href = '/billing';
        });
    }());
</script>
@endsection
