<template>
    <header class="header">
        <div class="flex-items">
            <a href="/">
                <img src="../icons/logo.svg" alt="sql-designer" class="logo" width="148" height="24">
            </a>
        </div>
        <div class="flex-items">
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">View diagrams</button>
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="Auth.logout()">Log out</button>
        </div>
    </header>
</template>

<script setup>
import { onMounted } from 'vue'
import { Auth } from '@/services/Auth.js'
import { useStore } from 'vuex'
import router from '@/router/index.js'
import '@/css/header.css'

const store = useStore()

onMounted(() => {
    if (document.querySelector('script[src*="googletagmanager"]')) return

    const gtagScript = document.createElement('script')
    gtagScript.async = true
    gtagScript.src = 'https://www.googletagmanager.com/gtag/js?id=G-4L116MPX4C'
    document.head.appendChild(gtagScript)

    window.dataLayer = window.dataLayer || []
    function gtag() { window.dataLayer.push(arguments) }
    window.gtag = gtag
    gtag('js', new Date())
    gtag('config', 'G-4L116MPX4C')
})
</script>