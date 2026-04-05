<template>
    <div class="centered-container">
        <div class="auth-card">
            <div class="auth-tabs">
                <button class="auth-tab" @click="router.push({ name: 'login' })">Sign in</button>
                <button class="auth-tab" @click="router.push({ name: 'register' })">Register</button>
                <div class="auth-tab-indicator" style="left: 0"></div>
            </div>

            <form class="auth-form" @submit.prevent="login">
                <div class="auth-field">
                    <label class="auth-label" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        class="auth-input"
                        v-model="userData.email"
                        placeholder="you@example.com"
                        required
                        autocomplete="email"
                    />
                </div>
                <div class="auth-field">
                    <label class="auth-label" for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        class="auth-input"
                        v-model="userData.password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <button type="submit" class="auth-submit">Sign in</button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <div class="oauth-row">
                <a href="/auth/google" class="oauth-btn" title="Continue with Google">
                    <img src="../../icons/google.svg" alt="Google" />
                </a>
                <a href="/auth/github" class="oauth-btn oauth-btn--invert" title="Continue with GitHub">
                    <img src="../../icons/github.svg" alt="GitHub" />
                </a>
                <a href="/auth/gitlab" class="oauth-btn" title="Continue with GitLab">
                    <img src="../../icons/gitlab.svg" alt="GitLab" />
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Auth } from '@/services/Auth.js'
import { useToast } from 'vue-toast-notification'
import { useRoute } from 'vue-router'
import router from '@/router/index.js'

const route = useRoute()
const $toast = useToast({ position: 'bottom-right' })

const userData = ref({ email: '', password: '' })

onMounted(() => {
    if (route.query.oauth_error) {
        $toast.error('Sign-in was cancelled or failed')
    }
})

const login = async () => {
    await Auth.login(userData.value)
}
</script>

<style scoped>
.auth-card {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    width: 100%;
    max-width: 380px;
    overflow: hidden;
}

/* ── Tabs ─────────────────────────────────────────────────────── */
.auth-tabs {
    display: flex;
    position: relative;
    border-bottom: 1px solid var(--border-color);
}

.auth-tab {
    flex: 1;
    padding: 1rem;
    background: none;
    border: none;
    font-family: inherit;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-muted);
    cursor: pointer;
    transition: color 0.15s;
}

.auth-tab:first-child {
    color: var(--text-primary);
    border-bottom: 2px solid var(--color-primary);
    margin-bottom: -1px;
}

.auth-tab:hover:not(:first-child) {
    color: var(--text-secondary);
}

/* ── Form ─────────────────────────────────────────────────────── */
.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding: 1.75rem 1.75rem 0;
}

.auth-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.auth-label {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.auth-input {
    width: 100%;
    padding: 0.6rem 0.75rem;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
    color: var(--text-primary);
    outline: none;
    transition: border-color 0.15s;
    box-sizing: border-box;
}

.auth-input::placeholder {
    color: var(--text-muted);
}

.auth-input:focus {
    border-color: var(--color-primary);
}

.auth-submit {
    width: 100%;
    padding: 0.7rem;
    background: var(--color-primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.15s;
    margin-top: 0.25rem;
}

.auth-submit:hover {
    background: var(--color-primary-hover);
}

/* ── Divider ──────────────────────────────────────────────────── */
.auth-divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1.75rem 0;
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-color);
}

/* ── OAuth ────────────────────────────────────────────────────── */
.oauth-row {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    padding: 1rem 1.75rem 1.75rem;
}

.oauth-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: border-color 0.15s, background 0.15s;
}

.oauth-btn:hover {
    border-color: var(--border-strong);
    background: var(--hover-bg);
}

.oauth-btn img {
    width: 20px;
    height: 20px;
}

.oauth-btn--invert img {
    filter: invert(0.8);
}
</style>
