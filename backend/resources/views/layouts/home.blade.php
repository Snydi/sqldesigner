<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="ebdef4d5d4512d71" />
    <title>@yield('title', 'SQL Designer — Free Online MySQL Database Schema Designer')</title>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="manifest" href="/manifest.json">
    @vite(['src/css/app.css'])
    @yield('head')
</head>
<body class="home-page">

<header class="header" role="banner">
    <a href="/"><img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo" width="148" height="24"></a>
    <nav class="flex-items" aria-label="Main navigation">
        <a class="btn btn-secondary" href="/blog">Blog</a>
        <div id="nav-authed" style="display:none; gap:1rem;">
            <a class="btn btn-secondary" href="/diagrams">My Diagrams</a>
            <a class="btn btn-secondary" href="/logout">Logout</a>
        </div>
        <div id="nav-guest" style="display:flex; gap:1rem;">
            <a class="btn btn-secondary" href="/register">Register</a>
            <a class="btn btn-secondary" href="/login">Login</a>
        </div>
    </nav>
    <script>
        if (localStorage.getItem('auth_token')) {
            document.getElementById('nav-authed').style.display = 'flex';
            document.getElementById('nav-guest').style.display = 'none';
        }
    </script>
</header>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-4L116MPX4C"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-4L116MPX4C');
</script>

<main>
    @yield('content')
</main>

<footer class="home-footer" role="contentinfo">
    &copy; {{ date('Y') }} SQL Designer. Free MySQL database schema designer.
    &mdash;
    <a href="https://gitlab.com/Snydi/sql-designer" target="_blank" rel="noopener noreferrer" aria-label="View source on GitLab" style="display:inline-flex;align-items:center;gap:0.3rem;color:#999;text-decoration:none;vertical-align:middle;" onmouseover="this.style.color='#FC6D26'" onmouseout="this.style.color='#999'">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M4.845.904A.98.98 0 004 1.475L.046 13.645a.995.995 0 00.361 1.115l11.6 8.43a.984.984 0 001.186 0l11.6-8.43a.995.995 0 00.361-1.115L21.2 1.476a.98.98 0 00-1.785-.127L16.56 9.42H7.442L4.63 1.35A.98.98 0 004.845.904z"/></svg>
        GitLab
    </a>
</footer>

</body>
</html>
