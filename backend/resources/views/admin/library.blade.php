<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Library</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            transition: border-color .2s, background .2s;
            display: inline-block;
        }
        .nav-btn:hover { border-color: #fff; background: rgba(255,255,255,.1); }
        main { padding: 2rem 1.5rem; max-width: 1000px; margin: 0 auto; }
        .section-heading {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            color: #8f2f2f;
            margin-bottom: 1.2rem;
            padding-bottom: 6px;
            border-bottom: 1px solid #f0eded;
        }
        .lib-diagram-card {
            background: #fff;
            border-radius: 4px;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .lib-diagram-row {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .lib-diagram-info { flex: 1; min-width: 0; }
        .lib-diagram-name {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            text-transform: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .lib-diagram-meta {
            font-size: 10px;
            color: #999;
            margin-top: 3px;
            letter-spacing: .04em;
        }
        .lib-diagram-meta a { color: #8f2f2f; text-decoration: none; }
        .lib-diagram-meta a:hover { text-decoration: underline; }
        .lib-featured-badge {
            font-size: 10px;
            background: #fff8e1;
            color: #7a5800;
            border: 1px solid #f5c842;
            border-radius: 3px;
            padding: 2px 7px;
            letter-spacing: .04em;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .feature-form { display: flex; gap: 6px; flex-shrink: 0; }
        .url-input {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            padding: 6px 10px;
            border: 1px solid #e0b0b0;
            border-radius: 4px;
            width: 260px;
            color: #2c3e50;
            text-transform: none;
            outline: none;
            transition: border-color .2s;
        }
        .url-input:focus { border-color: #8f2f2f; }
        .feature-btn {
            background: #8f2f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s;
        }
        .feature-btn:hover { background: #7a2222; }
        .feature-btn:disabled { background: #ccc; cursor: default; }
        .unfeature-btn {
            background: none;
            border: 1px solid #e0b0b0;
            border-radius: 4px;
            color: #8f2f2f;
            padding: 6px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s, border-color .2s;
        }
        .unfeature-btn:hover { background: #fff0f0; border-color: #8f2f2f; }
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #2e7d52;
            color: #fff;
            padding: 10px 18px;
            border-radius: 4px;
            font-size: 12px;
            letter-spacing: .04em;
            opacity: 0;
            transform: translateY(6px);
            transition: all .25s;
            pointer-events: none;
            z-index: 1000;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.error { background: #8f2f2f; }
        .empty { font-size: 12px; color: #bbb; margin-top: 2rem; }

        @media (max-width: 640px) {
            main { padding: 1.2rem 1rem; }
            header { padding: .8rem 1rem; }
            .lib-diagram-row { flex-wrap: wrap; gap: .6rem; }
            .lib-diagram-info { width: 100%; }
            .feature-form { width: 100%; }
            .url-input { width: 100%; flex: 1 1 auto; min-width: 0; }
        }
    </style>
</head>
<body>
    <header>
        <span>SQL Designer — Library</span>
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.dashboard') }}" class="nav-btn">Dashboard</a>
            <a href="{{ route('admin.reviews') }}" class="nav-btn">Reviews</a>
            <a href="{{ route('admin.promocodes') }}" class="nav-btn">Promocodes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-btn">Sign Out</button>
            </form>
        </div>
    </header>

    <main>
        <div class="section-heading">Library — {{ $libraryDiagrams->count() }} diagrams</div>

        @forelse ($libraryDiagrams as $diagram)
            <div class="lib-diagram-card" id="lib-diagram-{{ $diagram->id }}">
                <div class="lib-diagram-row">
                    <div class="lib-diagram-info">
                        <div class="lib-diagram-name">{{ $diagram->name }}</div>
                        <div class="lib-diagram-meta">
                            {{ $diagram->user->email ?? '—' }} &nbsp;&middot;&nbsp;
                            <a href="/diagrams/{{ $diagram->share_token }}" target="_blank">View</a>
                            @if ($diagram->featured_url)
                                &nbsp;&middot;&nbsp;
                                <a href="{{ $diagram->featured_url }}" target="_blank" rel="noopener noreferrer">{{ $diagram->featured_url }}</a>
                            @endif
                        </div>
                    </div>

                    @if ($diagram->featured)
                        <span class="lib-featured-badge">★ Featured</span>
                        <button class="unfeature-btn" onclick="unfeature({{ $diagram->id }}, this)">Remove</button>
                    @else
                        <div class="feature-form">
                            <input
                                class="url-input"
                                type="url"
                                placeholder="https://backlink-url (optional)"
                                id="url-{{ $diagram->id }}"
                            />
                            <button class="feature-btn" onclick="feature({{ $diagram->id }}, this)">Feature</button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="empty">No diagrams in the library yet.</p>
        @endforelse
    </main>

    <div class="toast" id="toast"></div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast show' + (isError ? ' error' : '');
            setTimeout(() => { t.className = 'toast'; }, 3000);
        }

        async function feature(diagramId, btn) {
            const url = document.getElementById('url-' + diagramId).value.trim();

            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch(`/admin/diagrams/${diagramId}/feature`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ url }),
                });
                if (!res.ok) throw new Error('Server error');
                showToast('Featured!');
                setTimeout(() => location.reload(), 800);
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Feature';
            }
        }

        async function unfeature(diagramId, btn) {
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch(`/admin/diagrams/${diagramId}/feature`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Server error');
                showToast('Removed from featured');
                setTimeout(() => location.reload(), 800);
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Remove';
            }
        }
    </script>
</body>
</html>
