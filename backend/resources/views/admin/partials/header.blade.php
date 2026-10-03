<header class="admin-header">
    <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="SQL Designer admin dashboard">
        <span class="admin-brand-mark" aria-hidden="true">SQL</span>
        <span>SQL Designer</span>
        <span class="admin-brand-section">Admin</span>
    </a>

    <nav class="admin-nav" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Users</a>
        <a href="{{ route('admin.library') }}" class="admin-nav-link {{ request()->routeIs('admin.library') ? 'is-active' : '' }}" @if(request()->routeIs('admin.library')) aria-current="page" @endif>Library</a>
        <a href="{{ route('admin.billing') }}" class="admin-nav-link {{ request()->routeIs('admin.billing') ? 'is-active' : '' }}" @if(request()->routeIs('admin.billing')) aria-current="page" @endif>Billing</a>
        <a href="{{ route('admin.promocodes') }}" class="admin-nav-link {{ request()->routeIs('admin.promocodes') ? 'is-active' : '' }}" @if(request()->routeIs('admin.promocodes')) aria-current="page" @endif>Promocodes</a>
        <a href="{{ route('admin.reviews') }}" class="admin-nav-link {{ request()->routeIs('admin.reviews') ? 'is-active' : '' }}" @if(request()->routeIs('admin.reviews')) aria-current="page" @endif>Reviews</a>
        <form class="admin-logout-form" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="admin-nav-link admin-sign-out">Sign out</button>
        </form>
    </nav>
</header>
