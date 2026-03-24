<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SQL Designer — Free Online MySQL & PostgreSQL Schema Designer</title>
    <meta name="description" content="Visually design MySQL and PostgreSQL database schemas with a drag-and-drop interface. Create tables, define relationships, and export SQL scripts — free and browser-based.">
    <meta name="robots" content="noindex, follow">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="manifest" href="/manifest.json">
    @vite(['src/css/app.css', 'src/main.js'])
    <!-- Google Tag Manager — deferred until first user interaction to avoid affecting LCP -->
    <script>
        window.dataLayer = window.dataLayer || [];
        function _loadGTM() {
            if (window._gtmLoaded) return;
            window._gtmLoaded = true;
            window.dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
            var f = document.getElementsByTagName('script')[0];
            var j = document.createElement('script');
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=GTM-5JFWWW2F';
            f.parentNode.insertBefore(j, f);
        }
        ['click', 'scroll', 'keydown', 'touchstart'].forEach(function(e) {
            document.addEventListener(e, _loadGTM, { once: true, passive: true });
        });
    </script>
</head>
<body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5JFWWW2F" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<div id="app"></div>
</body>
</html>
