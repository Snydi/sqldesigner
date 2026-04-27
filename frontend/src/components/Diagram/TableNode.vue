<template>
    <input
        class="input input_designer_table"
        :value="label"
        @mousedown.stop
        @click="canEdit && (data.editing = true)"
        @blur="(e) => { data.editing = false; $emit('update-label', id, label); e.target.scrollLeft = 0; }"
        @input="$emit('update-label', id, $event.target.value)"
        :readonly="!data.editing || !canEdit"
    />

    <label v-if="canEdit" class="table_color_picker" @mousedown.stop>
        <input
            type="color"
            :value="data.color || '#898989'"
            @input="$emit('update-color', id, $event.target.value)"
            class="table_color_input"
        />
    </label>

    <button v-if="canEdit" class="table_button table_button--add-row" @mousedown.stop @click="$emit('add-row', id)">
        <SvgIcon name="plus" :size="14" />
    </button>

    <button v-if="canEdit" class="table_button table_button--copy" @mousedown.stop @click="$emit('copy-table', id)">
        <SvgIcon name="copy" :size="13" />
    </button>

    <button v-if="canEdit" class="table_button" @mousedown.stop @click="$emit('delete-node', id)">
        <SvgIcon name="trash" :size="13" />
    </button>

    <template v-if="canEdit">
        <div class="table_resize_handle table_resize_handle--left" @mousedown.stop="$emit('resize-start', id, $event, 'left')"></div>
        <div class="table_resize_handle table_resize_handle--right" @mousedown.stop="$emit('resize-start', id, $event, 'right')"></div>
    </template>
</template>

<script setup>
import SvgIcon from '../SvgIcon.vue'

defineProps({
    id: String,
    data: Object,
    label: String,
    canEdit: { type: Boolean, default: true },
})

defineEmits(['delete-node', 'update-label', 'copy-table', 'add-row', 'resize-start', 'update-color'])
</script>

<style scoped>
.input_designer_table {
    text-align: center;
    color: white;
    flex-grow: 0;
    width: 30%;
}
</style>
