<template>
    <div class="modal-overlay" @click.self="$emit('close')">
        <div class="modal-card sql-modal">
            <div class="modal-header">
                <span class="modal-title">{{ primaryLabel }} SQL</span>
                <button class="modal-close" @click="$emit('close')" aria-label="Close">
                    <SvgIcon name="close" :size="16" />
                </button>
            </div>
            <div class="sql-textarea-wrap">
                <textarea
                    class="sql-textarea"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    :placeholder="primaryLabel === 'Import' ? 'Paste your CREATE TABLE statements here…' : ''"
                ></textarea>
                <button v-if="primaryLabel === 'Export'" class="sql-copy-btn" @click="copyText" title="Copy all">
                    <SvgIcon name="copy" :size="14" />
                </button>
            </div>
            <div class="modal-footer">
                <div v-if="primaryLabel === 'Export'" class="download-dropdown">
                    <button class="btn btn-secondary download-dropdown__trigger">
                        <SvgIcon name="download" :size="15" />
                    </button>
                    <div class="download-dropdown__menu">
                        <button @click="downloadSql('sql')">.sql</button>
                        <button @click="downloadJson">.json</button>
                    </div>
                </div>
                <div v-if="primaryLabel === 'Import'" class="import-footer">
                    <label class="btn btn-secondary import-file-btn" title="Upload from file">
                        <SvgIcon name="import" :size="15" />
                        <input type="file" accept=".sql,.json" @change="handleFileUpload" hidden>
                    </label>
                    <button class="btn btn-primary" @click="$emit('primary-action')" :disabled="loading">
                        {{ loading ? 'Importing…' : primaryLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'
import SvgIcon from '../SvgIcon.vue'

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
                emit('update:modelValue', jsonToSql(JSON.parse(content)))
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
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.modal-card {
    background: var(--bg-surface);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sql-modal {
    width: 700px;
    max-width: calc(100vw - 2rem);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.modal-title {
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-secondary);
}

.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    padding: 4px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    transition: color 120ms, background 120ms;
}

.modal-close:hover {
    color: var(--text-primary);
    background: var(--hover-bg);
}

.sql-textarea-wrap {
    position: relative;
    flex: 1;
    display: flex;
}

.sql-textarea {
    flex: 1;
    width: 100%;
    min-width: 0;
    min-height: 380px;
    padding: 16px;
    border: none;
    border-bottom: 1px solid var(--border-light);
    resize: none;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 0.82rem;
    line-height: 1.7;
    color: var(--text-primary);
    background: var(--bg-surface-alt);
    box-sizing: border-box;
    outline: none;
}

.sql-textarea:focus {
    background: var(--bg-surface);
}

.sql-copy-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 30px;
    height: 30px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-surface);
    color: var(--text-muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 120ms, background 120ms;
}

.sql-copy-btn:hover {
    color: var(--text-primary);
    background: var(--hover-bg);
}

.modal-footer {
    padding: 12px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-surface);
    border-top: 1px solid var(--border-light);
    flex-shrink: 0;
}

.import-footer {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-left: auto;
}

.import-file-btn {
    cursor: pointer;
}

/* Download dropdown */
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
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-left: none;
    border-radius: 0 7px 7px 0;
    overflow: hidden;
    flex-direction: row;
}

.download-dropdown:hover .download-dropdown__menu {
    display: flex;
}

.download-dropdown__trigger {
    border-radius: 7px 0 0 7px !important;
}

.download-dropdown__menu button {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 14px;
    border: none;
    border-left: 1px solid var(--border-color);
    background: var(--bg-surface-alt);
    color: var(--text-secondary);
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 120ms;
}

.download-dropdown__menu button:hover {
    background: var(--hover-bg-alt);
}
</style>
