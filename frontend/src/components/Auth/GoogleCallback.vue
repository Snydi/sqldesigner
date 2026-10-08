<template>
    <div class="centered-container">
        <p>Signing you in...</p>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import store from '@/store/index.js'
import router from '@/router/index.js'
import { useToast } from 'vue-toast-notification'
import { consumePostAuthRoute, navigateAfterAuth } from '@/js/auth-redirect.js'

const route = useRoute()
const $toast = useToast({ position: 'bottom-right' })

onMounted(async () => {
    const token = route.query.token
    const driver = route.query.driver ?? 'provider'
    const label = driver.charAt(0).toUpperCase() + driver.slice(1)
    if (token) {
        store.commit('login', token)
        $toast.success(`Signed in with ${label}`)
        await navigateAfterAuth(router, consumePostAuthRoute())
    } else {
        $toast.error(`${label} sign-in failed`)
        router.push({ name: 'login' })
    }
})
</script>
