const DEFAULT_POST_AUTH_ROUTE = '/diagrams'

export function normalizePostAuthRoute(value) {
    if (Array.isArray(value)) value = value[0]

    // Support values saved by older versions of the app.
    if (value === 'billing') return '/billing'
    if (value === 'diagrams') return DEFAULT_POST_AUTH_ROUTE

    if (typeof value !== 'string' || !value.startsWith('/') || value.startsWith('//')) {
        return DEFAULT_POST_AUTH_ROUTE
    }

    if (value === '/library' || value === '/billing' || value.startsWith('/billing/') || value === '/diagrams' || value.startsWith('/diagrams/')) {
        return value
    }

    return DEFAULT_POST_AUTH_ROUTE
}

export async function navigateAfterAuth(router, route) {
    if (route === '/library') {
        window.location.assign(route)
        return
    }

    await router.push(route)
}

export function rememberPostAuthRoute(value) {
    const route = normalizePostAuthRoute(value)
    sessionStorage.setItem('post_auth_route', route)
    return route
}

export function consumePostAuthRoute(fallback = DEFAULT_POST_AUTH_ROUTE) {
    const savedRoute = sessionStorage.getItem('post_auth_route')
    sessionStorage.removeItem('post_auth_route')
    return normalizePostAuthRoute(savedRoute ?? fallback)
}
