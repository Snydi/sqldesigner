<template>
    <header class="header">
        <div class="flex-items">
            <button class="btn btn-secondary">
                <a href="/">Home</a>
            </button>
            <button class="btn btn-secondary" @click="openExportModal">Export</button>
            <button class="btn btn-secondary" @click="openImportModal">Import</button>
            <button class="btn btn-secondary" @click="addTable">Add Table</button>
        </div>
        <div class="flex-items save-indicator-container">
            <button class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">View diagrams</button>
            <div class="save-button-wrapper">
                <button class="btn btn-secondary" @click="saveDiagram">Save</button>
                <div
                    class="save-indicator"
                    :class="{ 'saved': isSaved, 'unsaved': !isSaved }"
                    :title="isSaved ? 'All changes saved' : 'Unsaved changes'"
                ></div>
            </div>
            <button class="btn btn-secondary" @click="Auth.logout(store)">Log out</button>
        </div>
    </header>
</template>

<script setup>
import { Auth } from '@/services/Auth.js'
import { useStore } from 'vuex'
import router from '@/router/index.js'
import '@/css/header.css'

const store = useStore()

const {
    addTable,
    openExportModal,
    openImportModal,
    saveDiagram,
    isSaved
}
    = defineProps([
    'addTable',
    'openExportModal',
    'openImportModal',
    'saveDiagram',
    'isSaved'
])
</script>