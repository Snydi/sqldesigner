<template>
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
            <option value="TINYINT(1)">TINYINT</option>
            <option value="BIGINT">BIGINT</option>
            <option value="CHAR(255)">CHAR</option>
            <option value="VARCHAR(255)">VARCHAR</option>
            <option value="TEXT">TEXT</option>
            <option value="DATE">DATE</option>
            <option value="DATETIME">DATETIME</option>
            <option value="TIME">TIME</option>
            <option value="TIMESTAMP">TIMESTAMP</option>
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
        <img class="table_icon" src="../icons/cancel.svg" alt="Cancel">
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

const emit = defineEmits(['update-label', 'toggle-options-modal', 'delete-node', 'change'])

const toggleNullable = () => {
    props.data.nullable = !props.data.nullable
    emit('change')
}

const toggleUnsigned = () => {
    props.data.unsigned = !props.data.unsigned
    emit('change')
}
</script>
