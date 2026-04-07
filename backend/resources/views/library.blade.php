@extends('layouts.main')

@section('title', 'Schema Library — SQL Designer')

@section('head')
    <meta name="description" content="Browse real database schemas shared by the SQL Designer community. Find inspiration for your next project.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://sql-designer.com/library">
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Schema Library — SQL Designer",
        "url": "https://sql-designer.com/library",
        "description": "Browse real database schemas shared by the SQL Designer community.",
        "isPartOf": { "@type": "WebSite", "url": "https://sql-designer.com" }
    }
    @endverbatim
    </script>
    <style>
        body { overflow-y: auto; }

        .library-page {
            max-width: 860px;
            margin: 0 auto;
            padding: 4rem 1.5rem 6rem;
        }

        .library-header {
            margin-bottom: 3.5rem;
        }

        .library-header h1 {
            font-size: 1.6rem;
            margin: 0 0 0.6rem;
        }

        .library-header p {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin: 0;
            max-width: 540px;
        }

        /* ── Section heading ── */
        .lib-section {
            margin-bottom: 3.5rem;
        }

        .lib-section-heading {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            margin: 0 0 0.5rem;
        }

        .lib-section-heading h2 {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0;
            color: var(--text-primary);
        }

        .lib-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            background: var(--bg-surface);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .lib-badge.featured {
            background: #fffbeb;
            color: #92400e;
            border-color: #fcd34d;
        }

        .lib-section-desc {
            font-size: 0.875rem;
            color: var(--text-subtle);
            margin: 0 0 1.5rem;
            max-width: 600px;
            line-height: 1.6;
        }

        .lib-section-desc a {
            color: var(--color-primary);
            text-decoration: none;
        }

        .lib-section-desc a:hover {
            text-decoration: underline;
        }

        /* ── Empty state ── */
        .lib-empty {
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            padding: 2.5rem 2rem;
            text-align: center;
            color: var(--text-muted);
        }

        .lib-empty-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            opacity: 0.5;
        }

        .lib-empty p {
            margin: 0 0 0.35rem;
            font-size: 0.9rem;
        }

        .lib-empty .lib-empty-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            opacity: 0.7;
        }

        /* ── Diagram grid ── */
        .lib-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .lib-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-elevated);
            color: inherit;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .lib-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        }

        /* Scaled iframe preview */
        .lib-card-preview {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: var(--bg-page);
            border-bottom: 1px solid var(--border-color);
        }

        .lib-card-preview iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 860px;
            height: 484px;
            border: none;
            transform: scale(calc(1 / 3));
            transform-origin: top left;
            pointer-events: none;
        }

        .lib-card-body {
            padding: 0.9rem 1rem 0.8rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .lib-card-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lib-card-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ── Divider ── */
        .lib-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 0 0 3.5rem;
        }

        @media (max-width: 540px) {
            .library-page { padding: 2.5rem 1rem 4rem; }
            .library-header h1 { font-size: 1.3rem; }
            .lib-preview-modal { padding: 0; }
            .lib-preview-modal__inner { border-radius: 0; max-width: 100%; height: 100%; }
        }
    </style>
@endsection

@section('content')
<div class="library-page">

    <div class="library-header">
        <h1>Schema Library</h1>
        <p>Real database schemas shared by the community. Browse for inspiration or share your own.</p>
    </div>

    {{-- ── Featured ── --}}
    <div class="lib-section">
        <div class="lib-section-heading">
            <h2>Featured</h2>
            <span class="lib-badge featured">Coming soon</span>
        </div>
        <p class="lib-section-desc">
            Featured schemas come from users who embedded their diagram on their own website with a backlink to SQL Designer.
            In return, they get a spot here at the top. <a href="/features">Embed feature</a> coming soon.
        </p>
        <div class="lib-empty">
            <div class="lib-empty-icon">&#9733;</div>
            <p>No featured schemas yet.</p>
            <p class="lib-empty-hint">Embed your diagram on your site to get featured here.</p>
        </div>
    </div>

    <hr class="lib-divider">

    {{-- ── Shared by users ── --}}
    <div class="lib-section">
        <div class="lib-section-heading">
            <h2>Shared by users</h2>
        </div>
        <p class="lib-section-desc">
            Schemas shared publicly by users. When sharing a diagram, check the
            <em>"Share in library"</em> box to have it appear here.
        </p>
        @if($diagrams->isEmpty())
            <div class="lib-empty">
                <div class="lib-empty-icon">&#9733;</div>
                <p>No public schemas yet.</p>
                <p class="lib-empty-hint">Share a diagram and opt in to the library to be first.</p>
            </div>
        @else
            <div class="lib-grid">
                @foreach($diagrams as $diagram)
                    <a class="lib-card" href="/diagrams/{{ $diagram->share_token }}" target="_blank" rel="noopener noreferrer">
                        <div class="lib-card-preview">
                            <iframe
                                data-src="/embed/{{ $diagram->share_token }}"
                                title="{{ $diagram->name }} preview"
                                tabindex="-1"
                            ></iframe>
                        </div>
                        <div class="lib-card-body">
                            <span class="lib-card-name">{{ $diagram->name }}</span>
                            <span class="lib-card-meta">Updated {{ $diagram->updated_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const iframe = entry.target;
                if (iframe.dataset.src) iframe.src = iframe.dataset.src;
                observer.unobserve(iframe);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('.lib-card-preview iframe[data-src]').forEach(el => observer.observe(el));
</script>
@endsection
