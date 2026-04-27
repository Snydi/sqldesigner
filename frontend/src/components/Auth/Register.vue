<template>
    <div class="centered-container">
        <div class="auth-card">
            <div class="auth-tabs">
                <button class="auth-tab" @click="router.push({ name: 'login' })">Sign in</button>
                <button class="auth-tab auth-tab--active" @click="router.push({ name: 'register' })">Register</button>
            </div>

            <form class="auth-form" @submit.prevent="register">
                <div class="auth-field">
                    <label class="auth-label" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
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
                        name="password"
                        class="auth-input"
                        v-model="userData.password"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <button type="submit" class="auth-submit">Create account</button>
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
import { ref } from 'vue'
import { Auth } from '@/services/Auth.js'
import { useToast } from 'vue-toast-notification'
import router from '@/router/index.js'
import '@/css/auth.css'

const $toast = useToast({ position: 'bottom-right' })

const userData = ref({ email: '', password: '' })

function validatePassword(password) {
    if (password.length < 8) return 'Password must be at least 8 characters'
    if (!/[a-z]/.test(password)) return 'Password must contain a lowercase letter'
    if (!/[A-Z]/.test(password)) return 'Password must contain an uppercase letter'
    if (!/[0-9]/.test(password)) return 'Password must contain a number'
    return null
}

const register = async () => {
    const error = validatePassword(userData.value.password)
    if (error) {
        $toast.error(error)
        return
    }
    await Auth.register(userData.value)
}
</script>
