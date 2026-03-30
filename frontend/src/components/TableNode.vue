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

    <button class="table_button table_button--add-row" @mousedown.stop @click="$emit('add-row', id)">
        <img class="table_icon" src="../icons/plus.svg" alt="Add row">
    </button>

    <button class="table_button table_button--copy" @mousedown.stop @click="$emit('copy-table', id)">
        <img class="table_icon" src="../icons/copy.svg" alt="Copy">
    </button>

    <button class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <img class="table_icon" src="../icons/trash.svg" alt="Delete">
    </button>

    <div class="table_resize_handle table_resize_handle--left" @mousedown.stop="$emit('resize-start', id, $event, 'left')"></div>
    <div class="table_resize_handle table_resize_handle--right" @mousedown.stop="$emit('resize-start', id, $event, 'right')"></div>
</template>

<script setup>
defineProps({
    id: String,
    data: Object,
    label: String,
})

defineEmits(['delete-node', 'update-label', 'copy-table', 'add-row', 'resize-start'])
</script>
