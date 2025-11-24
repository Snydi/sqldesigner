<template>
    <svg>
        <defs>
            <!--God knows how this svg is constructed-->
            <marker id="chickenFoot" viewBox="0 0 20 20" refX="8" refY="5"
                    markerWidth="70" markerHeight="140"
                    orient="auto-start-reverse">
                <path d="M0,0 L5,5 M5,5 L5,5 M5,5 L10,0 M5,5 L10,0" fill="none" stroke="grey" transform="rotate(90 5 5)"
                      stroke-width="0.1"
                />
            </marker>
        </defs>
    </svg>

    <BaseEdge :id="id" :style="style" :path="path[0]"
              :marker-start="data.markerStart || 'none'" :marker-end="data.markerEnd || 'none'"/>
</template>

<script setup>
import { BaseEdge, getSmoothStepPath, useVueFlow } from '@vue-flow/core'
import { computed } from 'vue'

defineOptions({
    inheritAttrs: false
})
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    sourceX: {
        type: Number,
        required: true,
    },
    sourceY: {
        type: Number,
        required: true,
    },
    targetX: {
        type: Number,
        required: true,
    },
    targetY: {
        type: Number,
        required: true,
    },
    sourcePosition: {
        type: String,
        required: true,
    },
    targetPosition: {
        type: String,
        required: true,
    },
    data: {
        type: Object,
        required: false,
    },
    markerEnd: {
        type: String,
        required: false,
    },
    style: {
        type: Object,
        required: false,
    },
})
const path = computed(() => getSmoothStepPath(props))
</script>
