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
                        v-model="userData.email"
                        required
                    />
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="input input-underline"
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
import router from '@/router/index.js'

const userData = ref({
    email: '',
    password: ''
});

const register = async () => {
    await Auth.register(userData.value);
};
</script>
