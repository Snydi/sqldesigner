<template>
    <main class="subscription-status-page">
        <section class="subscription-status-card" aria-labelledby="subscription-started-title">
            <div class="status-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                    <path d="m5 12.5 4.25 4.25L19 7" />
                </svg>
            </div>
            <p class="status-eyebrow">Subscription started</p>
            <h1 id="subscription-started-title">Your Pro subscription is active</h1>
            <p class="status-message">
                Thanks for upgrading. You now have unlimited diagrams and exports.
            </p>
            <div class="status-actions">
                <router-link class="btn btn-primary" :to="{ name: 'diagrams' }">Go to my diagrams</router-link>
                <router-link class="btn btn-secondary" :to="{ name: 'billing' }">View billing</router-link>
            </div>
        </section>
    </main>
</template>

<style scoped>
.subscription-status-page {
    display: flex;
    flex: 1;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
    padding: 2rem 1rem;
    background: var(--bg-elevated);
}

.subscription-status-card {
    width: min(560px, 100%);
    padding: clamp(1.75rem, 5vw, 3rem);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    background: var(--bg-surface);
    box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
}

.status-icon {
    display: inline-flex;
    width: 64px;
    height: 64px;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--color-primary);
    border-radius: 50%;
    color: var(--color-primary-text);
    background: rgba(61, 122, 92, .2);
}

.status-icon svg {
    width: 34px;
    height: 34px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.status-eyebrow {
    margin: 1.25rem 0 .5rem;
    color: var(--color-primary-text);
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

h1 {
    margin: 0;
    color: var(--text-primary);
    font-size: clamp(1.6rem, 5vw, 2.2rem);
}

.status-message {
    margin: .85rem auto 0;
    color: var(--text-secondary);
    font-size: 16px;
    line-height: 1.65;
}

.status-actions {
    display: flex;
    justify-content: center;
    gap: .75rem;
    margin-top: 1.75rem;
}

.status-actions .btn {
    justify-content: center;
    font-size: 16px;
}

@media (max-width: 480px) {
    .status-actions {
        flex-direction: column;
    }
}
</style>
