<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="ebdef4d5d4512d71" />
    <title>@yield('title', 'SQL Designer — Free Online MySQL Database Schema Designer')</title>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.google-analytics.com">
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
    <img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo" width="148" height="24">
    <nav class="flex-items" aria-label="Main navigation">
        <a class="btn btn-secondary" href="/blog">Blog</a>
        <div id="nav-authed" style="display:none; gap:1rem;">
            <a class="btn btn-secondary" href="/diagrams">My Diagrams</a>
            <a class="btn btn-secondary" href="/logout">Logout</a>
        </div>
        <div id="nav-guest" style="display:none; gap:1rem;">
            <a class="btn btn-secondary" href="/register">Register</a>
            <a class="btn btn-secondary" href="/login">Login</a>
        </div>
    </nav>
    <script>
        if (localStorage.getItem('auth_token')) {
            document.getElementById('nav-authed').style.display = 'flex';
        } else {
            document.getElementById('nav-guest').style.display = 'flex';
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
</footer>

</body>
</html>
