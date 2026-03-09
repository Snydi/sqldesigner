<template>
    <div class="relationship_modal" ref="modalRef"
         :style="{ left: `${position.x}px`, top: `${position.y}px` }">
        <button @click="$emit('update-type', 'one-to-one')">One to one</button>
        <button @click="$emit('update-type', 'one-to-many')">One to many</button>
        <button @click="$emit('update-type', 'many-to-one')">Many to one</button>
        <button @click="$emit('update-type', 'many-to-many')">Many to many</button>
        <button @click="$emit('delete')">Delete</button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

defineProps({
    position: Object,
})

const emit = defineEmits(['update-type', 'delete', 'close'])

const modalRef = ref(null)

onClickOutside(modalRef, () => emit('close'))
</script>
