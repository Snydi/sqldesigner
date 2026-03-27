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
            <a href="/auth/google" class="btn btn-google">
                <img src="../../icons/google.svg" alt="Google" class="google-icon" />
                Continue with Google
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Auth } from "@/services/Auth.js";
import router from '@/router/index.js'

const userData = ref({
    email: '',
    password: ''
});

const login = async () => {
    await Auth.login(userData.value);
};
</script>
