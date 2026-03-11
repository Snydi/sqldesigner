<template>
    <div class="centered-container">
        <div class="form-container">
            <button class="btn btn-secondary float-left" @click="router.push({ name: 'login' })">Login</button>
            <br>
            <h2 class="form-title">Register</h2>
            <form @submit.prevent="register">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        class="input input-underline"
                        id="email"
                        name="email"
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
                        name="password"
                        v-model="userData.password"
                        required
                    />
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Auth } from "@/services/Auth.js";
import { useToast } from 'vue-toast-notification';
import router from '@/router/index.js'

const $toast = useToast({ position: 'bottom-right' })

const userData = ref({
    email: '',
    password: ''
});

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
    await Auth.register(userData.value);
};
</script>
