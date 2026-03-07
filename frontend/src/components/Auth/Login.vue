<template>
    <div class="centered-container">
        <div class="form-container">
            <a href="/" class="btn btn-secondary float-left">Home</a>
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
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Auth } from "@/services/Auth.js";

const userData = ref({
    email: '',
    password: ''
});

const login = async () => {
    await Auth.login(userData.value);
};
</script>
