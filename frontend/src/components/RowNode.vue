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
        @focus="(e) => { if (!canEdit) return; data.editing = true; e.target.select(); }"
        @blur="(e) => { data.editing = false; $emit('update-label', id, label); e.target.scrollLeft = 0; }"
        @input="$emit('update-label', id, $event.target.value)"
        @keydown="(e) => {
            if (e.shiftKey && e.key === 'Enter' && canEdit) { e.preventDefault(); $emit('add-row-after', id) }
            else if (e.key === 'Tab' && e.shiftKey) { e.preventDefault(); $emit('tab-prev', id) }
        }"
        :readonly="!data.editing || !canEdit"
    />

    <!-- Constraint badges + enum button grouped together -->
    <div v-if="badges.length || (isEnum && canEdit)" class="constraint_badges">
        <span v-for="b in badges" :key="b.label" :class="['constraint_badge', b.cls]">{{ b.label }}</span>
        <button v-if="isEnum && canEdit" ref="enumBtnRef" class="table_button enum_values_btn" @mousedown.stop @click="showEnumModal = !showEnumModal" title="Edit enum values">
            <SvgIcon name="list" :size="13" />
        </button>
        <span v-if="isEnum && isEnumEmpty && canEdit" class="enum_empty_wrapper">
            <SvgIcon name="warning" :size="13" stroke="#ef4444" />
        </span>
    </div>

    <!-- Enum values modal -->
    <EnumValuesModal
        v-if="isEnum && showEnumModal"
        :data="data"
        :dbType="dbType"
        :ignore="[enumBtnRef]"
        @change="emitChange()"
        @close="showEnumModal = false"
    />

    <!-- SQL Type -->
    <div class="type_cell">
        <input
            v-if="isLengthType"
            type="number"
            class="length_input"
            :value="typeLengthValue"
            min="1"
            :disabled="!canEdit"
            @mousedown.stop
            @change="onLengthChange"
        />
        <select v-model="sqlTypeForSelect" @change="emitChange()" :disabled="!canEdit">
            <optgroup v-for="(options, groupLabel) in typeGroups" :key="groupLabel" :label="groupLabel">
                <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </optgroup>
            <option :value="data.sqlType">{{ data.sqlType }}</option>
        </select>
    </div>

    <!-- Options -->
    <button v-if="canEdit" ref="gearBtnRef" class="table_button" @mousedown.stop @click="$emit('toggle-options-modal', id)">
        <SvgIcon name="gear" :size="13" />
    </button>

    <!-- Options modal -->
    <RowOptionsModal
        v-if="data.showOptionsModal"
        :data="data"
        :dbType="dbType"
        :label="label"
        :tableColumns="tableColumns"
        :tableUniqueTogether="tableUniqueTogether"
        :tableFulltextIndexes="tableFulltextIndexes"
        :ignore="[gearBtnRef]"
        @change="emitChange()"
        @close="$emit('toggle-options-modal', id)"
        @update-table-constraints="$emit('update-table-constraints', $event)"
        @update-table-fulltext="$emit('update-table-fulltext', $event)"
    />

    <!-- Delete row -->
    <button v-if="canEdit" class="table_button" @mousedown.stop @click="$emit('delete-node', id)" @keydown="(e) => { if (e.key === 'Tab' && !e.shiftKey) { e.preventDefault(); $emit('tab-next', id) } }">
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
import { computed, ref, watch } from 'vue'
import { Handle } from '@vue-flow/core'
import SvgIcon from './SvgIcon.vue'
import RowOptionsModal from './RowOptionsModal.vue'
import EnumValuesModal from './EnumValuesModal.vue'

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

const emit = defineEmits(['update-label', 'toggle-options-modal', 'delete-node', 'change', 'row-drag-start', 'update-table-constraints', 'update-table-fulltext', 'add-row-after', 'tab-next', 'tab-prev'])

const emitChange = () => emit('change', props.id)

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
    if (props.data.nullable) {
        result.push({ label: 'NULL', cls: 'badge--null' })
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
        { value: "ENUM('')", label: 'ENUM' },
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

const MSACCESS_TYPES = {
    'Numeric': [
        { value: 'BYTE', label: 'BYTE' },
        { value: 'SHORT', label: 'SHORT' },
        { value: 'LONG', label: 'LONG' },
        { value: 'SINGLE', label: 'SINGLE' },
        { value: 'DOUBLE', label: 'DOUBLE' },
        { value: 'CURRENCY', label: 'CURRENCY' },
        { value: 'DECIMAL(10,2)', label: 'DECIMAL' },
        { value: 'AUTOINCREMENT', label: 'AUTOINCREMENT' },
    ],
    'String': [
        { value: 'TEXT(255)', label: 'TEXT' },
        { value: 'CHAR(255)', label: 'CHAR' },
        { value: 'VARCHAR(255)', label: 'VARCHAR' },
        { value: 'MEMO', label: 'MEMO' },
    ],
    'Date & Time': [
        { value: 'DATETIME', label: 'DATETIME' },
        { value: 'DATE', label: 'DATE' },
    ],
    'Other': [
        { value: 'YESNO', label: 'YESNO' },
        { value: 'OLEOBJECT', label: 'OLEOBJECT' },
        { value: 'GUID', label: 'GUID' },
    ],
}

const SQLSERVER_TYPES = {
    'Numeric': [
        { value: 'TINYINT', label: 'TINYINT' },
        { value: 'SMALLINT', label: 'SMALLINT' },
        { value: 'INT', label: 'INT' },
        { value: 'BIGINT', label: 'BIGINT' },
        { value: 'DECIMAL(10,2)', label: 'DECIMAL' },
        { value: 'FLOAT', label: 'FLOAT' },
        { value: 'REAL', label: 'REAL' },
        { value: 'MONEY', label: 'MONEY' },
        { value: 'SMALLMONEY', label: 'SMALLMONEY' },
    ],
    'String': [
        { value: 'NVARCHAR(255)', label: 'NVARCHAR' },
        { value: 'VARCHAR(255)', label: 'VARCHAR' },
        { value: 'NCHAR(255)', label: 'NCHAR' },
        { value: 'CHAR(255)', label: 'CHAR' },
        { value: 'NVARCHAR(MAX)', label: 'NVARCHAR(MAX)' },
        { value: 'VARCHAR(MAX)', label: 'VARCHAR(MAX)' },
    ],
    'Date & Time': [
        { value: 'DATE', label: 'DATE' },
        { value: 'TIME', label: 'TIME' },
        { value: 'DATETIME2', label: 'DATETIME2' },
        { value: 'DATETIME', label: 'DATETIME' },
        { value: 'SMALLDATETIME', label: 'SMALLDATETIME' },
        { value: 'DATETIMEOFFSET', label: 'DATETIMEOFFSET' },
    ],
    'Other': [
        { value: 'BIT', label: 'BIT' },
        { value: 'UNIQUEIDENTIFIER', label: 'UNIQUEIDENTIFIER' },
        { value: 'VARBINARY(MAX)', label: 'VARBINARY(MAX)' },
        { value: 'XML', label: 'XML' },
    ],
}

const ORACLE_TYPES = {
    'Numeric': [
        { value: 'NUMBER', label: 'NUMBER' },
        { value: 'NUMBER(10,2)', label: 'NUMBER(p,s)' },
        { value: 'FLOAT', label: 'FLOAT' },
        { value: 'BINARY_FLOAT', label: 'BINARY_FLOAT' },
        { value: 'BINARY_DOUBLE', label: 'BINARY_DOUBLE' },
    ],
    'String': [
        { value: 'VARCHAR2(255)', label: 'VARCHAR2' },
        { value: 'CHAR(255)', label: 'CHAR' },
        { value: 'NVARCHAR2(255)', label: 'NVARCHAR2' },
        { value: 'NCHAR(255)', label: 'NCHAR' },
        { value: 'CLOB', label: 'CLOB' },
        { value: 'NCLOB', label: 'NCLOB' },
    ],
    'Date & Time': [
        { value: 'DATE', label: 'DATE' },
        { value: 'TIMESTAMP', label: 'TIMESTAMP' },
        { value: 'TIMESTAMP WITH TIME ZONE', label: 'TIMESTAMP WITH TZ' },
        { value: 'INTERVAL YEAR TO MONTH', label: 'INTERVAL YEAR TO MONTH' },
        { value: 'INTERVAL DAY TO SECOND', label: 'INTERVAL DAY TO SECOND' },
    ],
    'Other': [
        { value: 'BLOB', label: 'BLOB' },
        { value: 'RAW(255)', label: 'RAW' },
        { value: 'XMLTYPE', label: 'XMLTYPE' },
    ],
}

const SQLITE_TYPES = {
    'Numeric': [
        { value: 'INTEGER', label: 'INTEGER' },
        { value: 'REAL', label: 'REAL' },
        { value: 'NUMERIC(10,2)', label: 'NUMERIC' },
    ],
    'String': [
        { value: 'TEXT', label: 'TEXT' },
        { value: 'BLOB', label: 'BLOB' },
    ],
    'Date & Time': [
        { value: 'TEXT', label: 'TEXT (ISO 8601)' },
        { value: 'NUMERIC', label: 'NUMERIC (epoch)' },
    ],
    'Other': [
        { value: 'INTEGER', label: 'BOOLEAN' },
    ],
}

const typeGroups = computed(() => {
    if (props.dbType === 'postgresql') return POSTGRESQL_TYPES
    if (props.dbType === 'sqlite') return SQLITE_TYPES
    if (props.dbType === 'oracle') return ORACLE_TYPES
    if (props.dbType === 'sqlserver') return SQLSERVER_TYPES
    if (props.dbType === 'msaccess') return MSACCESS_TYPES
    return MYSQL_TYPES
})

const isEnum = computed(() => /^ENUM\(/i.test(props.data.sqlType))

const isEnumEmpty = computed(() => {
    const m = props.data.sqlType.match(/^ENUM\((.*)\)$/i)
    if (!m) return false
    const re = /'([^']*)'|"([^"]*)"/g
    let match
    while ((match = re.exec(m[1])) !== null) {
        if ((match[1] ?? match[2]).trim() !== '') return false
    }
    return true
})

const showEnumModal = ref(false)
const enumBtnRef = ref(null)
const gearBtnRef = ref(null)

watch(isEnum, (val) => { if (!val) showEnumModal.value = false })

// Types that take a single integer length param, e.g. VARCHAR(32)
const LENGTH_TYPE_RE = /^(CHAR|VARCHAR|NCHAR|NVARCHAR|BINARY|VARBINARY|VARCHAR2|NVARCHAR2|TEXT|RAW)\((\d+)\)$/i

const isLengthType = computed(() => LENGTH_TYPE_RE.test(props.data.sqlType))

const typeLengthValue = computed(() => {
    const m = props.data.sqlType?.match(LENGTH_TYPE_RE)
    return m ? parseInt(m[2]) : 255
})

const onLengthChange = (e) => {
    const num = parseInt(e.target.value)
    if (!num || num < 1) { e.target.value = typeLengthValue.value; return }
    const m = props.data.sqlType?.match(LENGTH_TYPE_RE)
    if (!m) return
    props.data.sqlType = `${m[1].toUpperCase()}(${num})`
    emitChange()
}

const sqlTypeForSelect = computed({
    get() {
        if (isEnum.value) return "ENUM('')"
        const m = props.data.sqlType?.match(LENGTH_TYPE_RE)
        if (m) {
            const baseName = m[1].toUpperCase()
            for (const group of Object.values(typeGroups.value)) {
                for (const opt of group) {
                    if (new RegExp(`^${baseName}\\(`, 'i').test(opt.value)) return opt.value
                }
            }
        }
        return props.data.sqlType
    },
    set(val) {
        const newMatch = val?.match(LENGTH_TYPE_RE)
        const curMatch = props.data.sqlType?.match(LENGTH_TYPE_RE)
        // Same base type — preserve the user's custom length
        if (newMatch && curMatch && newMatch[1].toUpperCase() === curMatch[1].toUpperCase()) return
        props.data.sqlType = val
    }
})
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

.enum_values_btn {
    width: 18px;
    height: 18px;
    padding: 2px;
    color: var(--color-primary);
}

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

/* ── Constraint badges ───────────────────────────────────────── */
.constraint_badges {
    display: flex;
    gap: 3px;
    flex-shrink: 0;
    margin-right: 4px;
}

.constraint_badge {
    font-size:$11rem;
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
.badge--null        { background: #7c3aed; color: #fff; }

.type_cell {
    display: flex;
    align-items: center;
    gap: 2px;
}

.length_input {
    width: 38px;
    padding: 4px 6px;
    background-color: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 5px;
    color: var(--text-primary);
    font-size:$11rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.15s;
    -moz-appearance: textfield;
}

.length_input::-webkit-outer-spin-button,
.length_input::-webkit-inner-spin-button {
    -webkit-appearance: none;
}

.length_input:hover {
    border-color: var(--border-strong);
    background-color: var(--hover-bg-alt);
}

.length_input:focus {
    outline: none;
    border-color: var(--border-strong);
    cursor: text;
}

.length_input:disabled {
    opacity: 0.6;
    cursor: default;
}

.enum_empty_wrapper {
    position: relative;
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
    cursor: default;
}

.enum_empty_wrapper::after {
    content: 'ENUM must have at least one value';
    position: absolute;
    left: 50%;
    bottom: calc(100% + 6px);
    transform: translateX(-50%);
    background: var(--bg-surface);
    border: 1px solid var(--border-strong);
    color: var(--text-primary);
    font-size:$11rem;
    white-space: nowrap;
    padding: 4px 8px;
    border-radius: 4px;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.15s;
    z-index: 100;
}

.enum_empty_wrapper:hover::after {
    opacity: 1;
}
</style>
