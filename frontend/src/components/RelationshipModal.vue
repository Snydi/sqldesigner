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
            <img src="../icons/trash.svg" class="relationship_modal__trash">
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
