<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="ebdef4d5d4512d71" />
    <title>@yield('title', 'SQL Designer — Free Online MySQL Database Schema Designer')</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" sizes="any">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
    @vite(['src/css/app.css'])
    <style>
        /* ── Token overrides for Blade pages ──────────────── */
        :root {
            --color-primary:       #2e5c45;
            --color-primary-hover: #224436;
            --color-primary-text:  #5db583;
            --bg-page:             #1f1f1f;
            --bg-surface:          #262626;
            --bg-surface-alt:      #2c2c2c;
            --bg-elevated:         #2a2a2a;
            --text-primary:        #e6e6e6;
            --text-secondary:      #b8b8b8;
            --text-subtle:         #8a8a8a;
            --text-muted:          #6a6a6a;
            --border-color:        #3a3a3a;
            --border-light:        #2f2f2f;
            --border-strong:       #565656;
            --maxw:                1120px;
            --gutter:              clamp(1.25rem, 4vw, 2.5rem);
        }

        *,*::before,*::after { box-sizing: border-box; }

        html, body { margin: 0; background: var(--bg-page); color: var(--text-primary); overflow-y: auto; }
        body {
            font-family: 'Geist', system-ui, -apple-system, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: var(--color-primary-text); color: #0c0c0c; }
        a { color: inherit; text-decoration: none; }
        .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }

        /* ── Header ───────────────────────────────────────── */
        .header {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.85rem var(--gutter);
            background: rgba(31,31,31,0.88);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-light);
            color: var(--text-primary);
        }
        .header-left { display: flex; align-items: center; gap: 1.75rem; }
        .header-left__nav { display: flex; align-items: center; gap: 0.15rem; }

        /* ── Buttons ──────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.55rem 0.95rem; border-radius: 6px;
            font-size: 0.875rem; font-weight: 500; line-height: 1;
            border: 1px solid transparent; cursor: pointer;
            transition: background 120ms ease, border-color 120ms ease, color 120ms ease;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-ghost { color: var(--text-secondary); background: none; border-color: transparent; }
        .btn-ghost:hover { color: var(--text-primary); background: var(--bg-surface); }
        .btn-outline { color: var(--text-primary); border-color: var(--border-strong); background: none; }
        .btn-outline:hover { border-color: var(--text-primary); }
        .btn-solid { background: var(--color-primary-text); color: #0c1f15; }
        .btn-solid:hover { background: #6dc290; }
        .btn-lg { padding: 0.75rem 1.15rem; font-size: 0.95rem; border-radius: 7px; }

        @media (max-width: 720px) {
            .header { padding: 0.65rem 1rem; }
            .nav-hide-mobile { display: none !important; }
        }

        /* ── Footer ───────────────────────────────────────── */
        footer.site {
            padding: 2.5rem var(--gutter) 1.5rem;
            background: var(--bg-page);
            color: var(--text-subtle);
            font-size: 0.85rem;
        }
        .footer-inner {
            max-width: var(--maxw); margin: 0 auto;
            display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between;
            padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);
        }
        .footer-col { min-width: 130px; }
        .footer-col h4 {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.14em;
            color: var(--text-muted); margin: 0 0 0.6rem; font-weight: 500;
            font-family: 'JetBrains Mono', monospace;
        }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col li { padding: 0.18rem 0; }
        .footer-col a { color: var(--text-subtle); font-size: 0.82rem; display: block; padding: 0.2rem 0; }
        .footer-col a:hover { color: var(--color-primary-text); }
        .footer-gitlab { display: inline-flex; align-items: center; gap: 0.35rem; }
        .footer-gitlab:hover { color: #FC6D26 !important; }
        .footer-discord { display: inline-flex; align-items: center; gap: 0.35rem; }
        .footer-discord:hover { color: #9198f4 !important; }
        .footer-bottom {
            max-width: var(--maxw); margin: 1rem auto 0;
            display: flex; justify-content: space-between; flex-wrap: wrap;
            gap: 0.5rem; font-size: 0.78rem; color: var(--text-muted);
        }
        @media (max-width: 540px) {
            .footer-inner { gap: 1.5rem; }
            .footer-bottom { flex-direction: column; }
        }
    </style>
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />
    @yield('head')
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
        setTimeout(loadGtag, 4000);
    </script>
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="6r4lGJ3Fgx0N5p0a4dI/OQ" async></script>
</head>
<body class="home-page">

<header class="header">
    <div class="header-left">
        <a href="/"><img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo" width="148" height="24"></a>
        <nav class="header-left__nav nav-hide-mobile" aria-label="Site navigation">
            <a class="btn btn-ghost" href="/features">Features</a>
            <a class="btn btn-ghost" href="/library">Library</a>
            <a class="btn btn-ghost" href="/blog">Blog</a>
        </nav>
    </div>
    <nav class="flex-items" aria-label="Main navigation">
        <div id="nav-authed" style="display:none; gap:0.6rem;">
            <a class="btn btn-ghost nav-hide-mobile" href="/diagrams">My Diagrams</a>
            <a class="btn btn-ghost" href="/logout">Logout</a>
        </div>
        <div id="nav-guest" style="display:flex; gap:0.6rem;">
            <a class="btn btn-ghost nav-hide-mobile" href="/login">Log in</a>
            <a class="btn btn-solid" href="/register">Sign up</a>
        </div>
    </nav>
    <script>
        if (localStorage.getItem('auth_token')) {
            document.getElementById('nav-authed').style.display = 'flex';
            document.getElementById('nav-guest').style.display = 'none';
        }
    </script>
</header>

<main>
    @yield('content')
</main>

<footer class="site">
    <div class="footer-inner">
        <div class="footer-col">
            <h4>Product</h4>
            <ul>
                <li><a href="/features">Features</a></li>
                <li><a href="/demo">Live demo</a></li>
                <li><a href="/library">Schema library</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Resources</h4>
            <ul>
                <li><a href="/blog">Blog</a></li>
                <li><a href="/sitemap">Sitemap</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Code</h4>
            <ul>
                <li>
                    <a href="https://gitlab.com/Snydi/sql-designer" target="_blank" rel="noopener noreferrer" class="footer-gitlab" aria-label="View source on GitLab">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M4.845.904A.98.98 0 004 1.475L.046 13.645a.995.995 0 00.361 1.115l11.6 8.43a.984.984 0 001.186 0l11.6-8.43a.995.995 0 00.361-1.115L21.2 1.476a.98.98 0 00-1.785-.127L16.56 9.42H7.442L4.63 1.35A.98.98 0 004.845.904z"/></svg>
                        GitLab
                    </a>
                </li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Community</h4>
            <ul>
                <li>
                    <a href="https://discord.gg/vFwgX7qKqA" target="_blank" rel="noopener noreferrer" class="footer-discord" aria-label="Join our Discord server">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                        Discord
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} SQL Designer &mdash; visual MySQL &amp; PostgreSQL schema designer</span>
    </div>
</footer>

</body>
</html>
