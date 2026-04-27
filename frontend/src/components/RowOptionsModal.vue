<template>
    <div class="options_modal"
         :style="{ left: `${data.modalPosition?.x}px`, top: `${data.modalPosition?.y}px` }"
         @mousedown.stop
         @pointerdown.stop
         ref="modalRef">
        <div class="options_modal_row">
            <p class="modal_text">Key</p>
            <select v-model="data.keyMod" @change="$emit('change')">
                <option selected="selected" value="None">None</option>
                <option value="PRIMARY KEY">Primary</option>
                <option value="UNIQUE">Unique</option>
                <option value="INDEX">Index</option>
            </select>
        </div>
        <label v-if="dbType !== 'postgresql'" class="options_modal_row" @mousedown.stop>
            <p class="modal_text">Unsigned</p>
            <input type="checkbox" :checked="data.unsigned" @change="toggleUnsigned">
        </label>
        <label class="options_modal_row" @mousedown.stop>
            <p class="modal_text">Nullable</p>
            <input type="checkbox" :checked="data.nullable" @change="toggleNullable">
        </label>
        <div class="options_modal_row">
            <p class="modal_text">Default</p>
            <input type="text" class="modal_text_input" @mousedown.stop v-model="data.defaultValue" @change="$emit('change')" placeholder="NULL">
        </div>
        <div v-if="dbType !== 'postgresql'" class="options_modal_row">
            <p class="modal_text">Comment</p>
            <input type="text" class="modal_text_input" @mousedown.stop v-model="data.comment" @change="$emit('change')" placeholder="">
        </div>

        <!-- Unique Together section -->
        <div class="options_modal_divider"></div>
        <p class="options_modal_section_label">Unique Together</p>
        <div v-for="(constraint, idx) in tableUniqueTogether" :key="idx" class="uq_constraint_row">
            <span class="uq_constraint_cols" :title="constraint.join(', ')">{{ constraint.join(', ') }}</span>
            <button class="uq_remove_btn" @click="removeUniqueTogether(idx)" title="Remove">×</button>
        </div>
        <p v-if="!tableUniqueTogether.length" class="uq_empty">None</p>
        <template v-if="showAddPicker">
            <div class="uq_picker">
                <label v-for="col in otherColumns" :key="col" class="constraints_col_label">
                    <input type="checkbox" :value="col" v-model="newUqCols" @mousedown.stop @pointerdown.stop @click.stop>
                    <span>{{ col }}</span>
                </label>
                <p v-if="!otherColumns.length" class="uq_empty">No other columns</p>
            </div>
            <div class="uq_picker_actions">
                <button class="uq_add_confirm_btn" @click="addUniqueTogether" :disabled="newUqCols.length < 1">Add</button>
                <button class="uq_cancel_btn" @click="showAddPicker = false; newUqCols = []">Cancel</button>
            </div>
        </template>
        <button v-else class="uq_add_toggle_btn" @click="showAddPicker = true">+ Add constraint</button>

        <!-- Fulltext Indexes section (MySQL only) -->
        <template v-if="dbType !== 'postgresql'">
            <div class="options_modal_divider"></div>
            <p class="options_modal_section_label">Fulltext Indexes</p>
            <div v-for="(ftIndex, idx) in tableFulltextIndexes" :key="idx" class="uq_constraint_row">
                <span class="uq_constraint_cols" :title="ftIndex.join(', ')">{{ ftIndex.join(', ') }}</span>
                <button class="uq_remove_btn" @click="removeFulltextIndex(idx)" title="Remove">×</button>
            </div>
            <p v-if="!tableFulltextIndexes.length" class="uq_empty">None</p>
            <template v-if="showFtAddPicker">
                <div class="uq_picker">
                    <label v-for="col in allColumns" :key="col" class="constraints_col_label">
                        <input type="checkbox" :value="col" v-model="newFtCols" @mousedown.stop @pointerdown.stop @click.stop>
                        <span>{{ col }}</span>
                    </label>
                    <p v-if="!allColumns.length" class="uq_empty">No columns</p>
                </div>
                <div class="uq_picker_actions">
                    <button class="uq_add_confirm_btn" @click="addFulltextIndex" :disabled="newFtCols.length < 1">Add</button>
                    <button class="uq_cancel_btn" @click="showFtAddPicker = false; newFtCols = []">Cancel</button>
                </div>
            </template>
            <button v-else class="uq_add_toggle_btn" @click="showFtAddPicker = true">+ Add fulltext</button>
        </template>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    data: { type: Object, required: true },
    dbType: { type: String, default: 'mysql' },
    label: { type: String, required: true },
    tableColumns: { type: Array, default: () => [] },
    tableUniqueTogether: { type: Array, default: () => [] },
    tableFulltextIndexes: { type: Array, default: () => [] },
})

const emit = defineEmits(['change', 'close', 'update-table-constraints', 'update-table-fulltext'])

const modalRef = ref(null)
onClickOutside(modalRef, () => emit('close'))

const toggleNullable = () => { props.data.nullable = !props.data.nullable; emit('change') }
const toggleUnsigned = () => { props.data.unsigned = !props.data.unsigned; emit('change') }

// --- Unique Together ---

const showAddPicker = ref(false)
const newUqCols = ref([])
const otherColumns = computed(() => props.tableColumns.filter(c => c !== props.label))

const removeUniqueTogether = (idx) => {
    emit('update-table-constraints', props.tableUniqueTogether.filter((_, i) => i !== idx))
}

const addUniqueTogether = () => {
    if (newUqCols.value.length < 1) return
    emit('update-table-constraints', [...props.tableUniqueTogether, [props.label, ...newUqCols.value]])
    newUqCols.value = []
    showAddPicker.value = false
}

// --- Fulltext Indexes ---

const showFtAddPicker = ref(false)
const newFtCols = ref([])
const allColumns = computed(() => props.tableColumns)

const removeFulltextIndex = (idx) => {
    emit('update-table-fulltext', props.tableFulltextIndexes.filter((_, i) => i !== idx))
}

const addFulltextIndex = () => {
    if (newFtCols.value.length < 1) return
    emit('update-table-fulltext', [...props.tableFulltextIndexes, [...newFtCols.value]])
    newFtCols.value = []
    showFtAddPicker.value = false
}
</script>

<style scoped>
.options_modal {
    position: absolute;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 12px;
    border: 1px solid var(--border-strong);
    border-radius: 5px;
    background: var(--bg-surface);
    z-index: 10;
    min-width: 200px;
}

.options_modal_row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

label.options_modal_row {
    cursor: pointer;
}

.modal_text {
    margin: 0;
    font-size: 14px;
    white-space: nowrap;
    flex-shrink: 0;
}

.modal_text_input {
    font-size: 14px;
    border: 1px solid var(--border-color);
    border-radius: 3px;
    padding: 2px 6px;
    width: 110px;
    box-sizing: border-box;
    background: var(--input-bg);
    color: var(--text-primary);
}

.options_modal_divider {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 4px 0;
}

.options_modal_section_label {
    margin: 0 0 4px 0;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}

.uq_constraint_row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    margin-bottom: 2px;
}

.uq_constraint_cols {
    font-size: 12px;
    font-family: 'Consolas', 'Monaco', monospace;
    color: var(--text-primary);
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.uq_remove_btn {
    flex-shrink: 0;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 14px;
    line-height: 1;
    padding: 0 2px;
    color: var(--text-muted);
}

.uq_remove_btn:hover { color: #e53935; }

.uq_empty {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0 0 2px 0;
}

.uq_picker {
    display: flex;
    flex-direction: column;
    gap: 2px;
    max-height: 110px;
    overflow-y: auto;
    margin: 4px 0 6px 0;
}

.constraints_col_label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    cursor: pointer;
    padding: 2px 3px;
    border-radius: 3px;
}

.constraints_col_label:hover { background: var(--hover-bg); }

.constraints_col_label input[type="checkbox"] {
    cursor: pointer;
    accent-color: var(--color-primary);
}

.uq_picker_actions {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 2px;
}

.uq_add_confirm_btn {
    font-size: 12px;
    padding: 3px 10px;
    border: none;
    background: var(--color-primary);
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
}

.uq_add_confirm_btn:disabled { opacity: 0.45; cursor: default; }

.uq_cancel_btn {
    font-size: 12px;
    padding: 3px 6px;
    border: none;
    background: none;
    cursor: pointer;
    color: var(--text-muted);
}

.uq_cancel_btn:hover { color: var(--text-primary); }

.uq_add_toggle_btn {
    font-size: 12px;
    color: var(--color-primary);
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 0;
    text-align: left;
    margin-top: 2px;
}

.uq_add_toggle_btn:hover { opacity: 0.75; }
</style>
