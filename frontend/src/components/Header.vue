<template>
    <header class="header">
        <div class="flex-items">
            <a href="/">
                <img src="../icons/logo.svg" alt="sql-designer" class="logo" width="148" height="24">
            </a>
        </div>
        <div class="flex-items">
            <button v-if="!store.state.auth_token && route.name !== 'demo'" class="hbtn-cta" @click="router.push({ name: 'demo' })">Try Demo</button>
            <button v-if="!store.state.auth_token && route.name === 'demo'" class="hbtn-cta" @click="router.push({ name: 'register' })">Register for free</button>
            <button v-if="store.state.auth_token" class="hbtn" @click="router.push({ name: 'diagrams' })" title="View diagrams">
                <SvgIcon name="eye" :size="17" />
            </button>
            <button v-if="store.state.auth_token" class="hbtn" @click="Auth.logout()" title="Log out">
                <SvgIcon name="logout" :size="17" />
            </button>
        </div>
    </header>
</template>

<script setup>
import { onMounted } from 'vue'
import { Auth } from '@/services/Auth.js'
import SvgIcon from './SvgIcon.vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import router from '@/router/index.js'
import '@/css/header.css'

const store = useStore()
const route = useRoute()


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

<style scoped>
.logo {
    margin-top: 4px;
    height: 24px;
    width: auto;
    max-width: 120px;
}
</style>
