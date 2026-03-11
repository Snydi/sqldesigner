<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="ebdef4d5d4512d71" />
    <title>@yield('title', 'SQL Designer — Free Online MySQL Database Schema Designer')</title>
    @vite(['src/css/app.css'])
    @yield('head')
</head>
<body class="home-page">

<header class="header" role="banner">
    <img class="logo" src="{{ Vite::asset('src/icons/logo.svg') }}" alt="SQL Designer logo">
    <nav class="flex-items" aria-label="Main navigation">
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
