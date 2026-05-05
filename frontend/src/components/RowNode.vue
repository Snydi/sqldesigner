<template>
    <!-- Drag handle -->
    <span
        v-if="canEdit"
        class="drag_handle_icon"
        title="Drag to reorder"
        @mousedown.stop.prevent="$emit('row-drag-start', id)"
    ><SvgIcon name="drag" :size="14" /></span>

    <input
        class="input input_designer_row ml-5 mr-5"
        :value="label"
        @mousedown.stop
        @click="canEdit && (data.editing = true)"
        @blur="(e) => { data.editing = false; $emit('update-label', id, label); e.target.scrollLeft = 0; }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing || !canEdit"
    />

    <!-- Constraint badges -->
    <div v-if="badges.length" class="constraint_badges">
        <span v-for="b in badges" :key="b.label" :class="['constraint_badge', b.cls]">{{ b.label }}</span>
    </div>

    <!-- SQL Type -->
    <div>
        <select v-model="sqlTypeForSelect" @change="emitChange()" :disabled="!canEdit">
            <optgroup v-for="(options, groupLabel) in typeGroups" :key="groupLabel" :label="groupLabel">
                <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </optgroup>
            <option :value="data.sqlType">{{ data.sqlType }}</option>
        </select>
    </div>

    <!-- Options -->
    <button v-if="canEdit" class="table_button" @mousedown.stop @click="$emit('toggle-options-modal', id)">
        <SvgIcon name="gear" :size="13" />
    </button>

    <!-- Options modal -->
    <div v-if="data.showOptionsModal" class="options_modal"
         :style="{ left: `${data.modalPosition?.x}px`, top: `${data.modalPosition?.y}px` }"
         @mousedown.stop
         @pointerdown.stop
         ref="optionsModalRef">
        <div class="options_modal_row">
            <p class="modal_text">Key</p>
            <select v-model="data.keyMod" @change="emitChange()">
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
            <input type="text" class="modal_text_input" @mousedown.stop v-model="data.defaultValue" @change="emitChange()" placeholder="NULL">
        </div>
        <div v-if="dbType !== 'postgresql'" class="options_modal_row">
            <p class="modal_text">Comment</p>
            <input type="text" class="modal_text_input" @mousedown.stop v-model="data.comment" @change="emitChange()" placeholder="">
        </div>
        <div v-if="isEnum" class="options_modal_row">
            <p class="modal_text">Values</p>
            <input type="text" class="modal_text_input" @mousedown.stop v-model="enumValuesText" placeholder="'A','B','C'">
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

    <!-- Delete row -->
    <button v-if="canEdit" class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <SvgIcon name="trash" :size="13" />
    </button>

    <!-- Left side handles -->
    <Handle type="source" position="left" id="source-left" />
    <Handle type="target" position="left" id="target-left" />

    <!-- Right side handles -->
    <Handle type="source" position="right" id="source-right" />
    <Handle type="target" position="right" id="target-right" />
</template>

<script setup>
import { computed, ref } from 'vue'
import { Handle } from '@vue-flow/core'
import { onClickOutside } from '@vueuse/core'
import SvgIcon from './SvgIcon.vue'

const props = defineProps({
    id: String,
    data: Object,
    label: String,
    dbType: { type: String, default: 'mysql' },
    canEdit: { type: Boolean, default: true },
    tableColumns: { type: Array, default: () => [] },
    tableUniqueTogether: { type: Array, default: () => [] },
    tableFulltextIndexes: { type: Array, default: () => [] },
})

const emit = defineEmits(['update-label', 'toggle-options-modal', 'delete-node', 'change', 'row-drag-start', 'update-table-constraints', 'update-table-fulltext'])

const optionsModalRef = ref(null)
onClickOutside(optionsModalRef, () => emit('toggle-options-modal', props.id))

const emitChange = () => emit('change', props.id)

// --- Unique Together ---

const showAddPicker = ref(false)
const newUqCols = ref([])

const otherColumns = computed(() => props.tableColumns.filter(c => c !== props.label))
const allColumns = computed(() => props.tableColumns)

const removeUniqueTogether = (idx) => {
    const updated = props.tableUniqueTogether.filter((_, i) => i !== idx)
    emit('update-table-constraints', updated)
}

const addUniqueTogether = () => {
    if (newUqCols.value.length < 1) return
    const newConstraint = [props.label, ...newUqCols.value]
    emit('update-table-constraints', [...props.tableUniqueTogether, newConstraint])
    newUqCols.value = []
    showAddPicker.value = false
}

// --- Fulltext Indexes ---

const showFtAddPicker = ref(false)
const newFtCols = ref([])

const removeFulltextIndex = (idx) => {
    emit('update-table-fulltext', props.tableFulltextIndexes.filter((_, i) => i !== idx))
}

const addFulltextIndex = () => {
    if (newFtCols.value.length < 1) return
    emit('update-table-fulltext', [...props.tableFulltextIndexes, [...newFtCols.value]])
    newFtCols.value = []
    showFtAddPicker.value = false
}

// --- Constraint badges ---

const badges = computed(() => {
    const result = []
    const km = props.data.keyMod
    if (km === 'PRIMARY KEY') result.push({ label: 'PK', cls: 'badge--pk' })
    else if (km === 'UNIQUE') result.push({ label: 'UQ', cls: 'badge--uq' })
    else if (km === 'INDEX') result.push({ label: 'IDX', cls: 'badge--idx' })
    if (props.tableUniqueTogether?.some(g => g.includes(props.label))) {
        result.push({ label: 'U+', cls: 'badge--uq-together' })
    }
    if (props.tableFulltextIndexes?.some(g => g.includes(props.label))) {
        result.push({ label: 'FT', cls: 'badge--ft' })
    }
    return result
})

// --- Type groups ---

const MYSQL_TYPES = {
    'Numeric': [
        { value: 'TINYINT', label: 'TINYINT' },
        { value: 'SMALLINT', label: 'SMALLINT' },
        { value: 'MEDIUMINT', label: 'MEDIUMINT' },
        { value: 'INT', label: 'INT' },
        { value: 'BIGINT', label: 'BIGINT' },
        { value: 'DECIMAL(10,2)', label: 'DECIMAL' },
        { value: 'FLOAT', label: 'FLOAT' },
        { value: 'DOUBLE', label: 'DOUBLE' },
        { value: 'BIT', label: 'BIT' },
    ],
    'String': [
        { value: 'CHAR(255)', label: 'CHAR' },
        { value: 'VARCHAR(255)', label: 'VARCHAR' },
        { value: 'TINYTEXT', label: 'TINYTEXT' },
        { value: 'TEXT', label: 'TEXT' },
        { value: 'MEDIUMTEXT', label: 'MEDIUMTEXT' },
        { value: 'LONGTEXT', label: 'LONGTEXT' },
        { value: 'BINARY(255)', label: 'BINARY' },
        { value: 'VARBINARY(255)', label: 'VARBINARY' },
        { value: 'TINYBLOB', label: 'TINYBLOB' },
        { value: 'BLOB', label: 'BLOB' },
        { value: 'MEDIUMBLOB', label: 'MEDIUMBLOB' },
        { value: 'LONGBLOB', label: 'LONGBLOB' },
        { value: "ENUM('')", label: 'ENUM' },
        { value: "SET('')", label: 'SET' },
    ],
    'Date & Time': [
        { value: 'DATE', label: 'DATE' },
        { value: 'TIME', label: 'TIME' },
        { value: 'DATETIME', label: 'DATETIME' },
        { value: 'TIMESTAMP', label: 'TIMESTAMP' },
        { value: 'YEAR', label: 'YEAR' },
    ],
    'Other': [
        { value: 'JSON', label: 'JSON' },
        { value: 'UUID', label: 'UUID' },
        { value: 'BOOLEAN', label: 'BOOLEAN' },
    ],
}

const POSTGRESQL_TYPES = {
    'Numeric': [
        { value: 'SMALLINT', label: 'SMALLINT' },
        { value: 'INTEGER', label: 'INTEGER' },
        { value: 'BIGINT', label: 'BIGINT' },
        { value: 'DECIMAL(10,2)', label: 'DECIMAL' },
        { value: 'NUMERIC(10,2)', label: 'NUMERIC' },
        { value: 'REAL', label: 'REAL' },
        { value: 'DOUBLE PRECISION', label: 'DOUBLE PRECISION' },
        { value: 'SMALLSERIAL', label: 'SMALLSERIAL' },
        { value: 'SERIAL', label: 'SERIAL' },
        { value: 'BIGSERIAL', label: 'BIGSERIAL' },
    ],
    'String': [
        { value: 'CHAR(255)', label: 'CHAR' },
        { value: 'VARCHAR(255)', label: 'VARCHAR' },
        { value: 'TEXT', label: 'TEXT' },
        { value: 'BYTEA', label: 'BYTEA' },
    ],
    'Date & Time': [
        { value: 'DATE', label: 'DATE' },
        { value: 'TIME', label: 'TIME' },
        { value: 'TIMESTAMP', label: 'TIMESTAMP' },
        { value: 'TIMESTAMPTZ', label: 'TIMESTAMPTZ' },
        { value: 'INTERVAL', label: 'INTERVAL' },
    ],
    'Other': [
        { value: 'BOOLEAN', label: 'BOOLEAN' },
        { value: 'JSON', label: 'JSON' },
        { value: 'JSONB', label: 'JSONB' },
        { value: 'UUID', label: 'UUID' },
    ],
}

const typeGroups = computed(() => props.dbType === 'postgresql' ? POSTGRESQL_TYPES : MYSQL_TYPES)

const isEnum = computed(() => /^ENUM\(/i.test(props.data.sqlType))

const sqlTypeForSelect = computed({
    get() {
        return isEnum.value ? "ENUM('')" : props.data.sqlType
    },
    set(val) {
        props.data.sqlType = val
    }
})

const enumValuesText = computed({
    get() {
        const m = props.data.sqlType.match(/^ENUM\((.*)\)$/i)
        return m ? m[1] : ''
    },
    set(val) {
        props.data.sqlType = `ENUM(${val})`
        emitChange()
    }
})

const toggleNullable = () => {
    props.data.nullable = !props.data.nullable
    emitChange()
}

const toggleUnsigned = () => {
    props.data.unsigned = !props.data.unsigned
    emitChange()
}
</script>

<style scoped>
.input_designer_row {
    flex: 1;
    min-width: 0;
    height: 5px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.ml-5 { margin-left: 5px; }
.mr-5 { margin-right: 5px; }

/* ── Drag handle ─────────────────────────────────────────────── */
.drag_handle_icon {
    flex-shrink: 0;
    width: 24px;
    opacity: 0.35;
    cursor: grab;
    user-select: none;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag_handle_icon:hover { opacity: 0.7; }
.drag_handle_icon:active { cursor: grabbing; opacity: 1; }

/* ── Options modal ───────────────────────────────────────────── */
.options_modal {
    position: absolute;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    background: var(--bg-surface);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
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
    font-family: inherit;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 2px 6px;
    width: 110px;
    box-sizing: border-box;
    background: var(--input-bg);
    color: var(--text-primary);
    outline: none;
    transition: border-color 0.15s;
}

.modal_text_input:focus {
    border-color: var(--color-primary);
}

/* ── Constraint badges ───────────────────────────────────────── */
.constraint_badges {
    display: flex;
    gap: 3px;
    flex-shrink: 0;
    margin-right: 4px;
}

.constraint_badge {
    font-size: 9px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 3px;
    letter-spacing: 0.3px;
    white-space: nowrap;
    line-height: 15px;
    user-select: none;
}

.badge--pk          { background: #f59e0b; color: #fff; }
.badge--uq          { background: #3b82f6; color: #fff; }
.badge--idx         { background: #6b7280; color: #fff; }
.badge--uq-together { background: #10b981; color: #fff; }
.badge--ft          { background: #f97316; color: #fff; }

/* ── Unique Together (options modal section) ─────────────────── */
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
    font-family: inherit;
    font-weight: 600;
    padding: 3px 10px;
    border: none;
    background: var(--color-primary);
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    transition: opacity 120ms;
}

.uq_add_confirm_btn:hover:not(:disabled) { opacity: 0.85; }
.uq_add_confirm_btn:disabled { opacity: 0.45; cursor: default; }

.uq_cancel_btn {
    font-size: 12px;
    font-family: inherit;
    padding: 3px 6px;
    border: none;
    background: none;
    cursor: pointer;
    color: var(--text-muted);
}

.uq_cancel_btn:hover { color: var(--text-primary); }

.uq_add_toggle_btn {
    font-size: 12px;
    font-family: inherit;
    color: var(--color-primary-text);
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px 0;
    text-align: left;
    margin-top: 2px;
    transition: opacity 120ms;
}

.uq_add_toggle_btn:hover { opacity: 0.75; }
</style>
