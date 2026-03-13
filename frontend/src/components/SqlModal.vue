<template>
    <div class="modal flex-centered">
        <div class="sql_modal_content">
            <div class="sql_modal_header">
                <span class="sql_modal_title">{{ primaryLabel }} SQL</span>
                <button class="sql_modal_close" @click="$emit('close')">
                    <img src="../icons/close.svg" alt="Close">
                </button>
            </div>
            <textarea class="sql_textarea" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :placeholder="primaryLabel === 'Import' ? 'Paste your SQL here...' : ''"></textarea>
            <div class="sql_modal_footer">
                <button class="btn btn-primary" @click="$emit('primary-action')">{{ primaryLabel }}</button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    primaryLabel: String,
})

defineEmits(['update:modelValue', 'primary-action', 'close'])
</script>
