<template>
    <div class="changelog-overlay" @click.self="$emit('close')">
        <div class="changelog-modal">
            <div class="changelog-modal__header">
                <span class="changelog-modal__title">Changelog</span>
                <button class="changelog-modal__close" @click="$emit('close')" aria-label="Close">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3L13 13M13 3L3 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="changelog-modal__body">
                <div v-if="loading" class="changelog-modal__status">Loading…</div>
                <div v-else-if="!entries.length" class="changelog-modal__status">No actions recorded yet.</div>
                <ul v-else class="changelog-modal__list">
                    <li v-for="entry in entries" :key="entry.id" class="changelog-entry">
                        <span class="changelog-entry__avatar" :style="{ background: colorFor(entry.user_id) }">
                            {{ initials(entry.user_name) }}
                        </span>
                        <div class="changelog-entry__body">
                            <span class="changelog-entry__user">{{ entry.user_name }}</span>
                            <span class="changelog-entry__action">
                                <span class="changelog-entry__dot" :style="{ background: actionColor(entry.action) }"></span>
                                <span :style="{ color: actionColor(entry.action) }">{{ labelFor(entry.action, entry.details) }}</span>
                            </span>
                        </div>
                        <span class="changelog-entry__time" :title="fullDate(entry.created_at)">{{ relativeTime(entry.created_at) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Diagram } from '@/services/Diagram.js'
import { CURSOR_COLORS } from '@/composables/useDiagramPresence.js'

const props = defineProps({
    diagramId: { type: Number, required: true },
})
defineEmits(['close'])

const entries = ref([])
const loading = ref(true)

onMounted(async () => {
    const data = await Diagram.getChangelog(props.diagramId)
    entries.value = data ?? []
    loading.value = false
})

const ACTION_LABELS = {
    table_created: (d) => `Created table${d?.table_name ? ` "${d.table_name}"` : ''}`,
    table_copied: (d) => `Copied table${d?.table_name ? ` "${d.table_name}"` : ''}`,
    table_deleted: (d) => `Deleted table${d?.table_name ? ` "${d.table_name}"` : ''}`,
    connection_created: (d) => {
        if (d?.from_table && d?.from_column && d?.to_table && d?.to_column)
            return `Connected ${d.from_table}.${d.from_column} → ${d.to_table}.${d.to_column}`
        if (d?.from_table && d?.to_table) return `Connected "${d.from_table}" → "${d.to_table}"`
        return 'Created connection'
    },
    connection_deleted: (d) => {
        if (d?.from_table && d?.from_column && d?.to_table && d?.to_column)
            return `Removed ${d.from_table}.${d.from_column} → ${d.to_table}.${d.to_column}`
        if (d?.from_table && d?.to_table) return `Removed "${d.from_table}" → "${d.to_table}"`
        return 'Deleted connection'
    },
    relationship_changed: (d) => {
        const type = d?.type ? ` to ${d.type}` : ''
        if (d?.from_table && d?.from_column && d?.to_table && d?.to_column)
            return `Changed ${d.from_table}.${d.from_column} → ${d.to_table}.${d.to_column}${type}`
        return `Changed relationship${type}`
    },
    column_added: (d) => d?.table_name ? `Added column to "${d.table_name}"` : 'Added column',
    column_deleted: (d) => {
        if (d?.column_name && d?.table_name) return `Deleted column "${d.column_name}" from "${d.table_name}"`
        if (d?.table_name) return `Deleted column from "${d.table_name}"`
        return 'Deleted column'
    },
}

const ACTION_COLORS = {
    table_created: '#22c55e',
    table_copied: '#22c55e',
    column_added: '#22c55e',
    connection_created: '#22c55e',
    table_deleted: '#ef4444',
    column_deleted: '#ef4444',
    connection_deleted: '#ef4444',
    relationship_changed: '#f97316',
}

const labelFor = (action, details) => {
    const fn = ACTION_LABELS[action]
    return fn ? fn(details) : action.replace(/_/g, ' ')
}

const actionColor = (action) => ACTION_COLORS[action] ?? '#6b7280'

const colorFor = (userId) => {
    if (!userId) return '#6b7280'
    return CURSOR_COLORS[userId % CURSOR_COLORS.length]
}

const initials = (name) => {
    if (!name) return '?'
    const parts = name.split('@')[0].split(/[._-]/)
    return parts.slice(0, 2).map(p => p[0]?.toUpperCase() ?? '').join('') || name[0].toUpperCase()
}

const relativeTime = (dateStr) => {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000)
    if (diff < 60) return 'just now'
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`
    return new Date(dateStr).toLocaleDateString()
}

const fullDate = (dateStr) => new Date(dateStr).toLocaleString()
</script>

<style scoped>
.changelog-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    z-index: 1000;
    padding: 56px 12px 12px;
}

.changelog-modal {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    width: 380px;
    max-height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.changelog-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.changelog-modal__title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}

.changelog-modal__close {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    padding: 2px;
    display: flex;
    align-items: center;
    border-radius: 4px;
}

.changelog-modal__close:hover {
    color: var(--text-primary);
    background: var(--hover-bg-alt);
}

.changelog-modal__body {
    overflow-y: auto;
    flex: 1;
    min-height: 0;
    padding: 8px 0;
}

.changelog-modal__status {
    padding: 24px 16px;
    text-align: center;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.changelog-modal__list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.changelog-entry {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 16px;
}

.changelog-entry:hover {
    background: var(--hover-bg-alt);
}

.changelog-entry__avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    margin-top: 1px;
}

.changelog-entry__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.changelog-entry__user {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.changelog-entry__action {
    font-size: 0.75rem;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 5px;
}

.changelog-entry__dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.changelog-entry__time {
    font-size: 0.7rem;
    color: var(--text-muted);
    white-space: nowrap;
    flex-shrink: 0;
    margin-top: 2px;
}
</style>
