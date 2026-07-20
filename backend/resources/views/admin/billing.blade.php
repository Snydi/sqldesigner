<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Admin — SQL Designer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin:0; font-family:'JetBrains Mono',monospace; background:#282828; color:#e0e0e0; font-size:12px; }
        header { background:#262626; color:#fff; padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #3c3c3c; }
        header nav { display:flex; gap:8px; align-items:center; }
        header a, header button { color:#fff; background:none; border:1px solid rgba(255,255,255,.4); border-radius:4px; padding:6px 12px; text-decoration:none; font:inherit; cursor:pointer; }
        main { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }
        .stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:2rem; }
        .stat, .panel { background:#323232; border:1px solid #484848; border-radius:5px; box-shadow:0 2px 8px rgba(0,0,0,.2); }
        .stat { padding:16px; }
        .stat-label { color:#999; font-size:10px; text-transform:uppercase; letter-spacing:.08em; }
        .stat-value { margin-top:7px; font-size:20px; font-weight:600; color:#5db583; }
        .panel { margin-bottom:1.5rem; overflow:hidden; }
        h2 { margin:0; padding:13px 16px; font-size:11px; color:#5db583; text-transform:uppercase; letter-spacing:.08em; border-bottom:1px solid #484848; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:9px 12px; border-bottom:1px solid #3c3c3c; white-space:nowrap; }
        th { color:#999; font-size:9px; text-transform:uppercase; letter-spacing:.06em; }
        td { font-size:10px; }
        .ok { color:#72c795; } .bad { color:#ed9090; } .muted { color:#aaa; }
        .empty { padding:16px; color:#aaa; }
        @media (max-width:700px) { .stats { grid-template-columns:1fr 1fr; } main { padding:1rem; } }
    </style>
</head>
<body>
    <header>
        <strong>SQL Designer — Billing</strong>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Users</a>
            <a href="{{ route('admin.library') }}">Library</a>
            <a href="{{ route('admin.reviews') }}">Reviews</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign Out</button></form>
        </nav>
    </header>
    <main>
        <div class="stats">
            <div class="stat"><div class="stat-label">Active Pro</div><div class="stat-value">{{ $activeProUsers }}</div></div>
            <div class="stat"><div class="stat-label">Paid</div><div class="stat-value">{{ $successfulPayments }}</div></div>
            <div class="stat"><div class="stat-label">Pending</div><div class="stat-value">{{ $pendingPayments }}</div></div>
            <div class="stat"><div class="stat-label">Nominal Revenue</div><div class="stat-value">${{ number_format($nominalRevenueMinor / 100, 2) }}</div></div>
        </div>

        <section class="panel">
            <h2>Latest subscriptions</h2>
            <div class="table-wrap"><table><thead><tr><th>User</th><th>Plan</th><th>Status</th><th>Starts</th><th>Ends</th><th>Cancelled</th></tr></thead><tbody>
            @forelse($subscriptions as $subscription)
                <tr><td>{{ $subscription->user?->email ?? 'Deleted user' }}</td><td>{{ $subscription->plan }}</td><td>{{ $subscription->status->value }}</td><td>{{ $subscription->starts_at?->format('Y-m-d H:i') ?? '—' }}</td><td>{{ $subscription->ends_at?->format('Y-m-d H:i') ?? '—' }}</td><td>{{ $subscription->cancelled_at?->format('Y-m-d H:i') ?? '—' }}</td></tr>
            @empty<tr><td colspan="6" class="empty">No subscriptions</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="panel">
            <h2>Latest payments</h2>
            <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>User</th><th>Status</th><th>Product</th><th>Robokassa</th><th>Method</th><th>Created</th></tr></thead><tbody>
            @forelse($payments as $payment)
                <tr><td>{{ $payment->provider_invoice_id ?? '—' }}</td><td>{{ $payment->user?->email ?? 'Deleted user' }}</td><td>{{ $payment->status->value }}</td><td>${{ number_format($payment->amount_minor / 100, 2) }} {{ $payment->currency }}</td><td>{{ number_format($payment->provider_amount_minor / 100, 2) }} {{ $payment->provider_currency }}</td><td>{{ $payment->payment_method ?? '—' }}</td><td>{{ $payment->created_at?->format('Y-m-d H:i') }}</td></tr>
            @empty<tr><td colspan="7" class="empty">No payments</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="panel">
            <h2>Latest webhook attempts</h2>
            <div class="table-wrap"><table><thead><tr><th>Time</th><th>Invoice</th><th>Status</th><th>HTTP</th><th>Message</th></tr></thead><tbody>
            @forelse($webhookLogs as $log)
                <tr><td>{{ $log->created_at?->format('Y-m-d H:i:s') }}</td><td>{{ $log->provider_invoice_id ?? '—' }}</td><td class="{{ $log->status === 'processed' ? 'ok' : 'bad' }}">{{ $log->status }}</td><td>{{ $log->http_status }}</td><td>{{ $log->message }}</td></tr>
            @empty<tr><td colspan="5" class="empty">No callbacks received</td></tr>@endforelse
            </tbody></table></div>
        </section>

        <section class="panel">
            <h2>Export usage today — MSK</h2>
            <div class="table-wrap"><table><thead><tr><th>User</th><th>Exports</th><th>Date</th></tr></thead><tbody>
            @forelse($exportUsages as $usage)
                <tr><td>{{ $usage->user?->email ?? 'Deleted user' }}</td><td>{{ $usage->count }}</td><td>{{ $usage->usage_date->format('Y-m-d') }}</td></tr>
            @empty<tr><td colspan="3" class="empty">No exports recorded today</td></tr>@endforelse
            </tbody></table></div>
        </section>
    </main>
</body>
</html>
