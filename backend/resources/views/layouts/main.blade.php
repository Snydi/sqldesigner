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
    <link rel="license" href="https://github.com/Snydi/sqldesigner/blob/master/LICENSE">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/geist-latin.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="/fonts/jetbrains-mono-latin.woff2">
    <style>
        /* Geist — self-hosted */
        @font-face { font-family: 'Geist'; font-style: normal; font-weight: 400 700; font-display: optional; src: url('/fonts/geist-cyrillic-ext.woff2') format('woff2'); unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F; }
        @font-face { font-family: 'Geist'; font-style: normal; font-weight: 400 700; font-display: optional; src: url('/fonts/geist-cyrillic.woff2') format('woff2'); unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116; }
        @font-face { font-family: 'Geist'; font-style: normal; font-weight: 400 700; font-display: optional; src: url('/fonts/geist-vietnamese.woff2') format('woff2'); unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB; }
        @font-face { font-family: 'Geist'; font-style: normal; font-weight: 400 700; font-display: optional; src: url('/fonts/geist-latin-ext.woff2') format('woff2'); unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF; }
        @font-face { font-family: 'Geist'; font-style: normal; font-weight: 400 700; font-display: optional; src: url('/fonts/geist-latin.woff2') format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
        /* Metric-adjusted Arial fallback — reduces CLS while Geist loads */
        @font-face { font-family: 'Geist Fallback'; src: local('Arial'); ascent-override: 93%; descent-override: 23%; line-gap-override: 0%; size-adjust: 104%; }
        /* JetBrains Mono — self-hosted */
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-cyrillic-ext.woff2') format('woff2'); unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F; }
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-cyrillic.woff2') format('woff2'); unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116; }
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-greek.woff2') format('woff2'); unicode-range: U+0370-0377, U+037A-037F, U+0384-038A, U+038C, U+038E-03A1, U+03A3-03FF; }
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-vietnamese.woff2') format('woff2'); unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB; }
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-latin-ext.woff2') format('woff2'); unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF; }
        @font-face { font-family: 'JetBrains Mono'; font-style: normal; font-weight: 400 600; font-display: optional; src: url('/fonts/jetbrains-mono-latin.woff2') format('woff2'); unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
    </style>
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
            font-family: 'Geist', 'Geist Fallback', system-ui, sans-serif;
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
            background: rgba(31,31,31,0.96);
            border-bottom: 1px solid var(--border-light);
            color: var(--text-primary);
        }
        .header-left { display: flex; align-items: center; gap: 0.5rem; }
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

        /* ── GitHub star button ──────────────────────────── */
        .gh-star {
            padding: 0.5rem 0.7rem 0.5rem 0.65rem;
        }
        .gh-star .gh-star-count {
            display: inline-flex; align-items: center;
            margin-left: 0.15rem; padding: 0.05rem 0.45rem;
            border: 1px solid var(--border-light); border-radius: 20px;
            font-size: 0.78rem; font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-secondary);
            transition: border-color 120ms ease, color 120ms ease;
        }
        .gh-star:hover .gh-star-count { border-color: var(--border-strong); color: var(--text-primary); }
        .gh-star .gh-star-icon { color: #e3b341; }

        /* ── Mobile nav ──────────────────────────────────── */
        .hamburger {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 48px;
            height: 48px;
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 0.25rem;
        }
        .hamburger span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--text-secondary);
            border-radius: 2px;
            transition: transform 200ms ease, opacity 200ms ease;
        }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .mobile-nav {
            display: none;
            position: fixed;
            top: 56px;
            left: 0; right: 0;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem 1rem;
            box-shadow: 0 12px 24px -8px rgba(0,0,0,0.5);
            z-index: 49;
            flex-direction: column;
            gap: 0.2rem;
        }
        .mobile-nav.open { display: flex; }
        .mobile-nav a {
            color: var(--text-secondary);
            font-size: 0.9rem;
            padding: 0 0.5rem;
            min-height: 48px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .mobile-nav a:hover { color: var(--text-primary); background: var(--bg-surface); }
        .mobile-nav .divider { height: 1px; background: var(--border-light); margin: 0.4rem 0; }

        @media (max-width: 720px) {
            .header { padding: 0 1rem; height: 56px; }
            .nav-hide-mobile { display: none !important; }
            .hamburger { display: flex; }
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
        .footer-col h2 {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.14em;
            color: var(--text-subtle); margin: 0 0 0.6rem; font-weight: 500;
            font-family: 'JetBrains Mono', monospace;
        }
        .footer-col ul { list-style: none; padding: 0; margin: 0; }
        .footer-col li { padding: 0.18rem 0; }
        .footer-col a { color: var(--text-subtle); font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0; }
        .footer-col a:hover { color: var(--color-primary-text); }
        .footer-github:hover { color: #f0f6fc !important; }
        .footer-discord:hover { color: #9198f4 !important; }
        .footer-email { color: var(--text-secondary) !important; }
        .footer-email:hover { color: var(--color-primary-text) !important; text-decoration: underline; }
        .footer-bottom {
            max-width: var(--maxw); margin: 1rem auto 0;
            display: flex; justify-content: space-between; flex-wrap: wrap;
            gap: 0.5rem; font-size: 0.78rem; color: var(--text-subtle);
        }
        .footer-bottom a:hover { color: var(--color-primary-text); }
        @media (max-width: 540px) {
            .footer-inner { gap: 1.5rem; }
            .footer-bottom { flex-direction: column; }
        }
    </style>
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
        setTimeout(loadGtag, 8000);
    </script>
</head>
<body class="home-page">

<header class="header">
    <div class="header-left">
        <a href="/"><img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo" width="148" height="24"></a>
        <nav class="header-left__nav nav-hide-mobile" aria-label="Site navigation">
            <a class="btn btn-ghost gh-star" href="https://github.com/Snydi/sqldesigner/stargazers" target="_blank" rel="noopener noreferrer" aria-label="Star SQL Designer on GitHub">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                <span class="gh-star-count">
                    <svg class="gh-star-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="15" height="15" fill="currentColor" aria-hidden="true" style="margin-right:0.2rem;"><path d="M8 .25a.75.75 0 01.673.418l1.882 3.815 4.21.612a.75.75 0 01.416 1.279l-3.046 2.97.719 4.192a.75.75 0 01-1.088.791L8 12.347l-3.766 1.98a.75.75 0 01-1.088-.79l.72-4.194L.818 6.374a.75.75 0 01.416-1.28l4.21-.611L7.327.668A.75.75 0 018 .25z"/></svg><span id="gh-star-count-val">—</span>
                </span>
            </a>
            <a class="btn btn-ghost" href="/features">Features</a>
            <a class="btn btn-ghost" href="/pricing">Pricing</a>
            <a class="btn btn-ghost" href="/library">Library</a>
            <a class="btn btn-ghost" href="/blog">Blog</a>
        </nav>
        <button class="hamburger" id="hamburger-btn" aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobile-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
    <nav class="flex-items" aria-label="Main navigation">
        <div id="nav-authed" style="display:none; gap:0.6rem;">
            <a class="btn btn-ghost nav-hide-mobile" href="/billing">Pro</a>
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

<nav class="mobile-nav" id="mobile-nav" aria-label="Mobile navigation">
    <a href="/features">Features</a>
    <a href="/pricing">Pricing</a>
    <a href="/library">Library</a>
    <a href="/blog">Blog</a>
    <a href="/about">About</a>
    <div class="divider"></div>
    <div id="mobile-nav-authed" style="display:none;">
        <a href="/billing">Pro</a>
        <a href="/diagrams">My Diagrams</a>
    </div>
    <a href="/login" id="mobile-nav-login">Log in</a>
    <a href="/register" style="color:var(--color-primary-text); font-weight:500;">Sign up free</a>
</nav>
<script>
    (function () {
        var btn = document.getElementById('hamburger-btn');
        var nav = document.getElementById('mobile-nav');
        if (!btn || !nav) return;
        btn.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            btn.classList.toggle('open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        if (localStorage.getItem('auth_token')) {
            var authed = document.getElementById('mobile-nav-authed');
            var loginLink = document.getElementById('mobile-nav-login');
            if (authed) authed.style.display = 'block';
            if (loginLink) loginLink.style.display = 'none';
        }
        fetch('/api/stats').then(function (r) { return r.json(); }).then(function (d) {
            if (!d.stars) return;
            var el = document.getElementById('gh-star-count-val');
            if (el) el.textContent = d.stars.toLocaleString();
        }).catch(function () {});
    }());
</script>

<main>
    @yield('content')
</main>

<footer class="site">
    <div class="footer-inner">
        <div class="footer-col">
            <h2>Product</h2>
            <ul>
                <li><a href="/features">Features</a></li>
                <li><a href="/pricing">Pricing</a></li>
                <li><a href="/demo">Live demo</a></li>
                <li><a href="/library">Schema library</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h2>Resources</h2>
            <ul>
                <li><a href="/blog">Blog</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/sitemap">Sitemap</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h2>Code</h2>
            <ul>
                <li>
                    <a href="https://github.com/Snydi/sqldesigner" target="_blank" rel="noopener noreferrer" class="footer-github" aria-label="View source on GitHub">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                        GitHub
                    </a>
                </li>
            </ul>
        </div>
        <div class="footer-col">
            <h2>Community</h2>
            <ul>
                <li>
                    <a href="mailto:dmitriy@sql-designer.com" class="footer-email" aria-label="Send an email">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        Email
                    </a>
                </li>
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
        <span>&copy; {{ date('Y') }} SQL Designer &mdash; visual SQL schema designer for 6 dialects &middot; Snyatkov Dmitriy Andreevich; INN 344818239248</span>
        <span>
            <a href="{{ route('oferta') }}" style="color:inherit;">Public Offer</a>
            &nbsp;&middot;&nbsp;
            <a href="/privacy" style="color:inherit;">Privacy Policy</a>
            &nbsp;&middot;&nbsp;
            <a href="/terms" style="color:inherit;">Terms of Service</a>
            &nbsp;&middot;&nbsp;
            <a href="/refund-policy" style="color:inherit;">Refund Policy</a>
            &nbsp;&middot;&nbsp;
            <a href="mailto:dmitriy@sql-designer.com" style="color:inherit;">dmitriy@sql-designer.com</a>
        </span>
    </div>
</footer>

</body>
</html>
