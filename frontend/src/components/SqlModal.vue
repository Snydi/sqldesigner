<template>
    <div class="modal flex-centered">
        <div class="sql_modal_content">
            <textarea class="sql_textarea" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"></textarea>
            <button class="btn btn-primary" @click="$emit('primary-action')">{{ primaryLabel }}</button>
            <button class="btn btn-primary" @click="$emit('close')">Close</button>
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
