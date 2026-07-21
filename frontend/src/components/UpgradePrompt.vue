<template>
    <div class="upgrade-overlay" @click.self="$emit('close')">
        <div class="upgrade-card" role="dialog" aria-modal="true" aria-labelledby="upgrade-title">
            <button class="upgrade-close" type="button" aria-label="Close" @click="$emit('close')">×</button>
            <div class="upgrade-eyebrow">Free plan limit reached</div>
            <h2 id="upgrade-title">Keep working with Pro</h2>
            <p>{{ message }}</p>
            <div class="upgrade-actions">
                <button type="button" class="btn btn-secondary" @click="$emit('close')">Not now</button>
                <a class="btn btn-primary" href="/pricing">View Pro plan</a>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: { type: String, required: true },
})

defineEmits(['close'])
</script>

<style scoped>
.upgrade-overlay {
    position: fixed;
    inset: 0;
    z-index: 3000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.68);
}

.upgrade-card {
    position: relative;
    width: min(440px, 100%);
    padding: 2rem;
    text-align: left;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45);
}

.upgrade-close {
    position: absolute;
    top: 10px;
    right: 12px;
    border: 0;
    background: transparent;
    color: var(--text-muted);
    font-size: 1.5rem;
    cursor: pointer;
}

.upgrade-eyebrow { color: var(--color-primary-text); font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
h2 { margin: .55rem 0 .7rem; color: var(--text-primary); font-size: 1.35rem; }
p { margin: 0; color: var(--text-secondary); line-height: 1.6; }
.upgrade-actions { display: flex; justify-content: flex-end; gap: .65rem; margin-top: 1.5rem; }
</style>
