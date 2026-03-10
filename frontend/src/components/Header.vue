<template>
    <header class="header">
        <div class="flex-items">
            <button class="btn btn-secondary">
                <a href="/">Home</a>
            </button>
        </div>
        <div class="flex-items">
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">View diagrams</button>
            <button v-if="store.state.auth_token" class="btn btn-secondary" @click="Auth.logout()">Log out</button>
        </div>
    </header>
</template>

<script setup>
import { Auth } from '@/services/Auth.js'
import { useStore } from 'vuex'
import router from '@/router/index.js'
import '@/css/header.css'

const store = useStore()
</script>