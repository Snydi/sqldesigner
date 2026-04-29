<template>
    <div class="rel-modal" ref="modalRef"
         :style="{ left: `${position.x}px`, top: `${position.y}px` }">
        <label class="rel-color-picker" title="Line color">
            <input
                type="color"
                :value="edgeColor || '#b1b1b7'"
                @input="$emit('update-color', $event.target.value)"
                class="table_color_input"
            />
        </label>
        <button class="rel-btn" @click="$emit('update-type', 'one-to-one')" title="One to one">1 → 1</button>
        <button class="rel-btn" @click="$emit('update-type', 'one-to-many')" title="One to many">1 → N</button>
        <button class="rel-btn" @click="$emit('update-type', 'many-to-one')" title="Many to one">N → 1</button>
        <button class="rel-btn" @click="$emit('update-type', 'many-to-many')" title="Many to many">N → N</button>
        <button class="rel-btn rel-btn--delete" @click="$emit('delete')" title="Delete">
            <SvgIcon name="trash" :size="14" />
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
import SvgIcon from '../SvgIcon.vue'

defineProps({
    position: Object,
    edgeColor: String,
})

const emit = defineEmits(['update-type', 'delete', 'close', 'update-color'])

const modalRef = ref(null)
onClickOutside(modalRef, () => emit('close'))
</script>

<style scoped>
.rel-modal {
    position: absolute;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 8px;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    transform: translate(-50%, -50%);
    z-index: 100;
}

.rel-btn {
    padding: 6px 16px;
    border: none;
    border-radius: 6px;
    background: var(--bg-surface-alt);
    color: var(--text-primary);
    font-size: 0.82rem;
    font-family: 'Consolas', 'Monaco', monospace;
    font-weight: 600;
    cursor: pointer;
    transition: background 120ms;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    white-space: nowrap;
}

.rel-btn:hover {
    background: rgba(93, 181, 131, 0.15);
}

.rel-btn--delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.rel-btn--delete:hover {
    background: rgba(239, 68, 68, 0.2);
}

.rel-color-picker {
    position: relative;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid var(--border-strong);
    background: conic-gradient(#e74c3c, #f0b429, #2ecc71, #3498db, #9b59b6, #e74c3c);
    cursor: pointer;
    overflow: hidden;
    flex-shrink: 0;
    align-self: center;
    transition: border-color 120ms;
}

.rel-color-picker:hover {
    border-color: var(--color-primary-text);
}
</style>
