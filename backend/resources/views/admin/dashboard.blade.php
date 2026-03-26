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
                    <button
                        class="impersonate-btn"
                        onclick="impersonate({{ $user->id }}, this)"
                    >
                        Login As
                    </button>
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

        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast show' + (isError ? ' error' : '');
            setTimeout(() => { t.className = 'toast'; }, 3000);
        }
    </script>
</body>
</html>
