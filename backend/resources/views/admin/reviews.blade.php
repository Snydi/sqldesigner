<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'JetBrains Mono', monospace;
            background: #fff;
            color: #2c3e50;
            min-height: 100vh;
            text-transform: uppercase;
            -webkit-font-smoothing: antialiased;
        }
        header {
            background: #8f2f2f;
            color: #fff;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header span { font-size: 14px; font-weight: 600; letter-spacing: .04em; }
        .header-nav { display: flex; align-items: center; gap: 12px; }
        .nav-btn {
            background: none;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 4px;
            color: #fff;
            padding: 6px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: border-color .2s, background .2s;
        }
        .nav-btn:hover { border-color: #fff; background: rgba(255,255,255,.1); }
        main { padding: 2rem 1.5rem; max-width: 800px; margin: 0 auto; }
        .section-heading {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            color: #8f2f2f;
            margin-bottom: 1.5rem;
            padding-bottom: 6px;
            border-bottom: 1px solid #f0eded;
        }
        .stats {
            font-size: 11px;
            color: #888;
            letter-spacing: .06em;
            margin-bottom: 1.5rem;
        }
        .stats strong { color: #2c3e50; }
        .review-card {
            background: #fff;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .review-stars {
            font-size: 16px;
            letter-spacing: 2px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .review-body { flex: 1; min-width: 0; }
        .review-message {
            font-size: 12px;
            color: #2c3e50;
            text-transform: none;
            line-height: 1.55;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .review-meta {
            font-size: 10px;
            color: #bbb;
            letter-spacing: .04em;
            margin-top: 6px;
        }
        .no-message { font-size: 11px; color: #ccc; font-style: italic; text-transform: none; }
        .empty { font-size: 12px; color: #bbb; margin-top: 2rem; }
    </style>
</head>
<body>
    <header>
        <span>SQL Designer — Reviews</span>
        <div class="header-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-btn">Dashboard</a>
            <a href="{{ route('admin.promocodes') }}" class="nav-btn">Promocodes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-btn">Sign Out</button>
            </form>
        </div>
    </header>

    <main>
        <div class="stats">
            Total reviews: <strong>{{ $reviews->count() }}</strong>
        </div>

        <div class="section-heading">All Reviews</div>

        @forelse ($reviews as $review)
            @php
                $stars = str_repeat('★', $review->stars) . str_repeat('☆', 5 - $review->stars);
            @endphp
            <div class="review-card">
                <div class="review-stars">{{ $stars }}</div>
                <div class="review-body">
                    @if ($review->message)
                        <div class="review-message">{{ $review->message }}</div>
                    @else
                        <span class="no-message">No message</span>
                    @endif
                    <div class="review-meta">
                        {{ $review->user?->email ?? 'Anonymous' }}
                        &nbsp;&middot;&nbsp;
                        {{ $review->created_at->format('d M Y H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <p class="empty">No reviews yet.</p>
        @endforelse
    </main>
</body>
</html>
