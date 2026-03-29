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
                        <button @click="downloadJson">.json</button>
                    </div>
                </div>
                <div v-if="primaryLabel === 'Import'" class="import-footer">
                    <label class="btn btn-secondary import-file-btn" title="Import from file">
                        <img src="../icons/import.svg" alt="Upload" class="icon">
                        <input type="file" accept=".sql,.txt,.json" @change="handleFileUpload" hidden>
                    </label>
                    <button class="btn btn-primary" @click="$emit('primary-action')" :disabled="loading">
                        {{ loading ? 'Importing...' : primaryLabel }}
                    </button>
                </div>
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
    jsonContent: { type: String, default: null },
    loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'primary-action', 'close'])

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

const downloadJson = () => {
    const blob = new Blob([props.jsonContent], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.json`
    a.click()
    URL.revokeObjectURL(url)
}

const jsonToSql = (json) => {
    let sql = ''
    for (const table of json.tables ?? []) {
        sql += `CREATE TABLE IF NOT EXISTS \`${table.name}\` (\n`
        const cols = (table.columns ?? []).map(col => {
            let def = `  \`${col.name}\` ${col.type}`
            if (col.unsigned) def += ' UNSIGNED'
            def += col.nullable ? ' NULL' : ' NOT NULL'
            if (col.key && col.key !== 'None') def += ` ${col.key}`
            return def
        })
        sql += cols.join(',\n') + '\n);\n\n'
    }
    for (const fk of json.foreignKeys ?? []) {
        sql += `ALTER TABLE \`${fk.table}\`\nADD FOREIGN KEY (\`${fk.column}\`) REFERENCES \`${fk.referencesTable}\`(\`${fk.referencesColumn}\`);\n\n`
    }
    return sql
}

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return
    event.target.value = ''

    const reader = new FileReader()
    reader.onload = (e) => {
        const content = e.target.result
        if (file.name.endsWith('.json')) {
            try {
                const json = JSON.parse(content)
                emit('update:modelValue', jsonToSql(json))
            } catch {
                $toast.error('Invalid JSON file')
            }
        } else {
            emit('update:modelValue', content)
        }
    }
    reader.readAsText(file)
}
</script>
