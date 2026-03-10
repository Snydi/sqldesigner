<template>
    <button class="add_row_button" @mousedown.stop @click="$emit('add-row')">
        <img class="table_icon" src="../icons/plus.svg" alt="Add row">
    </button>
</template>

<script setup>
defineEmits(['add-row'])
</script>
