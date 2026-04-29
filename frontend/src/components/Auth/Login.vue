<template>
    <div class="centered-container">
        <div class="auth-card">
            <div class="auth-tabs">
                <button class="auth-tab auth-tab--active" @click="router.push({ name: 'login' })">Sign in</button>
                <button class="auth-tab" @click="router.push({ name: 'register' })">Register</button>
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
import '@/css/auth.css'

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
