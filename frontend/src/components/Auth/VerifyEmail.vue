<template>
    <div class="centered-container">
        <div class="form-container">
            <h2 class="form-title">Verify Your Email</h2>
            <p v-if="userEmail">We've sent a verification link to <strong>{{ userEmail }}</strong>.</p>
            <p v-else>We've sent a verification link to your email address.</p>
            <p>Please check your inbox and click the link to activate your account.</p>
            <button class="btn btn-primary" @click="resend" :disabled="resent || loading">
                {{ resent ? 'Email sent!' : 'Resend verification email' }}
            </button>
            <br><br>
            <button class="btn btn-secondary" @click="router.push({ name: 'login' })">Back to Login</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toast-notification';
import axios from '@/axios';
import router from '@/router/index.js';
import store from '@/store/index.js';

const $toast = useToast({ position: 'bottom-right' });
const userEmail = ref('');
const resent = ref(false);
const loading = ref(false);

onMounted(async () => {
    if (!store.state.auth_token) return;
    const response = await axios.get('/api/user');
    userEmail.value = response.data.email;
    if (response.data.email_verified_at) {
        await router.push({ name: 'diagrams' });
    }
});

async function resend() {
    loading.value = true;
    try {
        const response = await axios.post('/api/email/resend');
        $toast.success(response.data.message);
        resent.value = true;
    } catch (error) {
        $toast.error(error.response?.data?.message ?? 'Failed to resend email');
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.form-container {
    background-color: var(--bg-surface);
    border-radius: 10px;
    padding: 2rem;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.form-title {
    color: var(--color-primary);
    margin-bottom: 1rem;
}
</style>
