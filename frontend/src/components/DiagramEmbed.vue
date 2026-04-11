<template>
    <div class="embed-root">
        <div v-if="loading" class="embed-status">
            <span>Loading…</span>
        </div>
        <div v-else-if="error" class="embed-status">
            <span>{{ error }}</span>
        </div>
        <template v-else>
            <VueFlow
                v-model="schema"
                :default-edge-options="{ type: 'chickenFoot' }"
                :nodes-draggable="false"
                :nodes-connectable="false"
                :edges-updatable="false"
                :zoom-on-double-click="false"
                :controlled="false"
                fit-view-on-init
                class="embed-canvas"
            >
                <template #edge-chickenFoot="props">
                    <ChickenFootEdge v-bind="props" />
                </template>

                <template #node-table="nodeProps">
                    <TableNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        :canEdit="false"
                    />
                </template>

                <template #node-row="nodeProps">
                    <RowNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        :dbType="dbType"
                        :canEdit="false"
                    />
                </template>

                <template #node-add-row-button>
                    <!-- hidden in embed -->
                </template>
            </VueFlow>

            <a class="embed-badge" href="https://sql-designer.com" target="_blank" rel="noopener noreferrer">
                Made with SQL Designer
            </a>
        </template>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { VueFlow } from '@vue-flow/core'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './Diagram/TableNode.vue'
import RowNode from './RowNode.vue'
import axios from '@/axios'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@/css/diagram.css'

const token = useRoute().params.token

const loading = ref(true)
const error = ref(null)
const schema = ref([])
const dbType = ref('mysql')

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/diagrams/embed/${token}`)
        dbType.value = data.db_type ?? 'mysql'
        schema.value = data.schema ? JSON.parse(data.schema) : []
    } catch (e) {
        const status = e.response?.status
        if (status === 404) error.value = 'Diagram not found.'
        else if (status === 403) error.value = 'This diagram is not publicly shared.'
        else error.value = 'Failed to load diagram.'
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.embed-root {
    position: relative;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    background: var(--bg-page);
}

.embed-canvas {
    width: 100%;
    height: 100%;
}

.embed-status {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.embed-badge {
    position: absolute;
    bottom: 10px;
    right: 12px;
    font-size: 0.7rem;
    color: var(--text-muted);
    text-decoration: none;
    background: var(--bg-elevated);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 0.2rem 0.5rem;
    opacity: 0.8;
    transition: opacity 0.15s;
    z-index: 10;
}

.embed-badge:hover {
    opacity: 1;
    color: var(--color-primary);
}
</style>
