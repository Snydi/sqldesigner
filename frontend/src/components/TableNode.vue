<template>
    <input
        class="input input_designer_table"
        :value="label"
        @mousedown.stop
        @click="data.editing = true"
        @blur="(e) => { data.editing = false; $emit('update-label', id, label); e.target.scrollLeft = 0; }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing"
    />

    <button class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <img class="table_icon" src="../icons/trash.svg" alt="Delete">
    </button>
</template>

<script setup>
defineProps({
    id: String,
    data: Object,
    label: String,
})

defineEmits(['delete-node', 'update-label'])
</script>
