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

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.sql_modal_content {
    background: var(--bg-surface);
    border-radius: 10px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.3);
    width: 700px;
    max-width: calc(100vw - 2rem);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sql_modal_header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: var(--color-primary);
    flex-shrink: 0;
}

.sql_modal_title {
    font-size: 14px;
    font-weight: 600;
    color: white;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.sql_modal_close {
    width: 26px;
    height: 26px;
    padding: 4px;
    border: none;
    background: rgba(255, 255, 255, 0.15);
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.15s;
}

.sql_modal_close:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

.sql_modal_close img {
    width: 14px;
    height: 14px;
    filter: brightness(0) invert(1);
}

.sql_textarea_wrapper {
    position: relative;
    flex: 1;
    display: flex;
}

.sql_copy_btn {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    padding: 5px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    background: var(--bg-surface);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.6;
    transition: opacity 0.15s;
}

.sql_copy_btn:hover {
    opacity: 1;
}

.sql_copy_btn img {
    width: 100%;
    height: 100%;
}

.sql_textarea {
    flex: 1;
    width: 100%;
    min-width: 0;
    min-height: 420px;
    padding: 16px;
    border: none;
    border-bottom: 1px solid var(--border-light);
    resize: none;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
    background: var(--bg-surface-alt);
    box-sizing: border-box;
}

.sql_textarea:focus {
    outline: none;
    background: var(--bg-surface);
}

.sql_modal_footer {
    padding: 12px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-surface);
    flex-shrink: 0;
}

.import-footer {
    display: flex;
    gap: 8px;
    align-items: center;
}

.import-file-btn {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.import-file-btn .icon {
    width: 16px;
    height: 16px;
    filter: brightness(0) invert(1);
}

.download-dropdown {
    position: relative;
    display: flex;
    align-items: stretch;
}

.download-dropdown__menu {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    bottom: 0;
    background: var(--color-primary);
    border-radius: 0 6px 6px 0;
    overflow: hidden;
    flex-direction: row;
}

.download-dropdown:hover .download-dropdown__menu {
    display: flex;
}

.download-dropdown__trigger {
    border-radius: 6px 0 0 6px !important;
}

.download-dropdown__trigger .icon {
    width: 16px;
    height: 16px;
    filter: brightness(0) invert(1);
}

.download-dropdown__menu button {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 12px;
    border: none;
    border-left: 1px solid rgba(255, 255, 255, 0.2);
    background: var(--color-primary);
    color: var(--color-text-on-primary);
    font-size: 13px;
    cursor: pointer;
    white-space: nowrap;
}

.download-dropdown__menu button:hover {
    background: var(--color-primary-hover);
}
</style>
