<template>
    <div class="centered-container">
        <div class="form-container">
            <button class="btn btn-secondary float-left" @click="router.push({ name: 'register' })">Register</button>
            <br>
            <h2 class="form-title">Login</h2>
            <form @submit.prevent="login">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        class="input input-underline"
                        id="email"
                        v-model="userData.email"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="input input-underline"
                        id="password"
                        v-model="userData.password"
                        required
                    />
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div class="google-divider">or</div>
            <a href="/auth/google" class="btn btn-oauth">
                <img src="../../icons/google.svg" alt="Google" class="oauth-icon" />
                Continue with Google
            </a>
            <a href="/auth/github" class="btn btn-oauth">
                <img src="../../icons/github.svg" alt="GitHub" class="oauth-icon" />
                Continue with GitHub
            </a>
            <a href="/auth/gitlab" class="btn btn-oauth">
                <img src="../../icons/gitlab.svg" alt="GitLab" class="oauth-icon" />
                Continue with GitLab
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Auth } from "@/services/Auth.js";
import { useToast } from 'vue-toast-notification';
import { useRoute } from 'vue-router';
import router from '@/router/index.js'

const route = useRoute()
const $toast = useToast({ position: 'bottom-right' })

const userData = ref({
    email: '',
    password: ''
});

onMounted(() => {
    if (route.query.oauth_error) {
        $toast.error('Sign-in was cancelled or failed')
    }
})

const login = async () => {
    await Auth.login(userData.value);
};
</script>
