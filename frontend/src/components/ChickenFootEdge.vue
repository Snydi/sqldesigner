<template>
    <svg>
        <defs>
            <!--God knows how this svg is constructed-->
            <marker id="chickenFoot" viewBox="0 0 20 20" refX="8" refY="5"
                    markerWidth="70" markerHeight="140" orient="auto-start-reverse">
                <path d="M0,0 L5,5 M5,5 L5,5 M5,5 L10,0 M5,5 L10,0" fill="none" stroke="context-stroke"
                      transform="rotate(90 5 5)" stroke-width="0.15" />
            </marker>
        </defs>
    </svg>

    <BaseEdge :id="id" :style="style" :path="path[0]"
              :marker-start="data?.markerStart || 'none'" :marker-end="data?.markerEnd || 'none'" />
</template>

<script setup>
import { BaseEdge, getSmoothStepPath } from '@vue-flow/core'
import { computed } from 'vue'

defineOptions({ inheritAttrs: false })

const props = defineProps({
    id: String,
    sourceX: Number,
    sourceY: Number,
    targetX: Number,
    targetY: Number,
    sourcePosition: String,
    targetPosition: String,
    data: Object,
    style: Object,
})

const path = computed(() => {
    const extend = -5
    return getSmoothStepPath({
        ...props,
        sourceX: props.sourcePosition === 'left' ? props.sourceX - extend : props.sourcePosition === 'right' ? props.sourceX + extend : props.sourceX,
        targetX: props.targetPosition === 'left' ? props.targetX - extend : props.targetPosition === 'right' ? props.targetX + extend : props.targetX,
    })
})
</script>
