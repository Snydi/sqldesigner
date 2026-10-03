<template>
    <section v-if="isRequired" class="auth-required" role="status" aria-live="polite">
        <p class="auth-required__eyebrow">Free account required</p>
        <h1 class="auth-required__title">{{ featureName }} requires an account</h1>
        <p v-if="isDemoDiagramSaved" class="auth-required__saved">Your demo diagram is saved. It will be added to your account after you sign in or create an account.</p>
        <p class="auth-required__intro">Sign in or create a free account to continue. With an account, you can:</p>
        <ul class="auth-required__benefits">
            <li>Save diagrams and continue working on them later</li>
            <li>Import schemas and export SQL, JSON, migrations, or PNG</li>
            <li>Use Schema Doctor and review diagram changes</li>
            <li>Share diagrams and collaborate with other people</li>
        </ul>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const featureNames = {
    billing: 'Managing billing and Pro settings',
    diagram: 'Opening this diagram',
    diagrams: 'Accessing your saved diagrams',
    doctor: 'Using Schema Doctor',
    export: 'Exporting your diagram',
    import: 'Importing SQL',
    like: 'Liking community diagrams',
}

const isRequired = computed(() => route.query.reason === 'account-required')
const isDemoDiagramSaved = computed(() => ['import', 'export', 'doctor'].includes(route.query.feature))
const featureName = computed(() => featureNames[route.query.feature] ?? 'This feature')
</script>
