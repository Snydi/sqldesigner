<template>
    <div class="enum_values_modal" ref="modalRef" @mousedown.stop @pointerdown.stop>
        <p class="enum_modal_title">Enum Values</p>
        <div class="enum_tags" @click="focusInput">
            <span v-for="(val, i) in enumValues" :key="i" class="enum_tag">
                {{ val }}<button class="enum_tag_remove" @mousedown.stop @click.stop="removeEnumValue(i)" tabindex="-1">×</button>
            </span>
            <input
                ref="inputRef"
                class="enum_tag_input"
                v-model="newEnumValue"
                placeholder="Type a value, press Enter"
                @mousedown.stop
                @keydown.enter.prevent="addEnumValue"
                @keydown.tab.prevent="addEnumValue"
            />
        </div>
        <p class="enum_hint">Press Enter or Tab to add</p>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    data: { type: Object, required: true },
    dbType: { type: String, default: 'mysql' },
    ignore: { type: Array, default: () => [] },
})

const emit = defineEmits(['change', 'close'])

const modalRef = ref(null)
const inputRef = ref(null)

onClickOutside(modalRef, () => emit('close'), { ignore: props.ignore })

const focusInput = () => nextTick(() => inputRef.value?.focus())

const enumValues = computed(() => {
    const m = props.data.sqlType.match(/^ENUM\((.*)\)$/i)
    if (!m || !m[1].trim()) return []
    const values = []
    const re = /'([^']*)'|"([^"]*)"/g
    let match
    while ((match = re.exec(m[1])) !== null) {
        const v = (match[1] ?? match[2]).trim()
        if (v !== '') values.push(v)
    }
    return values
})

const newEnumValue = ref('')

const buildEnumType = (vals) => {
    if (!vals.length) return "ENUM('')"
    return `ENUM(${vals.map(v => `'${v.replace(/'/g, "\\'")}'`).join(',')})`
}

const addEnumValue = () => {
    const val = newEnumValue.value.trim().replace(/^['"]|['"]$/g, '')
    if (!val || enumValues.value.includes(val)) return
    props.data.sqlType = buildEnumType([...enumValues.value, val])
    emit('change')
    newEnumValue.value = ''
}

const removeEnumValue = (idx) => {
    props.data.sqlType = buildEnumType(enumValues.value.filter((_, i) => i !== idx))
    emit('change')
}
</script>

<style scoped>
.enum_values_modal {
    position: absolute;
    left: 350px;
    top: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 12px;
    border: 1px solid var(--border-strong);
    border-radius: 5px;
    background: var(--bg-surface);
    z-index: 10;
    min-width: 220px;
    max-width: 320px;
}

.enum_modal_title {
    margin: 0 0 2px 0;
    font-size:$11rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}

.enum_tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
    padding: 5px 6px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--input-bg);
    min-height: 34px;
    cursor: text;
}

.enum_tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: var(--color-primary);
    color: #fff;
    font-size:$11rem;
    font-family: 'Consolas', 'Monaco', monospace;
    padding: 2px 5px 2px 7px;
    border-radius: 3px;
    white-space: nowrap;
}

.enum_tag_remove {
    border: none;
    background: none;
    color: rgba(255,255,255,0.7);
    font-size:$11rem;
    line-height: 1;
    padding: 0 1px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.enum_tag_remove:hover { color: #fff; }

.enum_tag_input {
    border: none;
    outline: none;
    background: transparent;
    color: var(--text-primary);
    font-size:$11rem;
    min-width: 100px;
    flex: 1;
    padding: 0 2px;
}

.enum_tag_input::placeholder { color: var(--text-muted); }

.enum_hint {
    margin: 0;
    font-size:$11rem;
    color: var(--text-muted);
}
</style>
