<template>
    <input
        class="input input_designer_table"
        :value="label"
        @click="data.editing = true"
        @blur="() => { data.editing = false; $emit('update-label', id, label); }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing"
    />

    <button class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <img class="table_icon" src="../icons/cancel.svg" alt="Cancel">
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
