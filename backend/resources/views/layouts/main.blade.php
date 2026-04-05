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
    @vite(['src/css/app.css'])
    <style>
        body { background-color: #fff; }
        .home-footer { background-color: #f0f0f0; color: #505050; font-size: 0.875rem; text-transform: none; padding: 2.5rem 1.5rem 1.5rem; }
        .home-footer a { color: var(--color-primary); text-decoration: none; }
        .home-footer a:hover { text-decoration: underline; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 2.5rem; justify-content: space-between; padding-bottom: 2rem; border-bottom: 1px solid #ddd; }
        .footer-col { min-width: 140px; }
        .footer-col-heading { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.12em; color: #999; margin: 0 0 0.75rem; }
        .footer-col-heading a { color: #999; font-weight: 600; }
        .footer-col-heading a:hover { color: var(--color-primary); text-decoration: none; }
        .footer-col ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.4rem; }
        .footer-col ul li a { font-size: 0.82rem; color: #555; }
        .footer-col ul li a:hover { color: var(--color-primary); }
        .footer-bottom { max-width: 1100px; margin: 1.2rem auto 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #888; }
        .footer-gitlab { display: inline-flex; align-items: center; gap: 0.3rem; color: #888; }
        .footer-gitlab:hover { color: #FC6D26 !important; text-decoration: none !important; }
        .footer-discord { display: inline-flex; align-items: center; gap: 0.3rem; color: #888; }
        .footer-discord:hover { color: #5865F2 !important; text-decoration: none !important; }
        @media (max-width: 540px) { .footer-inner { gap: 1.5rem; } .footer-bottom { flex-direction: column; align-items: flex-start; } }
        @media (max-width: 540px) {
            .header { padding: 0.5rem; }
            .flex-items { gap: 0.4rem; }
            .btn { padding: 0.35rem 0.6rem; font-size: 0.85rem; }
            .nav-hide-mobile { display: none !important; }
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
        window.addEventListener('load', function() {
            var s = document.createElement('script');
            s.async = true;
            s.src = 'https://www.googletagmanager.com/gtag/js?id=G-4L116MPX4C';
            document.head.appendChild(s);
        });
    </script>
</head>
<body class="home-page">
<header class="header">
    <a href="/"><img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo" width="148" height="24"></a>
    <nav class="flex-items" aria-label="Main navigation">
        <a class="btn btn-secondary nav-hide-mobile" href="/features">Features</a>
        <div id="nav-authed" style="display:none; gap:1rem;">
            <a class="btn btn-secondary" href="/diagrams">My Diagrams</a>
            <a class="btn btn-secondary" href="/logout">Logout</a>
        </div>
        <div id="nav-guest" style="display:flex; gap:1rem;">
            <a class="btn btn-primary" href="/register">Create account</a>
            <a class="btn btn-secondary nav-hide-mobile" href="/login">Login</a>
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

<footer class="home-footer">
    <div class="footer-inner">
        <div class="footer-col">
            <p class="footer-col-heading"><a href="/features">All Features</a></p>
            <ul>
                <li><a href="/features#canvas">Visual Canvas</a></li>
                <li><a href="/features#sql-export">SQL Export</a></li>
                <li><a href="/features#relationships">Foreign Keys</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <p class="footer-col-heading">Resources</p>
            <ul>
                <li><a href="/blog">Blog</a></li>
                <li><a href="/sitemap">Site Map</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <p class="footer-col-heading">Code</p>
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
            <p class="footer-col-heading">Community</p>
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
        <span>&copy; {{ date('Y') }} SQL Designer &mdash; Free MySQL &amp; PostgreSQL schema designer</span>
    </div>
</footer>

</body>
</html>
