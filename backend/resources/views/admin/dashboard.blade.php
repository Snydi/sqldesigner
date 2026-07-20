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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
        .auth-icon {
            display: inline-block;
            vertical-align: middle;
            width: 13px;
            height: 13px;
            margin-right: 1px;
        }
        .auth-at {
            font-size: 12px;
            font-weight: 600;
            color: #888;
            vertical-align: middle;
        }
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
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .diagram-tag .db-icon {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
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
        .email-btn {
            background: none;
            border: 1px solid #b0c8e0;
            border-radius: 4px;
            color: #2c5f8f;
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
        .email-btn:hover { background: #f0f5ff; border-color: #2c5f8f; }
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s;
        }
        .modal-overlay.show { opacity: 1; pointer-events: all; }
        .modal {
            background: #fff;
            border-radius: 6px;
            padding: 24px 28px;
            width: 520px;
            max-width: 95vw;
            box-shadow: 0 8px 32px rgba(0,0,0,.18);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .modal-title {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            color: #8f2f2f;
        }
        .modal-to {
            font-size: 11px;
            color: #888;
            letter-spacing: .04em;
        }
        .modal-to strong { color: #2c3e50; text-transform: none; }
        .modal input[type="text"], .modal textarea {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            padding: 8px 12px;
            border: 1px solid #e0dede;
            border-radius: 4px;
            width: 100%;
            color: #2c3e50;
            text-transform: none;
            outline: none;
            resize: vertical;
            transition: border-color .2s;
        }
        .modal input[type="text"]:focus, .modal textarea:focus { border-color: #8f2f2f; }
        .modal textarea { min-height: 140px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; }
        .modal-cancel {
            background: none;
            border: 1px solid #e0dede;
            border-radius: 4px;
            color: #888;
            padding: 8px 18px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: border-color .2s;
        }
        .modal-cancel:hover { border-color: #aaa; color: #555; }
        .modal-send {
            background: #8f2f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 20px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s;
        }
        .modal-send:hover { background: #7a2222; }
        .modal-send:disabled { background: #ccc; cursor: default; }
        .sort-toggle {
            display: flex;
            gap: 4px;
        }
        .sort-btn {
            background: none;
            border: 1px solid #e0dede;
            border-radius: 4px;
            color: #999;
            padding: 4px 10px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .sort-btn:hover { border-color: #aaa; color: #555; }
        .sort-btn.active { border-color: #8f2f2f; color: #8f2f2f; background: #fff5f5; }
        .chart-card {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            padding: 18px 20px 14px;
            margin-bottom: 2rem;
        }
        .chart-title {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .1em;
            color: #8f2f2f;
            margin-bottom: 14px;
        }
        .chart-canvas-wrap { position: relative; height: 180px; }
        .modal-subtitle { font-size: 11px; color: #888; letter-spacing: .04em; }
        .modal-subtitle strong { color: #2c3e50; text-transform: none; }

        @media (max-width: 640px) {
            main { padding: 1.2rem 1rem; }
            header { padding: .8rem 1rem; }

            .user-header { flex-wrap: wrap; gap: .6rem; }
            .user-info { width: 100%; min-width: 0; }
            .user-header > div:last-child { width: 100%; flex-wrap: wrap; }
            .impersonate-btn, .email-btn, .delete-btn { flex: 1 1 calc(50% - 4px); text-align: center; }

            .section-heading { flex-wrap: wrap; gap: 6px; }
            .section-heading > div { width: 100%; justify-content: flex-start !important; }

            .sort-toggle { flex-wrap: wrap; }

            .modal { padding: 18px 16px; gap: 12px; }
            .url-input { width: 100%; }
        }
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 1.5rem 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }
        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border: 1px solid #e0dede;
            border-radius: 4px;
            color: #888;
            text-decoration: none;
            background: #fff;
            letter-spacing: .04em;
            transition: border-color .2s, color .2s;
        }
        .pagination a:hover { border-color: #aaa; color: #2c3e50; }
        .pagination span[aria-current="page"] {
            background: #fff5f5;
            border-color: #8f2f2f;
            color: #8f2f2f;
        }
        .pagination span.disabled { opacity: .35; cursor: default; }
        .pagination span.dots { border: none; background: none; }
    </style>
</head>
<body>
    <header>
        <span>SQL Designer — Admin</span>
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('admin.library') }}" class="logout-btn" style="text-decoration:none;">Library</a>
            <a href="{{ route('admin.billing') }}" class="logout-btn" style="text-decoration:none;">Billing</a>
            <a href="{{ route('admin.reviews') }}" class="logout-btn" style="text-decoration:none;">Reviews</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Sign Out</button>
            </form>
        </div>
    </header>

    <main>
        <div class="stats">
            Total users: <strong>{{ $totalUsers }}</strong>
            &nbsp;&middot;&nbsp;
            Returning users: <strong>{{ $returningUsers }}</strong>
            &nbsp;&middot;&nbsp;
            Retention: <strong>{{ $retentionRate }}%</strong>
        </div>

        <div class="chart-card">
            <div class="chart-title">Registrations — Last 60 Days</div>
            <div class="chart-canvas-wrap">
                <canvas id="regChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">Active Users — Last 60 Days</div>
            <div class="chart-canvas-wrap">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="section-heading" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <span>Users — {{ $totalUsers }}</span>
            <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
                <div class="sort-toggle">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'registered', 'page' => 1]) }}" class="sort-btn {{ $sort === 'registered' ? 'active' : '' }}">Registered</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_action', 'page' => 1]) }}" class="sort-btn {{ $sort === 'last_action' ? 'active' : '' }}">Last Action</a>
                </div>
                <button class="feature-btn" style="font-size:10px;padding:5px 12px;" onclick="openBulkEmailModal()">Email All</button>
            </div>
        </div>

        @forelse ($users as $user)
            <div class="user-card">
                <div class="user-header">
                    <div class="user-info">
                        <div class="user-email">{{ $user->email }}</div>
                        <div class="user-meta">
                            ID: {{ $user->id }} &nbsp;&middot;&nbsp;
                            @if ($user->github_id)
                                <img class="auth-icon" src="/images/auth-github.svg" alt="GitHub" title="Registered via GitHub">
                            @elseif ($user->gitlab_id)
                                <img class="auth-icon" src="/images/auth-gitlab.svg" alt="GitLab" title="Registered via GitLab">
                            @elseif ($user->google_id)
                                <img class="auth-icon" src="/images/auth-google.svg" alt="Google" title="Registered via Google">
                            @else
                                <span class="auth-at" title="Registered via email">@</span>
                            @endif
                            &nbsp;&middot;&nbsp;
                            @if ($user->email_verified_at)
                                <span class="verified">Verified</span>
                            @else
                                <span class="unverified">Unverified</span>
                            @endif
                            &nbsp;&middot;&nbsp;
                            Registered: {{ $user->created_at->setTimezone('Europe/Moscow')->format('d M Y H:i') }} MSK
                            &nbsp;&middot;&nbsp;
                            Diagrams: {{ $user->diagrams->count() }}
                            &nbsp;&middot;&nbsp;
                            Plan: <span class="{{ $user->activeSubscription ? 'verified' : '' }}">{{ $user->activeSubscription ? 'Pro' : 'Free' }}</span>
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
                            class="email-btn"
                            onclick="openEmailModal({{ $user->id }}, '{{ addslashes($user->email) }}')"
                        >
                            Email
                        </button>
                        <button
                            class="email-btn"
                            style="border-color:#b0d0b0;color:#2c6e2c;"
                            onclick="openActivityModal({{ $user->id }}, '{{ addslashes($user->email) }}')"
                        >
                            Activity
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
                                    <img class="db-icon" src="/images/db-{{ $diagram->db_type }}.svg" alt="{{ $diagram->db_type }}" title="{{ $diagram->db_type }}">
                                    {{ $diagram->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="empty">No users yet.</p>
        @endforelse

        {{ $users->links('components.pagination', ['navClass' => 'pagination']) }}
    </main>

    <div class="toast" id="toast"></div>

    <div class="modal-overlay" id="bulkEmailModalOverlay">
        <div class="modal">
            <div class="modal-title">Email All Users</div>
            <div class="modal-to" style="color:#a05020;">Sends to <strong>{{ $totalUsers }}</strong> users — jobs will be queued and sent 2 seconds apart</div>
            <input type="text" id="bulkEmailSubject" placeholder="Subject" maxlength="255" />
            <textarea id="bulkEmailBody" placeholder="Message body..."></textarea>
            <div class="modal-actions">
                <button class="modal-cancel" onclick="closeBulkEmailModal()">Cancel</button>
                <button class="modal-send" id="bulkEmailSendBtn" onclick="sendEmailToAll()">Send to All</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="activityModalOverlay">
        <div class="modal" style="width:640px;max-width:96vw;">
            <div class="modal-title">User Activity — Last 60 Days</div>
            <div class="modal-subtitle"><strong id="activityModalEmail"></strong></div>
            <div style="position:relative;height:200px;">
                <canvas id="activityUserChart"></canvas>
            </div>
            <div class="modal-actions">
                <button class="modal-cancel" onclick="closeActivityModal()">Close</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="emailModalOverlay">
        <div class="modal">
            <div class="modal-title">Send Email</div>
            <div class="modal-to">To: <strong id="emailModalRecipient"></strong></div>
            <input type="text" id="emailSubject" placeholder="Subject" maxlength="255" />
            <textarea id="emailBody" placeholder="Message body..."></textarea>
            <div class="modal-actions">
                <button class="modal-cancel" onclick="closeEmailModal()">Cancel</button>
                <button class="modal-send" id="emailSendBtn" onclick="sendEmail()">Send</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const makeChart = (id, labels, shortLabels, data, color) => new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: shortLabels,
                    datasets: [{
                        data,
                        borderColor: color,
                        borderWidth: 2,
                        pointBackgroundColor: color,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: false,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => labels[items[0].dataIndex],
                                label: (item) => ` ${item.raw} user${item.raw !== 1 ? 's' : ''}`,
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: { family: "'JetBrains Mono', monospace", size: 9 },
                                color: '#aaa',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 20,
                            },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { family: "'JetBrains Mono', monospace", size: 9 },
                                color: '#aaa',
                                precision: 0,
                            },
                            grid: { color: '#f0eded' },
                        }
                    }
                }
            });

            const regLabels = @json(array_keys($registrationsByDay));
            const regShort  = regLabels.map(d => { const [,m,day] = d.split('-'); return `${day}/${m}`; });
            makeChart('regChart', regLabels, regShort, @json(array_values($registrationsByDay)), 'rgba(143,47,47,0.85)');

            const actLabels = @json(array_keys($activityByDay));
            const actShort  = actLabels.map(d => { const [,m,day] = d.split('-'); return `${day}/${m}`; });
            makeChart('activityChart', actLabels, actShort, @json(array_values($activityByDay)), 'rgba(46,125,82,0.85)');
        })();
    </script>

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

        function showToast(msg, isError = false) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = 'toast show' + (isError ? ' error' : '');
            setTimeout(() => { t.className = 'toast'; }, 3000);
        }

        function openBulkEmailModal() {
            document.getElementById('bulkEmailSubject').value = '';
            document.getElementById('bulkEmailBody').value = '';
            document.getElementById('bulkEmailSendBtn').disabled = false;
            document.getElementById('bulkEmailSendBtn').textContent = 'Send to All';
            document.getElementById('bulkEmailModalOverlay').classList.add('show');
            document.getElementById('bulkEmailSubject').focus();
        }

        function closeBulkEmailModal() {
            document.getElementById('bulkEmailModalOverlay').classList.remove('show');
        }

        document.getElementById('bulkEmailModalOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeBulkEmailModal();
        });

        async function sendEmailToAll() {
            const subject = document.getElementById('bulkEmailSubject').value.trim();
            const body = document.getElementById('bulkEmailBody').value.trim();
            if (!subject) { showToast('Enter a subject', true); return; }
            if (!body) { showToast('Enter a message body', true); return; }

            if (!confirm('Queue email to all users?')) return;

            const btn = document.getElementById('bulkEmailSendBtn');
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch('/admin/email-all', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ subject, body }),
                });
                if (!res.ok) throw new Error('Server error');
                const data = await res.json();
                showToast(`Queued for ${data.queued} users`);
                closeBulkEmailModal();
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Send to All';
            }
        }

        let userActivityChart = null;

        document.getElementById('activityModalOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeActivityModal();
        });

        function closeActivityModal() {
            document.getElementById('activityModalOverlay').classList.remove('show');
        }

        async function openActivityModal(userId, email) {
            document.getElementById('activityModalEmail').textContent = email;
            document.getElementById('activityModalOverlay').classList.add('show');

            if (userActivityChart) { userActivityChart.destroy(); userActivityChart = null; }

            try {
                const res = await fetch(`/admin/users/${userId}/activity`, {
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Server error');
                const data = await res.json();

                const labels    = Object.keys(data);
                const shortLabels = labels.map(d => { const [,m,day] = d.split('-'); return `${day}/${m}`; });
                const values    = Object.values(data);

                userActivityChart = new Chart(document.getElementById('activityUserChart'), {
                    type: 'line',
                    data: {
                        labels: shortLabels,
                        datasets: [{
                            data: values,
                            borderColor: 'rgba(46,125,82,0.85)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(46,125,82,0.85)',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            fill: false,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: (items) => labels[items[0].dataIndex],
                                    label: (item) => ` ${item.raw} action${item.raw !== 1 ? 's' : ''}`,
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { font: { family: "'JetBrains Mono', monospace", size: 9 }, color: '#aaa', maxRotation: 0, autoSkip: true, maxTicksLimit: 20 },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { font: { family: "'JetBrains Mono', monospace", size: 9 }, color: '#aaa', precision: 0 },
                                grid: { color: '#f0eded' },
                            }
                        }
                    }
                });
            } catch (err) {
                showToast('Error loading activity', true);
                closeActivityModal();
            }
        }

        let emailTargetUserId = null;

        function openEmailModal(userId, email) {
            emailTargetUserId = userId;
            document.getElementById('emailModalRecipient').textContent = email;
            document.getElementById('emailSubject').value = '';
            document.getElementById('emailBody').value = '';
            document.getElementById('emailSendBtn').disabled = false;
            document.getElementById('emailSendBtn').textContent = 'Send';
            document.getElementById('emailModalOverlay').classList.add('show');
            document.getElementById('emailSubject').focus();
        }

        function closeEmailModal() {
            document.getElementById('emailModalOverlay').classList.remove('show');
            emailTargetUserId = null;
        }

        document.getElementById('emailModalOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeEmailModal();
        });

        async function sendEmail() {
            const subject = document.getElementById('emailSubject').value.trim();
            const body = document.getElementById('emailBody').value.trim();
            if (!subject) { showToast('Enter a subject', true); return; }
            if (!body) { showToast('Enter a message body', true); return; }

            const btn = document.getElementById('emailSendBtn');
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const res = await fetch(`/admin/users/${emailTargetUserId}/email`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ subject, body }),
                });
                if (!res.ok) throw new Error('Server error');
                showToast('Email sent');
                closeEmailModal();
            } catch (e) {
                showToast('Error: ' + e.message, true);
                btn.disabled = false;
                btn.textContent = 'Send';
            }
        }
    </script>
</body>
</html>
