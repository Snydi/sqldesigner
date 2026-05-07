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
    <RowOptionsModal
        v-if="data.showOptionsModal"
        :data="data"
        :dbType="dbType"
        :label="label"
        :tableColumns="tableColumns"
        :tableUniqueTogether="tableUniqueTogether"
        :tableFulltextIndexes="tableFulltextIndexes"
        @change="emitChange()"
        @close="$emit('toggle-options-modal', id)"
        @update-table-constraints="$emit('update-table-constraints', $event)"
        @update-table-fulltext="$emit('update-table-fulltext', $event)"
    />

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
import { computed } from 'vue'
import { Handle } from '@vue-flow/core'
import SvgIcon from './SvgIcon.vue'
import RowOptionsModal from './RowOptionsModal.vue'

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
</style>
