<template>
    <!-- Drag handle -->
    <img
        class="drag_handle_icon"
        src="../icons/drag.svg"
        alt="Drag to reorder"
        @mousedown.stop.prevent="$emit('row-drag-start', id)"
    />

    <input
        class="input input_designer_row ml-5 mr-5"
        :value="label"
        @mousedown.stop
        @click="data.editing = true"
        @blur="(e) => { data.editing = false; $emit('update-label', id, label); e.target.scrollLeft = 0; }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing"
    />

    <!-- SQL Type -->
    <div>
        <select v-model="data.sqlType" @change="emitChange()">
            <optgroup v-for="(options, groupLabel) in typeGroups" :key="groupLabel" :label="groupLabel">
                <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </optgroup>
            <option :value="data.sqlType">{{ data.sqlType }}</option>
        </select>
    </div>

    <!-- Options -->
    <button class="table_button" @mousedown.stop @click="$emit('toggle-options-modal', id)">
        <img class="table_icon" src="../icons/gear.svg" alt="More options">
    </button>

    <!-- Options modal -->
    <div v-if="data.showOptionsModal" class="options_modal"
         :style="{ left: `${data.modalPosition?.x}px`, top: `${data.modalPosition?.y}px` }"
         @mousedown.stop
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
    </div>

    <!-- Delete row -->
    <button class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <img class="table_icon" src="../icons/trash.svg" alt="Delete">
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

const props = defineProps({
    id: String,
    data: Object,
    label: String,
    dbType: { type: String, default: 'mysql' },
})

const emit = defineEmits(['update-label', 'toggle-options-modal', 'delete-node', 'change', 'row-drag-start'])

const optionsModalRef = ref(null)
onClickOutside(optionsModalRef, () => emit('toggle-options-modal', props.id))

const emitChange = () => emit('change', props.id)

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

const toggleNullable = () => {
    props.data.nullable = !props.data.nullable
    emitChange()
}

const toggleUnsigned = () => {
    props.data.unsigned = !props.data.unsigned
    emitChange()
}
</script>
