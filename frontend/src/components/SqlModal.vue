<template>
    <div class="modal flex-centered">
        <div class="sql_modal_content">
            <div class="sql_modal_header">
                <span class="sql_modal_title">{{ primaryLabel }} SQL</span>
                <button class="sql_modal_close" @click="$emit('close')">
                    <img src="../icons/close.svg" alt="Close">
                </button>
            </div>
            <div class="sql_textarea_wrapper">
                <textarea class="sql_textarea" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :placeholder="primaryLabel === 'Import' ? 'Paste your SQL here...' : ''"></textarea>
                <button v-if="primaryLabel === 'Export'" class="sql_copy_btn" @click="copyText" title="Copy all text">
                    <img src="../icons/copy.svg" alt="Copy">
                </button>
            </div>
            <div class="sql_modal_footer">
                <div v-if="primaryLabel === 'Export'" class="download-dropdown">
                    <button class="btn btn-secondary download-dropdown__trigger">
                        <img src="../icons/download.svg" alt="Download" class="icon">
                    </button>
                    <div class="download-dropdown__menu">
                        <button @click="downloadSql('sql')">.sql</button>
                        <button @click="downloadSql('txt')">.txt</button>
                    </div>
                </div>
                <button v-if="primaryLabel === 'Import'" class="btn btn-primary" @click="$emit('primary-action')">{{ primaryLabel }}</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'

const $toast = useToast()

const props = defineProps({
    modelValue: String,
    primaryLabel: String,
    filename: { type: String, default: 'schema' },
})

defineEmits(['update:modelValue', 'primary-action', 'close'])

const copyText = async () => {
    await navigator.clipboard.writeText(props.modelValue)
    $toast.success('Text copied')
}

const downloadSql = (ext) => {
    const blob = new Blob([props.modelValue], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.${ext}`
    a.click()
    URL.revokeObjectURL(url)
}
</script>
