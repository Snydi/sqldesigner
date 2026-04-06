<template>
    <header class="header header--diagram">
        <div class="flex-items">
            <button v-if="canEdit" class="btn btn-secondary" @click="$emit('add-table')" title="Add Table">
                <img src="../icons/plus.svg" alt="Add Table" class="icon" style="width:26px;height:26px;">
            </button>
            <button v-if="isOwner || isDemo" class="btn btn-secondary" @click="$emit('import')" title="Import">
                <img src="../icons/import.svg" alt="Import" class="icon">
            </button>
            <button v-if="isOwner || isDemo" class="btn btn-secondary" @click="$emit('export')" title="Export">
                <img src="../icons/export.svg" alt="Export" class="icon">
            </button>
            <span v-if="!isOwner && !isDemo" class="diagram-name-label">{{ diagramName }}</span>
        </div>
        <div class="flex-items">
            <div v-if="canEdit" class="save-button-wrapper">
                <button class="btn btn-secondary" @click="$emit('save')" title="Save" :disabled="!isDemo && isSaved">
                    <img src="../icons/save.svg" alt="Save" class="icon">
                </button>
                <div v-if="!isDemo" :title="isSaved ? 'All changes saved' : 'Unsaved changes'"></div>
            </div>
            <div v-if="isOwner" class="share-btn-wrapper">
                <button class="btn btn-secondary" @click="$emit('show-share')" title="Share">
                    <img src="../icons/share.svg" alt="Share" class="icon">
                </button>
                <span v-if="hasPendingVisitors" class="share-pending-dot"></span>
            </div>
        </div>
    </header>
</template>

<script setup>
defineProps({
    canEdit: Boolean,
    isOwner: Boolean,
    isDemo: Boolean,
    isSaved: Boolean,
    diagramName: String,
    hasPendingVisitors: { type: Boolean, default: false },
})
defineEmits(['add-table', 'import', 'export', 'save', 'show-share'])
</script>
