<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SQL Designer</title>
    <meta name="description" content="Visually design MySQL and PostgreSQL schemas with drag-and-drop. Create tables, define relationships, and export SQL scripts — free and browser-based.">
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="manifest" href="/manifest.json">
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
        setTimeout(loadGtag, 4000);
    </script>
</head>
<body>
<div id="app"></div>
</body>
</html>
