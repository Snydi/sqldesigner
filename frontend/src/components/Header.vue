<template>
    <header class="header">
        <div class="flex-items">
            <a href="/">
                <img src="../icons/logo.svg" alt="sql-designer" class="logo">
            </a>
        </div>
        <div class="flex-items">
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">View diagrams</button>
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="Auth.logout()">Log out</button>
        </div>
    </header>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4L116MPX4C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-4L116MPX4C');
    </script>
</template>

<script setup>
import { Auth } from '@/services/Auth.js'
import { useStore } from 'vuex'
import router from '@/router/index.js'
import '@/css/header.css'

const store = useStore()
</script>