<template>
    <div class="relationship_modal" ref="modalRef"
         :style="{ left: `${position.x}px`, top: `${position.y}px` }">
        <label class="relationship_modal__color_picker center" title="Line color">
            <input
                type="color"
                :value="edgeColor || '#b1b1b7'"
                @input="$emit('update-color', $event.target.value)"
                class="table_color_input"
            />
        </label>
        <button @click="$emit('update-type', 'one-to-one')" title="One to one">1 → 1</button>
        <button @click="$emit('update-type', 'one-to-many')" title="One to many">1 → N</button>
        <button @click="$emit('update-type', 'many-to-one')" title="Many to one">N → 1</button>
        <button @click="$emit('update-type', 'many-to-many')" title="Many to many">N → N</button>
        <button class="relationship_modal__delete" @click="$emit('delete')" title="Delete">
            <img src="../../icons/trash.svg" class="relationship_modal__trash" alt="trash_icon">
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

defineProps({
    position: Object,
    edgeColor: String,
})

const emit = defineEmits(['update-type', 'delete', 'close', 'update-color'])

const modalRef = ref(null)

onClickOutside(modalRef, () => emit('close'))
</script>

<style scoped>
.relationship_modal {
    position: absolute;
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 8px;
    background-color: var(--bg-surface);
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
    transform: translate(-50%, -50%);
}

.relationship_modal button {
    padding: 6px 14px;
    border: none;
    border-radius: 5px;
    background-color: var(--color-primary);
    color: #fff;
    font-size: 14px;
    font-family: inherit;
    cursor: pointer;
    transition: background-color 0.15s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
}

.relationship_modal button:hover {
    background-color: var(--color-primary-hover);
}

.relationship_modal__delete {
    background-color: #888 !important;
}

.relationship_modal__delete:hover {
    background-color: #555 !important;
}

.relationship_modal__color_picker {
    position: relative;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid white;
    background: conic-gradient(#e74c3c, #f0b429, #2ecc71, #3498db, #9b59b6, #e74c3c);
    cursor: pointer;
    overflow: hidden;
    flex-shrink: 0;
    align-self: center;
}

.relationship_modal__color_picker:hover {
    border-color: #3d7a5c;
}

.relationship_modal__trash {
    width: 16px;
    height: 16px;
    filter: brightness(0) invert(1);
}
</style>
