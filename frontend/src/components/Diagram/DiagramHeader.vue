<template>
    <header class="dh">
        <div class="dh-group">
            <button v-if="canEdit" class="dh-btn" @click="$emit('add-table')" title="Add Table (Ctrl+A)">
                <SvgIcon name="plus" :size="17" />
            </button>
            <button v-if="isOwner || isDemo" class="dh-btn" @click="$emit('import')" title="Import SQL">
                <SvgIcon name="import" :size="17" />
            </button>
            <button v-if="isOwner || isDemo" class="dh-btn" @click="$emit('export')" title="Export" :disabled="exportLoading">
                <SvgIcon name="export" :size="17" />
            </button>
            <span v-if="!isOwner && !isDemo" class="dh-name">{{ diagramName }}</span>
        </div>
        <div class="dh-group">
            <div v-if="canEdit" class="dh-save-wrap">
                <button class="dh-btn" @click="$emit('save')" title="Save (Ctrl+S)" :disabled="!isDemo && isSaved">
                    <SvgIcon name="save" :size="17" />
                </button>
                <span v-if="!isDemo" class="dh-save-dot" :class="{ 'dh-save-dot--saved': isSaved }"></span>
            </div>
            <button v-if="canEdit && !isDemo" class="dh-btn" @click="$emit('show-changelog')" title="Changelog">
                <SvgIcon name="history" :size="17" />
            </button>
            <div v-if="isOwner" class="dh-share-wrap">
                <button class="dh-btn" @click="$emit('show-share')" title="Share">
                    <SvgIcon name="share" :size="17" />
                </button>
                <span v-if="hasPendingVisitors" class="dh-pending-dot"></span>
            </div>
        </div>
    </header>
</template>

<script setup>
import SvgIcon from '../SvgIcon.vue'

defineProps({
    canEdit: Boolean,
    isOwner: Boolean,
    isDemo: Boolean,
    isSaved: Boolean,
    exportLoading: { type: Boolean, default: false },
    diagramName: String,
    hasPendingVisitors: { type: Boolean, default: false },
})
defineEmits(['add-table', 'import', 'export', 'save', 'show-share', 'show-changelog'])
</script>

<style scoped>
.dh {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0.65rem;
    height: 48px;
    flex-shrink: 0;
    background: rgba(38, 38, 38, 0.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-light);
    position: relative;
    z-index: 50;
}

.dh-group {
    display: flex;
    gap: 4px;
    align-items: center;
}

.dh-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 7px;
    border: 1px solid transparent;
    background: transparent;
    color: var(--text-secondary);
    cursor: pointer;
    transition: background 120ms, border-color 120ms, color 120ms;
}

.dh-btn:hover:not(:disabled) {
    background: var(--bg-surface-alt);
    border-color: var(--border-color);
}

.dh-btn:disabled {
    opacity: 0.35;
    cursor: default;
}

.dh-name {
    font-size: 0.82rem;
    color: var(--text-secondary);
    margin-left: 4px;
}

.dh-save-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.dh-save-dot {
    position: absolute;
    top: 6px;
    right: 4px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #f59e0b;
    border: 1.5px solid var(--bg-page);
    pointer-events: none;
}

.dh-save-dot--saved {
    background: var(--color-primary-text);
}

.dh-share-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.dh-pending-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #f97316;
    border: 1.5px solid var(--bg-page);
    pointer-events: none;
}
</style>
