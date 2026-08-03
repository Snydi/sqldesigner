<section class="page-intro">
    <div class="intro-inner">
        <p class="breadcrumb"><a href="/">Home</a><span class="sep">/</span><a href="/blog">Blog</a><span class="sep">/</span><span>{{ $breadcrumb }}</span></p>
        <h1 class="page-h1">{{ $heading }}</h1>
        <p class="page-sub">{{ $intro }}</p>
    </div>
</section>

<main class="hub-main">
    <nav class="hub-switcher" aria-label="Database design topic hubs">
        <a href="/blog/erd-tools-guides">ERD tools</a>
        <a href="/blog/relational-database-design-guides">Relational fundamentals</a>
        <a href="/blog/mysql-postgresql-guides">MySQL &amp; PostgreSQL</a>
    </nav>

    <section aria-labelledby="hub-guides-heading">
        <h2 id="hub-guides-heading">Guides in this topic</h2>
        <div class="hub-grid">
            @foreach ($articles as $article)
                <article class="hub-card">
                    <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                    <p>{{ $article['description'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <aside class="hub-next" aria-labelledby="hub-next-heading">
        <h2 id="hub-next-heading">Put the concepts into practice</h2>
        <p>Open the <a href="/demo">SQL Designer demo</a> to model tables and relationships in your browser, or browse all <a href="/blog">database design guides</a>.</p>
    </aside>
</main>
