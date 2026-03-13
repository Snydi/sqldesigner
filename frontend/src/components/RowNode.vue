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
        @click="data.editing = true"
        @blur="() => { data.editing = false; $emit('update-label', id, label); }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing"
    />

    <!-- SQL Type -->
    <div>
        <select v-model="data.sqlType" @change="$emit('change')">
            <optgroup label="Numeric">
                <option value="TINYINT">TINYINT</option>
                <option value="SMALLINT">SMALLINT</option>
                <option value="MEDIUMINT">MEDIUMINT</option>
                <option value="INT">INT</option>
                <option value="BIGINT">BIGINT</option>
                <option value="DECIMAL(10,2)">DECIMAL</option>
                <option value="FLOAT">FLOAT</option>
                <option value="DOUBLE">DOUBLE</option>
                <option value="BIT">BIT</option>
            </optgroup>
            <optgroup label="String">
                <option value="CHAR(255)">CHAR</option>
                <option value="VARCHAR(255)">VARCHAR</option>
                <option value="TINYTEXT">TINYTEXT</option>
                <option value="TEXT">TEXT</option>
                <option value="MEDIUMTEXT">MEDIUMTEXT</option>
                <option value="LONGTEXT">LONGTEXT</option>
                <option value="BINARY(255)">BINARY</option>
                <option value="VARBINARY(255)">VARBINARY</option>
                <option value="TINYBLOB">TINYBLOB</option>
                <option value="BLOB">BLOB</option>
                <option value="MEDIUMBLOB">MEDIUMBLOB</option>
                <option value="LONGBLOB">LONGBLOB</option>
                <option value="ENUM('')">ENUM</option>
                <option value="SET('')">SET</option>
            </optgroup>
            <optgroup label="Date & Time">
                <option value="DATE">DATE</option>
                <option value="TIME">TIME</option>
                <option value="DATETIME">DATETIME</option>
                <option value="TIMESTAMP">TIMESTAMP</option>
                <option value="YEAR">YEAR</option>
            </optgroup>
            <optgroup label="Other">
                <option value="JSON">JSON</option>
                <option value="UUID">UUID</option>
                <option value="BOOLEAN">BOOLEAN</option>
            </optgroup>
            <option v-bind:value="data.sqlType">{{ data.sqlType }}</option>
        </select>
    </div>

    <!-- Options -->
    <button class="table_button" @mousedown.stop @click="$emit('toggle-options-modal', id)">
        <img class="table_icon" src="../icons/dots.svg" alt="More options">
    </button>

    <!-- Options modal -->
    <div v-if="data.showOptionsModal" class="options_modal"
         :style="{ left: `${data.modalPosition?.x}px`, top: `${data.modalPosition?.y}px` }">
        <select v-model="data.keyMod" @change="$emit('change')">
            <option selected="selected" value="None">None</option>
            <option value="PRIMARY KEY">Primary</option>
            <option value="UNIQUE">Unique</option>
            <option value="INDEX">Index</option>
        </select>
        <p class="modal_text">Unsigned</p>
        <input type="checkbox" @mousedown.stop :checked="data.unsigned" @change="toggleUnsigned">
        <p class="modal_text">Nullable</p>
        <input type="checkbox" @mousedown.stop :checked="data.nullable" @change="toggleNullable">
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
import { Handle } from '@vue-flow/core'

const props = defineProps({
    id: String,
    data: Object,
    label: String,
})

const emit = defineEmits(['update-label', 'toggle-options-modal', 'delete-node', 'change', 'row-drag-start'])

const toggleNullable = () => {
    props.data.nullable = !props.data.nullable
    emit('change')
}

const toggleUnsigned = () => {
    props.data.unsigned = !props.data.unsigned
    emit('change')
}
</script>
