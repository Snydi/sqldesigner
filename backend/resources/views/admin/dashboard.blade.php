<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — SQL Designer</title>
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
        .logout-btn {
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
            transition: border-color .2s, background .2s;
        }
        .logout-btn:hover { border-color: #fff; background: rgba(255,255,255,.1); }
        main { padding: 2rem 1.5rem; max-width: 1000px; margin: 0 auto; }
        .stats {
            font-size: 11px;
            color: #888;
            letter-spacing: .06em;
            margin-bottom: 1.5rem;
        }
        .stats strong { color: #2c3e50; }
        .user-card {
            background: #fff;
            border-radius: 4px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .user-header {
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-email {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: none;
        }
        .user-meta {
            font-size: 10px;
            color: #999;
            margin-top: 4px;
            letter-spacing: .04em;
        }
        .user-meta .verified { color: #2e7d52; }
        .user-meta .unverified { color: #a05020; }
        .impersonate-btn {
            background: #8f2f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 7px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
            transition: background .2s;
        }
        .impersonate-btn:hover { background: #7a2222; }
        .impersonate-btn:disabled { background: #ccc; color: #999; cursor: default; }
        .delete-btn {
            background: none;
            border: 1px solid #e0b0b0;
            border-radius: 4px;
            color: #8f2f2f;
            padding: 7px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
            transition: background .2s, border-color .2s;
        }
        .delete-btn:hover { background: #fff0f0; border-color: #8f2f2f; }
        .delete-btn:disabled { opacity: .4; cursor: default; }
        .diagrams-section {
            border-top: 1px solid #f0eded;
            padding: 10px 18px;
            background: #fdf9f9;
        }
        .diagrams-label {
            font-size: 10px;
            color: #bbb;
            letter-spacing: .08em;
            margin-bottom: 7px;
        }
        .diagram-list { display: flex; flex-wrap: wrap; gap: 6px; }
        .diagram-tag {
            background: #fff;
            border: 1px solid #e8dede;
            border-radius: 3px;
            padding: 3px 9px;
            font-size: 11px;
            color: #2c3e50;
        }
        .diagram-tag .db-type {
            color: #8f2f2f;
            margin-left: 5px;
            font-size: 10px;
        }
        .no-diagrams { font-size: 11px; color: #ccc; }
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
        .section-heading {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            color: #8f2f2f;
            margin: 2.5rem 0 1rem;
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
        .lib-diagram-meta a {
            color: #8f2f2f;
            text-decoration: none;
        }
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
        .feature-form {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }
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
    </style>
</head>
<body>
    <header>
        <span>SQL Designer — Admin</span>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Sign Out</button>
        </form>
    </header>

    <main>
        <div class="stats">
            Total users: <strong>{{ $users->count() }}</strong>
        </div>

        <div class="section-heading">Library — {{ $libraryDiagrams->count() }} diagrams</div>

        @forelse ($libraryDiagrams as $diagram)
            <div class="lib-diagram-card" id="lib-diagram-{{ $diagram->id }}">
                <div class="lib-diagram-row">
                    <div class="lib-diagram-info">
                        <div class="lib-diagram-name">{{ $diagram->name }}</div>
                        <div class="lib-diagram-meta">
                            {{ $diagram->user->email ?? '—' }} &nbsp;&middot;&nbsp;
                            <a href="/diagrams/{{ $diagram->share_token }}" target="_blank">View</a>
                            @if ($diagram->featured && $diagram->featured_url)
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
                                placeholder="https://their-site.com/page-with-embed"
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

        <div class="section-heading">Users — {{ $users->count() }}</div>

        @forelse ($users as $user)
            <div class="user-card">
                <div class="user-header">
                    <div class="user-info">
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="user-meta">
                            ID: {{ $user->id }} &nbsp;&middot;&nbsp;
                            @if ($user->email_verified_at)
                                <span class="verified">Verified</span>
                            @else
                                <span class="unverified">Unverified</span>
                            @endif
                            &nbsp;&middot;&nbsp;
                            Registered: {{ $user->created_at->format('d M Y H:i') }}
                            &nbsp;&middot;&nbsp;
                            Diagrams: {{ $user->diagrams->count() }}
                        </div>
                    </div>
                    <div style="display:flex;gap:8px">
                        <button
                            class="impersonate-btn"
                            onclick="impersonate({{ $user->id }}, this)"
                        >
                            Login As
                        </button>
                        <button
                            class="delete-btn"
                            onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->email) }}', this)"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <div class="diagrams-section">
                    <div class="diagrams-label">Diagrams</div>
                    @if ($user->diagrams->isEmpty())
                        <span class="no-diagrams">No diagrams</span>
                    @else
                        <div class="diagram-list">
                            @foreach ($user->diagrams as $diagram)
                                <span class="diagram-tag">
                                    {{ $diagram->name }}
                                    <span class="db-type">{{ $diagram->db_type }}</span>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="empty">No users yet.</p>
        @endforelse
    </main>

    <div class="toast" id="toast"></div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        async function impersonate(userId, btn) {
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch(`/admin/impersonate/${userId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Server error');
                const data = await res.json();
                localStorage.setItem('auth_token', data.token);
                showToast('Switching account...');
                setTimeout(() => { window.location.href = '/diagrams'; }, 800);
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Login As';
            }
        }

        async function deleteUser(userId, email, btn) {
            if (!confirm(`Delete ${email} and all their diagrams? This cannot be undone.`)) return;

            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Server error');
                showToast('User deleted');
                btn.closest('.user-card').remove();
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Delete';
            }
        }

        async function feature(diagramId, btn) {
            const url = document.getElementById('url-' + diagramId).value.trim();
            if (!url) { showToast('Enter a URL first', true); return; }

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

        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast show' + (isError ? ' error' : '');
            setTimeout(() => { t.className = 'toast'; }, 3000);
        }
    </script>
</body>
</html>
