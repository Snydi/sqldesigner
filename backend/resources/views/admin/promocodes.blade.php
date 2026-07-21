<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promocodes Admin — SQL Designer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing:border-box; } body { margin:0; background:#282828; color:#e0e0e0; font:12px 'JetBrains Mono',monospace; }
        header { padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; background:#262626; border-bottom:1px solid #3c3c3c; }
        nav, .actions, form.inline { display:flex; align-items:center; gap:8px; } header a, button { padding:6px 10px; border:1px solid rgba(255,255,255,.4); border-radius:4px; color:#fff; background:none; font:inherit; text-decoration:none; cursor:pointer; }
        button.primary { color:#14291c; background:#72c795; border-color:#72c795; font-weight:600; } button.danger { color:#ed9090; border-color:#a75757; }
        main { max-width:1200px; margin:auto; padding:2rem 1.5rem; } h1 { margin:0 0 .45rem; font-size:20px; color:#72c795; } p { color:#aaa; line-height:1.5; }
        .panel { margin-top:1.25rem; padding:1.2rem; overflow:auto; background:#323232; border:1px solid #484848; border-radius:5px; } h2 { margin:0 0 1rem; color:#72c795; font-size:11px; letter-spacing:.08em; text-transform:uppercase; }
        form.create { display:flex; flex-wrap:wrap; gap:10px; align-items:end; } label { display:grid; gap:6px; color:#aaa; font-size:10px; text-transform:uppercase; } input, select { min-height:34px; padding:7px 9px; border:1px solid #5b5b5b; border-radius:4px; color:#fff; background:#262626; font:inherit; }
        .flash { padding:10px 12px; margin-top:1rem; color:#b6e2c8; border:1px solid #3c7654; border-radius:4px; background:#1f3628; } .errors { color:#ed9090; }
        table { width:100%; min-width:800px; border-collapse:collapse; } th,td { padding:9px 8px; text-align:left; border-bottom:1px solid #3c3c3c; white-space:nowrap; } th { color:#999; font-size:9px; letter-spacing:.06em; text-transform:uppercase; } td { font-size:10px; } .used { color:#72c795; } .unused { color:#aaa; }
        td input { width:145px; min-height:29px; padding:5px 7px; } td input.months { width:72px; } .muted { color:#999; }
        @media (max-width:700px) { header { align-items:flex-start; flex-direction:column; } nav { flex-wrap:wrap; } main { padding:1rem; } }
    </style>
</head>
<body>
    <header>
        <strong>SQL Designer — Promocodes</strong>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Users</a><a href="{{ route('admin.library') }}">Library</a><a href="{{ route('admin.billing') }}">Billing</a><a href="{{ route('admin.reviews') }}">Reviews</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign Out</button></form>
        </nav>
    </header>
    <main>
        <h1>One-use Pro promo codes</h1>
        <p>Each code grants a fixed Pro period once. Redeeming a code extends an active Pro period instead of replacing it.</p>
        @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="flash errors">{{ $errors->first() }}</div>@endif

        <section class="panel">
            <h2>Generate random code</h2>
            <form class="create" method="POST" action="{{ route('admin.promocodes.generate') }}">@csrf
                <label>Pro period<select name="duration_months"><option value="1" selected>1 month</option><option value="2">2 months</option><option value="3">3 months</option><option value="6">6 months</option></select></label>
                <button class="primary" type="submit">Generate code</button>
            </form>
        </section>

        <section class="panel">
            <h2>Create custom code</h2>
            <form class="create" method="POST" action="{{ route('admin.promocodes.store') }}">@csrf
                <label>Code<input name="code" maxlength="32" pattern="[A-Za-z0-9-]+" required placeholder="LAUNCH-2026"></label>
                <label>Pro period<select name="duration_months"><option value="1" selected>1 month</option><option value="2">2 months</option><option value="3">3 months</option><option value="6">6 months</option></select></label>
                <button type="submit">Create code</button>
            </form>
        </section>

        <section class="panel">
            <h2>All codes ({{ $promocodes->count() }})</h2>
            <table><thead><tr><th>Code</th><th>Pro period</th><th>Status</th><th>Used by</th><th>Redeemed at</th><th>Created</th><th>Actions</th></tr></thead><tbody>
            @forelse($promocodes as $promocode)
                <tr>
                    @if($promocode->redeemed_at === null)
                        <td><form id="update-{{ $promocode->id }}" method="POST" action="{{ route('admin.promocodes.update', $promocode) }}">@csrf @method('PUT')<input name="code" value="{{ $promocode->code }}" maxlength="32" required></form></td><td><select form="update-{{ $promocode->id }}" class="months" name="duration_months"><option value="1" @selected($promocode->duration_months === 1)>1 month</option><option value="2" @selected($promocode->duration_months === 2)>2 months</option><option value="3" @selected($promocode->duration_months === 3)>3 months</option><option value="6" @selected($promocode->duration_months === 6)>6 months</option></select></td>
                        <td class="unused">Available</td><td class="muted">—</td><td class="muted">—</td><td>{{ $promocode->created_at?->format('Y-m-d H:i') }}</td><td class="actions"><button form="update-{{ $promocode->id }}" type="submit">Save</button><form class="inline" method="POST" action="{{ route('admin.promocodes.delete', $promocode) }}" onsubmit="return confirm('Delete this promo code?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form></td>
                    @else
                        <td>{{ $promocode->code }}</td><td>{{ $promocode->duration_months }} {{ $promocode->duration_months === 1 ? 'month' : 'months' }}</td><td class="used">Used</td><td>{{ $promocode->redeemedBy?->email ?? 'Deleted user' }}</td><td>{{ $promocode->redeemed_at?->format('Y-m-d H:i') }}</td><td>{{ $promocode->created_at?->format('Y-m-d H:i') }}</td><td><form class="inline" method="POST" action="{{ route('admin.promocodes.delete', $promocode) }}" onsubmit="return confirm('Delete this promo code and its redemption record?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form></td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No promo codes yet.</td></tr>
            @endforelse
            </tbody></table>
        </section>
    </main>
</body>
</html>
